<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIChatService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-3.5-turbo');
    }

    /**
     * Generate a chat response using OpenAI
     */
    public function generateResponse(string $userMessage, array $conversationHistory = [], string $language = 'en', array $userInfo = []): array
    {
        try {
            $systemPrompt = $this->buildSystemPrompt($language, $userInfo);
            
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];

            // Add conversation history (last 10 messages)
            $recentHistory = array_slice($conversationHistory, -10);
            foreach ($recentHistory as $msg) {
                $role = $msg['sender_role'] === 'ai' ? 'assistant' : 'user';
                $messages[] = [
                    'role' => $role,
                    'content' => $msg['message_content'],
                ];
            }

            // Add current user message
            $messages[] = [
                'role' => 'user',
                'content' => $userMessage,
            ];

            $response = Http::timeout(5) // Reduced to 5 seconds to prevent 504 errors
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 2048,
                ]);

            if (!$response->successful()) {
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => $this->getFallbackMessage($language, $userMessage),
                    'error' => 'OpenAI API request failed',
                ];
            }

            $data = $response->json();
            $aiMessage = $data['choices'][0]['message']['content'] ?? null;

            if (!$aiMessage) {
                return [
                    'success' => false,
                    'message' => $this->getFallbackMessage($language, $userMessage),
                    'error' => 'No response from OpenAI',
                ];
            }

            return [
                'success' => true,
                'message' => trim($aiMessage),
                'language' => $language,
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI chat service error', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $this->getFallbackMessage($language, $userMessage),
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
     * Get a fallback message when the API fails
     */
    protected function getFallbackMessage(string $language, string $userMessage = ''): string
    {
        $lowerMessage = strtolower($userMessage);
        
        if ($language === 'rw') {
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
}
