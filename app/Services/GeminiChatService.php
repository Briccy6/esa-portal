<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-flash-latest');
        $this->endpoint = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models');
    }

    /**
     * Generate a chat response using Gemini AI
     *
     * @param string $userMessage The user's message
     * @param array $conversationHistory Previous messages for context
     * @param string $language The language to respond in (en, rw, etc.)
     * @param array $userInfo Information about the user for context
     * @return array Response with status and message
     */
    public function generateResponse(string $userMessage, array $conversationHistory = [], string $language = 'en', array $userInfo = []): array
    {
        try {
            // Build the system prompt
            $systemPrompt = $this->buildSystemPrompt($language, $userInfo);

            // Build conversation context
            $contents = $this->buildConversationContents($systemPrompt, $userMessage, $conversationHistory);

            // Make API request
            $url = "{$this->endpoint}/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => env('VERIFY_SSL', true),
                ])
                ->post($url, [
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048,
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => $this->getFallbackMessage($language),
                    'error' => 'API request failed',
                ];
            }

            $data = $response->json();
            $aiMessage = $data['candidates'][0]['content']['parts'][0]['text'] ?? $this->getFallbackMessage($language);

            return [
                'success' => true,
                'message' => trim($aiMessage),
                'language' => $language,
            ];

        } catch (\Exception $e) {
            Log::error('Gemini chat service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $this->getFallbackMessage($language),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build the system prompt based on language and user info
     */
    protected function buildSystemPrompt(string $language, array $userInfo): string
    {
        $basePrompt = "You are ESA AI Assistant, a helpful support assistant for Excellent Servant Academy. ";

        if ($language === 'rw' || str_contains(strtolower($userMessage ?? ''), 'kinyarwanda')) {
            $basePrompt .= "Murakoze kubaza ibibazo. Ndi ESA AI Assistant, umufasha w'ikoranabuhanga wa Excellent Servant Academy. ";
        }

        $basePrompt .= "\n\nYour role is to help students, teachers, and staff with:\n";
        $basePrompt .= "- Academic questions and lessons\n";
        $basePrompt .= "- Examination and test preparation\n";
        $basePrompt .= "- School registration procedures\n";
        $basePrompt .= "- Learning materials access\n";
        $basePrompt .= "- General school information\n";
        $basePrompt .= "- Platform navigation assistance\n\n";

        $basePrompt .= "Important guidelines:\n";
        $basePrompt .= "- Be patient, helpful, and educational\n";
        $basePrompt .= "- If you don't know specific school policies, fees, or examination results, say so honestly\n";
        $basePrompt .= "- Do not invent information about policies, results, or administrative details\n";
        $basePrompt .= "- Guide users to appropriate resources or suggest contacting staff when needed\n";
        $basePrompt .= "- Respond in the same language the user uses (English or Kinyarwanda)\n";
        $basePrompt .= "- If the user requests human assistance, acknowledge and offer to connect them\n\n";

        if (!empty($userInfo)) {
            $basePrompt .= "User context:\n";
            if (!empty($userInfo['name'])) {
                $basePrompt .= "- Name: {$userInfo['name']}\n";
            }
            if (!empty($userInfo['role'])) {
                $basePrompt .= "- Role: {$userInfo['role']}\n";
            }
            if (!empty($userInfo['reg_number'])) {
                $basePrompt .= "- Registration Number: {$userInfo['reg_number']}\n";
            }
            if (!empty($userInfo['course'])) {
                $basePrompt .= "- Course: {$userInfo['course']}\n";
            }
            $basePrompt .= "\n";
        }

        return $basePrompt;
    }

    /**
     * Build the conversation contents for the API request
     */
    protected function buildConversationContents(string $systemPrompt, string $userMessage, array $conversationHistory): array
    {
        $contents = [];

        // Add system prompt as first message
        $contents[] = [
            'parts' => [
                ['text' => $systemPrompt],
            ],
            'role' => 'user',
        ];

        // Add conversation history (last 10 messages to stay within context limits)
        $recentHistory = array_slice($conversationHistory, -10);

        foreach ($recentHistory as $msg) {
            $role = $msg['sender_role'] === 'ai' ? 'model' : 'user';
            $contents[] = [
                'parts' => [
                    ['text' => $msg['message_content']],
                ],
                'role' => $role,
            ];
        }

        // Add current user message
        $contents[] = [
            'parts' => [
                ['text' => $userMessage],
            ],
            'role' => 'user',
        ];

        return $contents;
    }

    /**
     * Get a fallback message when the API fails
     */
    protected function getFallbackMessage(string $language): string
    {
        if ($language === 'rw') {
            return "Murakoze cyane kubaza. Ntabwo mbishobora guhura na we ubu wa gihe. Nyamuka wa ndikanya ugaruke maze ukanda again. Niba ari iby'ingenzi, tabara aba karitinya.";
        }

        return "Thank you for your message. I'm unable to respond at the moment. Please try again in a moment. If this is urgent, please contact our support staff directly.";
    }

    /**
     * Detect the language of a message
     */
    public function detectLanguage(string $message): string
    {
        // Simple Kinyarwanda detection based on common words
        $kinyarwandaWords = [
            'murakoze', 'urakoze', 'mbabwirinje', 'mbwirinje', 'ndi', 'ndabwira',
            'waba', 'ugomba', 'niba', 'byose', 'cyane', 'cyo', 'aho', 'tur',
            'du', 'mu', 'ku', 'wa', 'za', 'iyi', 'izo', 'na', 'ya', 'am',
            'im', 'um', 'ur', 'ik', 'in', 'ub', 'uk', 'kw', 'gu', 'ku',
            'gukora', 'kugira', 'ubwo', 'kuko', 'kugira ngo', 'amafoto',
            'amasomo', 'ikigo', 'ishuri', 'ishyirahamwe', 'amasomo',
        ];

        $messageLower = strtolower($message);
        $kinyarwandaCount = 0;

        foreach ($kinyarwandaWords as $word) {
            if (str_contains($messageLower, $word)) {
                $kinyarwandaCount++;
            }
        }

        // If 2 or more Kinyarwanda words are detected, assume Kinyarwanda
        return $kinyarwandaCount >= 2 ? 'rw' : 'en';
    }
}
