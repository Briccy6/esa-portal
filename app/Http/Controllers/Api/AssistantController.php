<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiAssistantService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class AssistantController extends Controller
{
    public function chat(Request $request, GeminiAssistantService $assistant): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'chat_history' => ['sometimes', 'array', 'max:10'],
            'chat_history.*.role' => ['required', 'in:user,model'],
            'chat_history.*.text' => ['required', 'string', 'max:2000'],
        ]);

        $guard = Auth::getDefaultDriver();
        $user = Auth::guard($guard)->user();

        if (!($user instanceof Authenticatable)) {
            abort(401, 'Unauthenticated.');
        }

        try {
            $reply = $assistant->generateForAuthenticatedAccount(
                $user,
                $guard,
                $validated['message'],
                $validated['chat_history'] ?? []
            );
        } catch (RuntimeException) {
            return response()->json([
                'message' => 'AI support is temporarily unavailable. Please try again later.',
            ], 503);
        }

        return response()->json(['reply' => $reply]);
    }
}
