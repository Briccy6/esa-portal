<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicResourceController extends Controller
{
    private $flutterwavePublicKey;
    private $flutterwaveSecretKey;
    private $flutterwaveBaseUrl;

    public function __construct()
    {
        $this->flutterwavePublicKey = config('services.flutterwave.public_key');
        $this->flutterwaveSecretKey = config('services.flutterwave.secret_key');
        $this->flutterwaveBaseUrl = 'https://api.flutterwave.com/v3';
    }

    public function index(Request $request)
    {
        $query = Resource::with(['teacher', 'course', 'branch', 'lesson'])
            ->where('is_public', true)
            ->where(function($q) {
                $q->where('is_free', true)
                  ->orWhereNotNull('price');
            })
            ->where(function($q) {
                $q->whereNotNull('document_blob')
                  ->orWhereNotNull('video_blob')
                  ->orWhereNotNull('youtube_link');
            });

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('document_name', 'like', "%{$searchTerm}%")
                  ->orWhere('video_name', 'like', "%{$searchTerm}%")
                  ->orWhereHas('course', function($courseQuery) use ($searchTerm) {
                      $courseQuery->where('course_name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('lesson', function($lessonQuery) use ($searchTerm) {
                      $lessonQuery->where('title', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('teacher', function($teacherQuery) use ($searchTerm) {
                      $teacherQuery->where('teacher_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('price_type')) {
            if ($request->price_type === 'free') {
                $query->where('is_free', true);
            } elseif ($request->price_type === 'paid') {
                $query->where('is_free', false);
            }
        }

        $resources = $query->latest()->paginate(12);

        $courses = \App\Models\Course::select('id', 'course_name')
            ->whereHas('resources', function($q) {
                $q->where('is_public', true);
            })
            ->orderBy('course_name')
            ->get();

        return view('public.resources.index', compact('resources', 'courses'));
    }

    public function show($id)
    {
        try {
            $resource = Resource::with(['teacher', 'course', 'branch', 'lesson'])
                ->where('is_public', true)
                ->findOrFail($id);

            return view('public.resources.show', compact('resource'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('public.resources.index')
                ->with('error', 'This resource is not available for public access or does not exist.');
        }
    }

    public function downloadDocument($id)
    {
        try {
            $resource = Resource::where('is_public', true)->findOrFail($id);

            if (!$resource->document_blob) {
                return back()->with('error', 'Document not available for download');
            }

            if (!$resource->is_free && $resource->price > 0) {
                return redirect()->route('public.resources.payment', $resource)
                    ->with('error', 'This is a paid resource. Please complete the payment to download.');
            }

            return response($resource->document_blob)
                ->header('Content-Type', $resource->document_mime)
                ->header('Content-Disposition', 'attachment; filename="'.$resource->document_name.'"');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('public.resources.index')
                ->with('error', 'This resource is not available for public access or does not exist.');
        }
    }

    public function previewDocument($id)
    {
        try {
            $resource = Resource::where('is_public', true)->findOrFail($id);

            if (!$resource->document_blob) {
                return back()->with('error', 'Document not available for preview');
            }

            if (!$resource->is_free && $resource->price > 0) {
                return redirect()->route('public.resources.payment', $resource)
                    ->with('error', 'This is a paid resource. Please complete the payment to preview.');
            }

            return response($resource->document_blob)
                ->header('Content-Type', $resource->document_mime)
                ->header('Content-Disposition', 'inline; filename="'.$resource->document_name.'"');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('public.resources.index')
                ->with('error', 'This resource is not available for public access or does not exist.');
        }
    }

    /**
     * Show payment page for resource access
     */
    public function showPayment($id)
    {
        try {
            $resource = Resource::where('is_public', true)->findOrFail($id);

            if ($resource->is_free || $resource->price <= 0) {
                return redirect()->route('public.resources.show', $resource)
                    ->with('error', 'This resource is free to access');
            }

            $flutterwavePublicKey = $this->flutterwavePublicKey;
            $resourceName         = $resource->document_name ?? $resource->video_name ?? $resource->title ?? 'Resource';
            $systemName           = config('app.name', 'ESA E-Learning');

            return view('public.resources.payment', compact('resource', 'flutterwavePublicKey', 'resourceName', 'systemName'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('public.resources.index')
                ->with('error', 'This resource is not available for public access or does not exist.');
        }
    }

    /**
     * Process payment for resource access using Flutterwave
     */
    public function processPayment(Request $request, $id)
    {
        try {
            $resource = Resource::where('is_public', true)->findOrFail($id);

            $request->validate([
                'email'          => 'required|email',
                'phone_number'   => 'required|string',
                'payment_method' => 'required|in:card,mobile_money,bank_transfer',
                'name'           => 'required|string|max:255',
                'tx_ref'         => 'required|string',
            ]);

            if ($resource->is_free || $resource->price <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'This resource is free to access'
                ]);
            }

            // Store payment session data for callback handling
            session([
                'resource_payment' => [
                    'resource_id'      => $resource->id,
                    'payment_reference'=> $request->tx_ref,
                    'customer_email'   => $request->email,
                    'customer_phone'   => $request->phone_number,
                    'customer_name'    => $request->name,
                    'payment_amount'   => $resource->price,
                    'payment_status'   => 'pending',
                    'payment_method'   => $request->payment_method,
                ],
            ]);

            // Initialize Flutterwave payment on server side
            $paymentData = [
                'tx_ref'          => $request->tx_ref,
                'amount'          => $resource->price,
                'currency'        => 'RWF',
                'payment_options' => $request->payment_method === 'mobile_money' ? 'mobilemoneyrwanda' : $request->payment_method,
                'redirect_url'    => route('public.resources.payment.callback', $resource),
                'customer'        => [
                    'email'        => $request->email,
                    'name'         => $request->name,
                    'phone_number' => $request->phone_number,
                ],
                'customizations' => [
                    'title'       => 'Resource Access: ' . ($resource->document_name ?: $resource->video_name ?: 'Resource'),
                    'description' => 'Payment for resource: ' . ($resource->document_name ?: $resource->video_name ?: 'Resource'),
                    'logo'        => url('/images/esa.png')
                ],
                'meta' => [
                    'resource_id'   => $resource->id,
                    'resource_type' => $resource->document_blob ? 'document' : 'video',
                    'payment_type'  => 'resource_access',
                ],
            ];

            $response = $this->initializeFlutterwavePayment($paymentData);

            if (!$response || !isset($response['status']) || $response['status'] !== 'success') {
                $errorMessage = 'Payment initialization failed';
                if (isset($response['message'])) {
                    $errorMessage .= ': ' . $response['message'];
                } elseif (isset($response['error'])) {
                    $errorMessage .= ': ' . $response['error'];
                }

                return response()->json([
                    'status'  => false,
                    'message' => $errorMessage,
                ]);
            }

            return response()->json([
                'status'       => true,
                'message'      => 'Payment initialized successfully',
                'payment_link' => $response['data']['link'] ?? null,
                'tx_ref'       => $request->tx_ref,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'This resource is not available for public access or does not exist.',
            ]);
        } catch (\Exception $e) {
            Log::error('Resource payment initialization failed: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle Flutterwave payment callback for resources
     */
    public function handlePaymentCallback(Request $request, $id)
    {
        try {
            $resource = Resource::where('is_public', true)->findOrFail($id);

            $txRef         = $request->query('tx_ref');
            $transactionId = $request->query('transaction_id');
            $status        = $request->query('status');

            if ($status === 'successful' || $status === 'completed') {
                $verification = $this->verifyFlutterwaveTransaction($transactionId);

                if ($verification['status'] === 'successful') {
                    session([
                        'resource_payment' => array_merge(session('resource_payment', []), [
                            'payment_status'             => 'paid',
                            'flutterwave_transaction_id' => $transactionId,
                            'paid_at'                    => now(),
                        ]),
                    ]);

                    return redirect()->route('public.resources.payment.success', [$resource, 'reference' => $txRef])
                        ->with('success', 'Payment successful! You can now access this resource.');
                }
            }

            return redirect()->route('public.resources.show', $resource)
                ->with('error', 'Payment failed or was cancelled. Please try again.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('public.resources.index')
                ->with('error', 'This resource is not available for public access or does not exist.');
        }
    }

    /**
     * Show payment success page for resources
     */
    public function paymentSuccess($id, Request $request)
    {
        try {
            $resource = Resource::where('is_public', true)->findOrFail($id);
            $reference = $request->query('reference');

            return view('public.resources.payment-success', compact('resource', 'reference'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('public.resources.index')
                ->with('error', 'This resource is not available for public access or does not exist.');
        }
    }

    /**
     * Initialize Flutterwave payment
     */
    private function initializeFlutterwavePayment($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api.flutterwave.com/v3/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->flutterwaveSecretKey,
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            Log::error('Flutterwave cURL Error: ' . $err);
            throw new \Exception('cURL Error: ' . $err);
        }

        Log::info('Flutterwave Response HTTP Code: ' . $httpCode);
        Log::info('Flutterwave Response: ' . $response);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Flutterwave JSON Decode Error: ' . json_last_error_msg());
            throw new \Exception('Invalid JSON response from Flutterwave');
        }

        return $decodedResponse;
    }

    /**
     * Verify Flutterwave transaction
     */
    private function verifyFlutterwaveTransaction($transactionId)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->flutterwaveBaseUrl . '/transactions/' . $transactionId . '/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->flutterwaveSecretKey,
            ],
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception('cURL Error: ' . $err);
        }

        return json_decode($response, true);
    }
}
