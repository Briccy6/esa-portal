<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiAssistantService
{
    public function __construct(private AcademyKnowledgeBuilder $knowledgeBuilder)
    {
    }

    public function generateResponse(User $user, string $message, array $chatHistory = []): string
    {
        return $this->generateForAuthenticatedAccount($user, 'web', $message, $chatHistory);
    }

    public function generateForAuthenticatedAccount(
        Authenticatable $user,
        string $guard,
        string $message,
        array $chatHistory = []
    ): string {
        $apiKey = config('services.gemini.api_key');
        if (!is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('The Gemini API key is not configured.');
        }

        $model = config('services.gemini.model', 'gemini-flash-latest');
        $endpoint = rtrim((string) config(
            'services.gemini.endpoint',
            'https://generativelanguage.googleapis.com/v1beta/models'
        ), '/');
        $role = $this->knowledgeBuilder->roleForAuthenticatedAccount($user, $guard);

        try {
            $response = Http::connectTimeout(5)
                ->timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $apiKey,
                ])
                ->post("{$endpoint}/{$model}:generateContent", [
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $this->knowledgeBuilder->buildForAuthenticatedAccount($user, $guard)],
                        ],
                    ],
                    'contents' => $this->buildContents($message, $chatHistory),
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'maxOutputTokens' => 1200,
                    ],
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Gemini academy assistant connection failed.', [
                'guard' => $guard,
                'role' => $role,
            ]);

            throw new RuntimeException('The AI support service is temporarily unavailable.', 0, $exception);
        }

        if (!$response->successful()) {
            Log::warning('Gemini academy assistant request failed.', [
                'status' => $response->status(),
                'guard' => $guard,
                'role' => $role,
            ]);

            throw new RuntimeException('The AI support service is temporarily unavailable.');
        }

        $answer = $response->json('candidates.0.content.parts.0.text');
        if (!is_string($answer) || trim($answer) === '') {
            Log::warning('Gemini academy assistant returned no text.', [
                'guard' => $guard,
                'role' => $role,
            ]);

            throw new RuntimeException('The AI support service returned an empty response.');
        }

        return trim($answer);
    }

    private function buildContents(string $message, array $chatHistory): array
    {
        $contents = [];

        foreach (array_slice($chatHistory, -10) as $entry) {
            if (
                !is_array($entry)
                || !in_array($entry['role'] ?? null, ['user', 'model'], true)
                || !is_string($entry['text'] ?? null)
                || trim($entry['text']) === ''
            ) {
                continue;
            }

            $contents[] = [
                'role' => $entry['role'],
                'parts' => [['text' => mb_substr($entry['text'], 0, 2000)]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]],
        ];

        return $contents;
    }
}
