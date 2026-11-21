<?php

namespace App\Http\Controllers;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\SupportInternalNote;
use App\Services\GeminiChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportChatController extends Controller
{
    protected GeminiChatService $geminiService;

    public function __construct(GeminiChatService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Get or create a conversation for the authenticated user
     */
    public function getConversation()
    {
        $user = Auth::user();
        $userType = $this->getUserType();

        if (!$user || !$userType) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $conversation = SupportConversation::where('user_id', $user->id)
            ->where('user_type', $userType)
            ->where('status', '!=', 'closed')
            ->latest()
            ->first();

        if (!$conversation) {
            $conversation = SupportConversation::create([
                'user_id' => $user->id,
                'user_type' => $userType,
                'status' => 'open',
                'handling_mode' => 'ai',
                'language' => 'en',
            ]);

            // Add welcome message
            $this->addWelcomeMessage($conversation, $user, $userType);
        }

        return response()->json([
            'conversation' => $conversation->load('messages'),
        ]);
    }

    /**
     * Send a message in the conversation
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'conversation_id' => 'nullable|exists:support_conversations,id',
        ]);

        $user = Auth::user();
        $userType = $this->getUserType();

        if (!$user || !$userType) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get or create conversation
        $conversationId = $request->conversation_id;
        if (!$conversationId) {
            $conversation = SupportConversation::where('user_id', $user->id)
                ->where('user_type', $userType)
                ->where('status', '!=', 'closed')
                ->latest()
                ->first();

            if (!$conversation) {
                $conversation = SupportConversation::create([
                    'user_id' => $user->id,
                    'user_type' => $userType,
                    'status' => 'open',
                    'handling_mode' => 'ai',
                    'language' => 'en',
                ]);
            }
        } else {
            $conversation = SupportConversation::findOrFail($conversationId);

            // Verify ownership
            if ($conversation->user_id != $user->id || $conversation->user_type != $userType) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        // Detect language
        $detectedLanguage = $this->geminiService->detectLanguage($request->message);
        $conversation->update(['language' => $detectedLanguage]);

        // Save user message
        $userMessage = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => $userType,
            'sender_role' => 'user',
            'message_content' => $request->message,
            'message_type' => 'text',
        ]);

        $conversation->updateLastMessageAt();

        // Check if conversation is in AI mode
        if ($conversation->handling_mode === 'ai') {
            // Get conversation history for context
            $history = $conversation->messages()
                ->where('sender_role', '!=', 'agent')
                ->where('is_internal_note', false)
                ->orderBy('created_at', 'asc')
                ->take(10)
                ->get()
                ->toArray();

            // Get user info for context
            $userInfo = $this->getUserInfo($user, $userType);

            // Generate AI response
            $aiResponse = $this->geminiService->generateResponse(
                $request->message,
                $history,
                $detectedLanguage,
                $userInfo
            );

            // Save AI message
            $aiMessage = SupportMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => null,
                'sender_type' => null,
                'sender_role' => 'ai',
                'message_content' => $aiResponse['message'],
                'message_type' => 'text',
            ]);

            $conversation->updateLastMessageAt();

            return response()->json([
                'user_message' => $userMessage,
                'ai_message' => $aiMessage,
                'conversation' => $conversation,
            ]);
        }

        // If in human mode, just acknowledge
        return response()->json([
            'user_message' => $userMessage,
            'conversation' => $conversation,
            'message' => 'Your message has been sent to the support agent.',
        ]);
    }

    /**
     * Get conversation history
     */
    public function getHistory()
    {
        $user = Auth::user();
        $userType = $this->getUserType();

        if (!$user || !$userType) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $conversations = SupportConversation::where('user_id', $user->id)
            ->where('user_type', $userType)
            ->with(['messages' => function ($query) {
                $query->where('is_internal_note', false)->orderBy('created_at');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($conversations);
    }

    /**
     * Request human support
     */
    public function requestHumanSupport(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:support_conversations,id',
        ]);

        $user = Auth::user();
        $userType = $this->getUserType();

        if (!$user || !$userType) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $conversation = SupportConversation::findOrFail($request->conversation_id);

        // Verify ownership
        if ($conversation->user_id != $user->id || $conversation->user_type != $userType) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update conversation status
        $conversation->update([
            'status' => 'pending',
            'handling_mode' => 'human',
        ]);

        // Add system message
        SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => null,
            'sender_role' => 'user',
            'message_content' => 'User has requested human support.',
            'message_type' => 'system',
        ]);

        return response()->json([
            'message' => 'Your request for human support has been received. An agent will be with you shortly.',
            'conversation' => $conversation,
        ]);
    }

    /**
     * Add welcome message to conversation
     */
    protected function addWelcomeMessage(SupportConversation $conversation, $user, string $userType)
    {
        $language = $conversation->language;

        if ($language === 'rw') {
            $welcomeMessage = "Murakaza neza! Ndi ESA AI Assistant.\n\nNshobora kugufasha mu:\n- Iby'amasomo n'imyitwarire\n- Igeragezabushobozi n'ibizaminiro\n- Kwiyandikisha n'imyitwarire y'ikigo\n- Ubufasha mu kwiga\n- Ibisobanuro by'ikigo\n\nUrashobora kubaza in Kinyarwanda cyangwa Icyongereza. Ndashobora gute kukwigira?";
        } else {
            $welcomeMessage = "Hello! Welcome to Excellent Servant Academy.\n\nI am ESA AI Assistant. I can help you with:\n- Academic questions and lessons\n- Examination preparation\n- Registration procedures\n- Learning materials\n- School information\n- Platform navigation\n\nYou can communicate with me in English or Kinyarwanda. How can I help you today?";
        }

        SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => null,
            'sender_role' => 'ai',
            'message_content' => $welcomeMessage,
            'message_type' => 'text',
        ]);

        $conversation->updateLastMessageAt();
    }

    /**
     * Get user type based on current guard
     */
    protected function getUserType(): ?string
    {
        $guard = Auth::getDefaultDriver();

        return match ($guard) {
            'student' => 'student',
            'teacher' => 'teacher',
            'staff' => 'staff',
            'admin' => 'admin',
            default => null,
        };
    }

    /**
     * Get user information for AI context
     */
    protected function getUserInfo($user, string $userType): array
    {
        $info = [
            'name' => $user->full_name ?? $user->name ?? $user->teacher_name ?? '',
            'role' => $userType,
        ];

        if ($userType === 'student') {
            $info['reg_number'] = $user->reg_number ?? '';
            $info['course'] = $user->course->name ?? '';
            $info['branch'] = $user->branch->name ?? '';
        } elseif ($userType === 'teacher') {
            $info['department'] = $user->department ?? '';
        } elseif ($userType === 'staff') {
            $info['role'] = $user->role ?? '';
        }

        return $info;
    }
}
