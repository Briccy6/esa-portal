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
     * Generate a chat response using Gemini AI with OpenAI fallback
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
            // First try Gemini
            $geminiResult = $this->tryGeminiResponse($userMessage, $conversationHistory, $language, $userInfo);
            
            if ($geminiResult['success']) {
                return $geminiResult;
            }

            // If Gemini fails, try OpenAI as fallback
            Log::info('Gemini failed, trying OpenAI fallback', ['error' => $geminiResult['error'] ?? 'Unknown']);
            
            $openaiService = new OpenAIChatService();
            $openaiResult = $openaiService->generateResponse($userMessage, $conversationHistory, $language, $userInfo);
            
            if ($openaiResult['success']) {
                return $openaiResult;
            }

            // Both failed, return fallback
            return [
                'success' => false,
                'message' => $this->getFallbackMessage($language, $userMessage),
                'error' => 'Both AI services unavailable',
            ];

        } catch (\Exception $e) {
            Log::error('Chat service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $this->getFallbackMessage($language, $userMessage),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Try to generate response with Gemini
     */
    protected function tryGeminiResponse(string $userMessage, array $conversationHistory, string $language, array $userInfo): array
    {
        try {
            // Build the system prompt
            $systemPrompt = $this->buildSystemPrompt($language, $userInfo);

            // Build conversation context
            $contents = $this->buildConversationContents($systemPrompt, $userMessage, $conversationHistory);

            // Try multiple models in order of preference
            $models = [
                $this->model, // Primary model from config
                'gemini-flash-latest', // Fallback 1
                'gemini-pro-latest', // Fallback 2
                'gemini-1.5-flash', // Fallback 3
                'gemini-1.5-pro', // Fallback 4
            ];

            $lastError = null;
            
            foreach ($models as $model) {
                try {
                    $result = $this->tryGenerateResponse($contents, $model, $language);
                    if ($result['success']) {
                        return $result;
                    }
                    $lastError = $result['error'] ?? 'Unknown error';
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                    Log::warning("Gemini model {$model} failed", ['error' => $lastError]);
                    continue;
                }
            }

            return [
                'success' => false,
                'error' => $lastError,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Try to generate response with a specific model
     */
    protected function tryGenerateResponse(array $contents, string $model, string $language): array
    {
        $url = "{$this->endpoint}/{$model}:generateContent?key={$this->apiKey}";

        $response = Http::timeout(5) // Reduced to 5 seconds to prevent 504 errors
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
            $errorBody = $response->body();
            Log::warning("Gemini API error for model {$model}", [
                'status' => $response->status(),
                'body' => $errorBody,
            ]);

            // Check if it's a rate limit or high demand error
            if (str_contains($errorBody, 'high demand') || str_contains($errorBody, 'UNAVAILABLE')) {
                return [
                    'success' => false,
                    'error' => 'Service temporarily unavailable due to high demand',
                ];
            }

            return [
                'success' => false,
                'error' => "API request failed with status {$response->status()}",
            ];
        }

        $data = $response->json();
        $aiMessage = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$aiMessage) {
            return [
                'success' => false,
                'error' => 'No response from AI',
            ];
        }

        return [
            'success' => true,
            'message' => trim($aiMessage),
            'language' => $language,
        ];
    }

    /**
     * Build the system prompt based on language and user info
     */
    protected function buildSystemPrompt(string $language, array $userInfo): string
    {
        $basePrompt = "You are ESA AI Assistant, a helpful support assistant for Excellent Servant Academy. ";

        if ($language === 'rw') {
            $basePrompt .= "The user's preferred language is Kinyarwanda. Reply entirely in clear, natural Kinyarwanda unless they ask to switch languages. ";
        } else {
            $basePrompt .= "Reply in English unless the user writes in Kinyarwanda or asks to switch languages. ";
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
    protected function getFallbackMessage(string $language, string $userMessage = ''): string
    {
        // Try to provide a helpful response based on keywords
        $lowerMessage = strtolower($userMessage);
        
        if ($language === 'rw') {
            // Kinyarwanda fallback responses
            if (str_contains($lowerMessage, 'amasomo') || str_contains($lowerMessage, 'lesson')) {
                return "Murakoze kubaza. Amateka y'amasomo arashobora kuboneka kuri sisitemu yawe. Niba ukeneye uburenganzira bwo gusoma, baza uwizeretse utangiza w'amasomo.";
            }
            if (str_contains($lowerMessage, 'ikizamini') || str_contains($lowerMessage, 'exam')) {
                return "Murakoze kubaza. Ibizamini bisubira muri sisitemu yawe. Niba ukeneye amakuru y'ibizamini, baza uwizeretse utangiza w'amashuri.";
            }
            if (str_contains($lowerMessage, 'kwinjira') || str_contains($lowerMessage, 'login')) {
                return "Kwinjira kuri sisitemu, kanda kuri 'Login' hejuru y'urupapuro rw'inyeredi. Ongeraho email yawe n'ijambobanga ryawe.";
            }
            return "Murakoze cyane kubaza. Ntabwo mbishobora guhura na we ubu. Nyamuka wa ndikanya ugaruke maze ukanda again. Niba ari iby'ingenzi, tabara aba karitinya.";
        }

        // English fallback responses
        if (str_contains($lowerMessage, 'lesson') || str_contains($lowerMessage, 'course') || str_contains($lowerMessage, 'study')) {
            return "Thank you for your question. Your lessons and courses are available in the system. If you need access, please contact your course instructor or administrator.";
        }
        if (str_contains($lowerMessage, 'exam') || str_contains($lowerMessage, 'test') || str_contains($lowerMessage, 'grade')) {
            return "Thank you for your question. Exam schedules and results are available in the system. For specific exam inquiries, please contact your academic advisor.";
        }
        if (str_contains($lowerMessage, 'login') || str_contains($lowerMessage, 'password') || str_contains($lowerMessage, 'sign in')) {
            return "To log in, click the 'Login' button at the top of the page. Enter your registered email and password. If you've forgotten your password, use the 'Forgot Password' option.";
        }
        if (str_contains($lowerMessage, 'register') || str_contains($lowerMessage, 'enroll') || str_contains($lowerMessage, 'admission')) {
            return "For registration and enrollment information, please visit the admissions section or contact the admissions office directly.";
        }
        if (str_contains($lowerMessage, 'fee') || str_contains($lowerMessage, 'payment') || str_contains($lowerMessage, 'tuition')) {
            return "For fee and payment inquiries, please contact the bursar's office or check the payments section in your student portal.";
        }
        
        return "Thank you for your message. I'm currently experiencing high demand and may not respond immediately. Please try again in a few moments. If this is urgent, you can request human support using the button below.";
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
