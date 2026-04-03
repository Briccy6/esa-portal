# Flutterwave Payment Gateway Implementation Summary

## Overview
Flutterwave is integrated into the ESA application as the primary payment gateway for:
- Student subscriptions
- Token purchases  
- Live class payments
- Resource/Exam payments for public users

---

## 1. Configuration & Environment Variables

### Configuration Files:

**[config/flutterwave.php](config/flutterwave.php)**
```php
return [
    'secret_key' => env('FLW_SECRET_KEY'),
    'public_key' => env('FLW_PUBLIC_KEY'),
    'encryption_key' => env('FLW_ENCRYPTION_KEY'),
    'webhook_hash' => env('FLUTTERWAVE_WEBHOOK_HASH'),
    'redirect_url' => env('APP_URL') . '/flutterwave/callback',
    'logo_url' => env('APP_URL') . '/images/logo.png',
];
```

**[config/services.php](config/services.php) - Lines 49-53**
```php
'flutterwave' => [
    'secret_key' => env('FLW_SECRET_KEY'),
    'public_key' => env('FLW_PUBLIC_KEY'),
    'encryption_key' => env('FLW_ENCRYPTION_KEY'),
    'webhook_hash' => env('FLW_WEBHOOK_HASH'),
    'redirect_url' => env('APP_URL') . '/payment/callback',
],
```

### Required Environment Variables:
- `FLW_SECRET_KEY` - Flutterwave API secret key (production/sandbox)
- `FLW_PUBLIC_KEY` - Flutterwave public key
- `FLW_ENCRYPTION_KEY` - Encryption key for communication
- `FLUTTERWAVE_WEBHOOK_HASH` / `FLW_WEBHOOK_HASH` - Webhook verification hash

---

## 2. Service Provider & Initialization

**[app/Providers/FlutterwaveServiceProvider.php](app/Providers/FlutterwaveServiceProvider.php)**

```php
class FlutterwaveServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Only configure Flutterwave if all required keys are present
        if (config('services.flutterwave.secret_key') && config('services.flutterwave.public_key')) {
            try {
                $config = ForkConfig::setUp(
                    config('services.flutterwave.secret_key'),
                    config('services.flutterwave.public_key'),
                    config('services.flutterwave.encryption_key'),
                    'staging' // Use staging environment
                );
                
                Flutterwave::bootstrap($config);
            } catch (\Exception $e) {
                \Log::warning('Flutterwave configuration failed: ' . $e->getMessage());
            }
        }
    }
}
```

**Status**: Currently **DISABLED** in [bootstrap/providers.php](bootstrap/providers.php) - Flutterwave is handled manually in controllers instead of using the service provider.

---

## 3. Main Payment Controller

**[app/Http/Controllers/FlutterwavePaymentController.php](app/Http/Controllers/FlutterwavePaymentController.php)**

### Key Methods:

#### A. Initialize Subscription Payment
```php
public function initializeSubscriptionPayment(Request $request)
```
- Validates subscription ID
- Creates pending StudentSubscription record
- Generates unique tx_ref: `SUB-{student_id}-{subscription_id}-{timestamp}`
- Calls Flutterwave API with Guzzle HTTP client (not SDK)
- Returns payment link for redirect

**Payment Data Structure:**
```php
[
    'tx_ref' => $tx_ref,
    'amount' => $subscription->price,
    'currency' => 'RWF',
    'payment_options' => 'card,bank_transfer,ussd,mobile_money',
    'redirect_url' => route('flutterwave.payment.callback'),
    'customer' => [
        'email' => $student->email,
        'name' => $student->full_name,
        'phone' => $student->phone,
    ],
    'meta' => [
        'student_id' => $student->id,
        'subscription_id' => $subscription->id,
        'student_subscription_id' => $studentSubscription->id,
        'tokens_included' => $subscription->tokens_included,
        'type' => 'subscription'
    ],
]
```

#### B. Initialize Token Purchase Payment
```php
public function initializeTokenPayment(Request $request)
```
- Validates token amount (1-1000)
- Calculates bulk discounts (5% for 50+, 10% for 100+)
- Base price: 500 RWF per token
- Generates tx_ref: `TKN-{student_id}-{timestamp}`
- Creates pending StudentTransaction record

#### C. Handle Payment Callback
```php
public function handlePaymentCallback(Request $request)
```
- Receives transaction_id from Flutterwave redirect
- Verifies transaction via Flutterwave API `/v3/transactions/{tx_ref}/verify`
- Dispatches to processSubscriptionPayment() or processTokenPayment() based on type
- Updates StudentSubscription/StudentTransaction status
- Initializes or adds tokens to student account
- Redirects to student dashboard with success/error message

#### D. Process Subscription Payment (Private)
```php
private function processSubscriptionPayment($txData, $meta)
```
- Updates StudentSubscription to 'paid' status
- Sets start_date to now(), end_date based on duration_days
- Creates StudentTransaction record with type 'subscription'
- Initializes tokens for student if needed
- Adds subscription tokens to student token balance

#### E. Process Token Payment (Private)
```php
private function processTokenPayment($txData, $meta)
```
- Updates StudentTransaction to 'successful' status
- Adds flutterwave_transaction_id to metadata
- Adds purchased tokens to student token balance

---

## 4. Live Class Payment Controller

**[app/Http/Controllers/LiveClassController.php](app/Http/Controllers/LiveClassController.php) - Lines 280-470**

### Payment Flow:

1. **Create Payment Record** (initializePayment method):
   - Creates LiveClassPayment record with 'pending' status
   - Stores payment_gateway as 'flutterwave'

2. **Call Flutterwave Hosted Payment**:
   - Uses REST API directly: `POST /v3/payments`
   - Maps payment method to payment_options based on currency
   - Currency-specific payment options:
     - RWF: mobile_money (MTN, AIRTEL, TIGO)
     - GHS: mobile money or card
     - UGX, XAF, ZMW, TZS, KES all have region-specific networks

3. **Handle Payment Callback** (handlePaymentCallback method):
   - Receives tx_ref and transaction_id
   - Verifies transaction status
   - Updates LiveClassPayment record (completed/failed)
   - Stores gateway_response JSON

### Payment Method Mapping:
```php
if ($selectedMethod === 'mobile_money') {
    $paymentOptions = 'mobilemoney';
} elseif ($selectedMethod === 'card') {
    // RWF does not support card; silently fall back to mobile money
    $paymentOptions = in_array($currency, $mobileMoneyCurrencies) ? 'mobilemoney' : 'card';
} elseif ($selectedMethod === 'bank_transfer') {
    $paymentOptions = 'banktransfer';
} elseif ($selectedMethod === 'ussd') {
    $paymentOptions = 'ussd';
}
```

---

## 5. Public Resource Payment

**[app/Http/Controllers/PublicResourceController.php](app/Http/Controllers/PublicResourceController.php) - Lines 230-270**

- Handles payment for public resources (documents, PDFs)
- Stores Flutterwave transaction ID in session: `session('resource_payment')['flutterwave_transaction_id']`
- Verifies transaction before granting access
- Redirects to payment success page

---

## 6. Public Exam Payment

**[app/Http/Controllers/PublicExamController.php](app/Http/Controllers/PublicExamController.php) - Lines 160-194**

- Processes payments for public exam access
- Stores Flutterwave link/transaction ID in session
- Similar pattern to resource payments

---

## 7. Payment Models

### LiveClassPayment Model
**[app/Models/LiveClassPayment.php](app/Models/LiveClassPayment.php)**

```php
protected $fillable = [
    'live_class_id',
    'user_type', // 'public'
    'email',
    'name',
    'phone',
    'amount',
    'currency',
    'payment_method', // 'card', 'mobile_money', 'bank_transfer'
    'transaction_id',
    'status', // 'pending', 'completed', 'failed', 'refunded'
    'payment_gateway', // 'flutterwave'
    'gateway_response', // JSON
    'paid_at',
    'refunded_at',
    'refund_reason'
];

protected $casts = [
    'gateway_response' => 'json',
    'paid_at' => 'datetime',
    'refunded_at' => 'datetime',
];
```

### ClientSubscription Model
**[app/Models/ClientSubscription.php](app/Models/ClientSubscription.php)**

```php
protected $fillable = [
    'payment_gateway', // 'flutterwave'
    'gateway_response', // JSON
    'payment_status', // 'pending', 'paid'
    'payment_reference',
    'paid_amount',
    'starts_at',
    'ends_at',
    'is_active',
];
```

---

## 8. Routes

**[routes/web.php](routes/web.php)**

### Student Payment Routes (Lines 1379-1384):
```php
Route::middleware('auth:student')->prefix('flutterwave')->name('flutterwave.')->group(function () {
    Route::post('/payment/initialize-subscription', 
        [FlutterwavePaymentController::class, 'initializeSubscriptionPayment'])
        ->name('payment.initialize-subscription');
    Route::post('/payment/initialize-tokens', 
        [FlutterwavePaymentController::class, 'initializeTokenPayment'])
        ->name('payment.initialize-tokens');
    Route::get('/payment/callback', 
        [FlutterwavePaymentController::class, 'handlePaymentCallback'])
        ->name('payment.callback');
    Route::post('/webhook', 
        [FlutterwavePaymentController::class, 'handleWebhook'])
        ->name('webhook');
});
```

### Live Class Payment Routes:
```php
Route::get('/live-classes/{liveClass}/payment', 
    [LiveClassController::class, 'showPaymentForm'])
    ->name('live-classes.payment');
Route::post('/live-classes/{liveClass}/payment', 
    [LiveClassController::class, 'processPayment'])
    ->name('live-classes.payment.process');
Route::get('/live-classes/{liveClass}/payment/callback', 
    [LiveClassController::class, 'handlePaymentCallback'])
    ->name('live-classes.payment.callback');
Route::post('/webhooks/flutterwave/live-classes', 
    [LiveClassController::class, 'handleWebhook'])
    ->name('webhooks.flutterwave.live-classes');
```

### Legacy Routes (Lines 85-88):
```php
Route::post('/payment/process', [FlutterwaveController::class, 'addPayment'])
    ->name('payment.process');
Route::get('/payment/callback', [FlutterwaveController::class, 'handleCallback'])
    ->name('payment.callback');
Route::get('/payment/success', [FlutterwaveController::class, 'paymentSuccess'])
    ->name('payment.success');
Route::get('/payment/failed', [FlutterwaveController::class, 'paymentFailed'])
    ->name('payment.failed');
```

---

## 9. Payment Views/Forms

**[resources/views/client/subscriptions/purchase.blade.php](resources/views/client/subscriptions/purchase.blade.php) - Lines 470+**

- **Payment Info Box** (line 489):
  ```html
  You will be redirected to Flutterwave secure payment gateway to complete your purchase.
  ```

- **Purchase Button**: Triggers payment initialization via AJAX/form submission
- **Plan Summary**: Shows subscription name, price in RWF, duration, tokens included
- **Features List**: Lists subscription benefits (exams, progress tracking, certificates, support)

---

## 10. API Integration Details

### HTTP Client Usage (Guzzle):
```php
$client = new \GuzzleHttp\Client();

$response = $client->post('https://api.flutterwave.com/v3/payments', [
    'headers' => [
        'Authorization' => 'Bearer ' . config('services.flutterwave.secret_key'),
        'Content-Type' => 'application/json',
    ],
    'json' => $paymentData,
    'timeout' => 30,
    'verify' => false, // Disable SSL for testing
]);

$body = $response->getBody()->getContents();
$payment = json_decode($body, true);
```

### Transaction Verification:
```php
$response = $client->get("https://api.flutterwave.com/v3/transactions/{$tx_ref}/verify", [
    'headers' => [
        'Authorization' => 'Bearer ' . config('services.flutterwave.secret_key'),
        'Content-Type' => 'application/json',
    ],
    'verify' => false,
]);
```

### Response Handling:
- **Success**: `$payment['status'] === 'success'` and `$payment['data']['link']` contains redirect URL
- **Verification**: Checks if `$transaction['data']['status'] === 'successful'`
- **Meta Data**: Passed in `meta` field for transaction tracking

---

## 11. Database Migrations

**[database/migrations/2026_03_06_add_missing_columns_to_live_class_payments_table.php](database/migrations/2026_03_06_add_missing_columns_to_live_class_payments_table.php)**

Adds payment_gateway and gateway_response columns:
```php
$table->string('payment_gateway')->nullable()->after('status');
$table->json('gateway_response')->nullable()->after('payment_gateway');
```

**[database/migrations/2026_03_08_090921_create_client_subscriptions_table.php](database/migrations/2026_03_08_090921_create_client_subscriptions_table.php)**

Includes payment_gateway column for client subscriptions.

---

## 12. Supported Currencies & Payment Methods

### Currencies:
- **RWF** (Rwandan Franc) - Primary
- **GHS** (Ghanaian Cedi)
- **UGX** (Ugandan Shilling)  
- **XAF** (Central African Franc)
- **ZMW** (Zambian Kwacha)
- **TZS** (Tanzanian Shilling)
- **KES** (Kenyan Shilling)

### Payment Options by Currency:
- **Mobile Money** Networks:
  - RWF: MTN, AIRTEL, TIGO
  - GHS: MTN, VODAFONE, TIGO
  - UGX: MTN, AIRTEL
  - XAF: MTN, ORANGE, VODAFONE
  - ZMW: MTN, ZAMTEL
  - TZS: AIRTEL, TIGO, HALOPESA, VODAFONE
  - KES: MPESA

- **Other Methods**: Card, Bank Transfer, USSD

---

## 13. Key Features & Implementation Notes

1. **Direct API Usage**: Uses Guzzle HTTP client instead of Flutterwave SDK in controllers
2. **SSL Verification**: Disabled in development (`'verify' => false`)
3. **Transaction Metadata**: Stores detailed meta data including student ID, subscription details, token counts
4. **Staging Environment**: Configured for 'staging' mode
5. **Error Handling**: Comprehensive try-catch blocks with logging
6. **Automatic Rollback**: Deletes pending subscriptions if payment initialization fails
7. **Session Storage**: Uses Laravel sessions to temporarily store payment info
8. **Token Integration**: Automatically initializes and adds tokens post-payment
9. **Database Transactions**: Wraps multi-step payment processing in DB transactions

---

## 14. Transaction References Format

- **Subscriptions**: `SUB-{student_id}-{subscription_id}-{timestamp}`
- **Tokens**: `TKN-{student_id}-{timestamp}`
- **Live Classes**: `LC_{random_12chars}_{timestamp}`

---

## 15. Known Settings

- **Environment**: Staging (can be changed in FlutterwaveServiceProvider)
- **Currency Default**: RWF
- **Base Token Price**: 500 RWF per token
- **Bulk Discounts**: 
  - 5% for 50+ tokens
  - 10% for 100+ tokens

---

## 16. Potential Issues & Notes

- ✅ Service provider is disabled - payments work via controller implementation
- ⚠️ SSL verification disabled (should be enabled in production)
- ⚠️ Webhook handling method exists but implementation not fully shown in grep results
- ✅ Multiple payment types supported (subscription, token, live class, resource, exam)
- ✅ Proper error logging and user feedback implemented

---

## Files Summary

| File | Purpose |
|------|---------|
| [config/flutterwave.php](config/flutterwave.php) | Flutterwave config |
| [config/services.php](config/services.php) | Services config |
| [app/Providers/FlutterwaveServiceProvider.php](app/Providers/FlutterwaveServiceProvider.php) | Service provider (disabled) |
| [app/Http/Controllers/FlutterwavePaymentController.php](app/Http/Controllers/FlutterwavePaymentController.php) | Main payment controller |
| [app/Http/Controllers/LiveClassController.php](app/Http/Controllers/LiveClassController.php) | Live class payments |
| [app/Http/Controllers/PublicResourceController.php](app/Http/Controllers/PublicResourceController.php) | Resource payments |
| [app/Http/Controllers/PublicExamController.php](app/Http/Controllers/PublicExamController.php) | Exam payments |
| [app/Models/LiveClassPayment.php](app/Models/LiveClassPayment.php) | LiveClassPayment model |
| [app/Models/ClientSubscription.php](app/Models/ClientSubscription.php) | ClientSubscription model |
| [routes/web.php](routes/web.php) | Payment routes |
| [resources/views/client/subscriptions/purchase.blade.php](resources/views/client/subscriptions/purchase.blade.php) | Purchase form |

