<?php

namespace App\Http\Controllers;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\SupportInternalNote;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportDashboardController extends Controller
{
    /**
     * Display the support dashboard
     */
    public function index()
    {
        $stats = [
            'total' => SupportConversation::count(),
            'open' => SupportConversation::where('status', 'open')->count(),
            'pending' => SupportConversation::where('status', 'pending')->count(),
            'in_progress' => SupportConversation::where('status', 'in_progress')->count(),
            'resolved' => SupportConversation::where('status', 'resolved')->count(),
            'ai_handled' => SupportConversation::where('handling_mode', 'ai')->count(),
            'human_handled' => SupportConversation::where('handling_mode', 'human')->count(),
        ];

        $recentConversations = SupportConversation::with(['latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.support.dashboard', compact('stats', 'recentConversations'));
    }

    /**
     * Get all conversations with filters
     */
    public function getConversations(Request $request)
    {
        $query = SupportConversation::with(['latestMessage', 'user', 'assignedAgent']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by handling mode
        if ($request->has('handling_mode') && $request->handling_mode !== 'all') {
            $query->where('handling_mode', $request->handling_mode);
        }

        // Filter by user type
        if ($request->has('user_type') && $request->user_type !== 'all') {
            $query->where('user_type', $request->user_type);
        }

        // Search by user name or reg number
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHasMorph('user', ['student', 'teacher', 'staff'], function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('teacher_name', 'like', "%{$search}%")
                    ->orWhere('reg_number', 'like', "%{$search}%");
            });
        }

        $conversations = $query->orderBy('last_message_at', 'desc')
            ->paginate(20);

        // Load user details for each conversation
        $conversations->getCollection()->transform(function ($conversation) {
            $conversation->user_details = $this->getUserDetails($conversation);
            return $conversation;
        });

        return response()->json($conversations);
    }

    /**
     * Get conversation details with messages
     */
    public function getConversation($id)
    {
        $conversation = SupportConversation::with([
            'messages' => function ($query) {
                $query->where('is_internal_note', false)->orderBy('created_at');
            },
            'internalNotes' => function ($query) {
                $query->orderBy('created_at');
            },
        ])->findOrFail($id);

        $conversation->user_details = $this->getUserDetails($conversation);

        return response()->json($conversation);
    }

    /**
     * Assign conversation to an agent
     */
    public function assignAgent(Request $request, $id)
    {
        $request->validate([
            'agent_id' => 'required',
            'agent_type' => 'required|in:admin,staff',
        ]);

        $conversation = SupportConversation::findOrFail($id);
        $user = Auth::user();

        $conversation->assignToAgent($request->agent_id, $request->agent_type);

        // Add system message
        SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'sender_role' => 'agent',
            'message_content' => 'Agent has taken over this conversation.',
            'message_type' => 'system',
        ]);

        return response()->json([
            'message' => 'Conversation assigned successfully',
            'conversation' => $conversation,
        ]);
    }

    /**
     * Reply to a conversation as an agent
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $conversation = SupportConversation::findOrFail($id);
        $user = Auth::user();

        // Ensure conversation is in human mode
        if ($conversation->handling_mode !== 'human') {
            $conversation->update(['handling_mode' => 'human']);
        }

        // Save agent message
        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'sender_role' => 'agent',
            'message_content' => $request->message,
            'message_type' => 'text',
        ]);

        $conversation->updateLastMessageAt();

        return response()->json([
            'message' => 'Reply sent successfully',
            'conversation' => $conversation->load('messages'),
        ]);
    }

    /**
     * Update conversation status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,pending,in_progress,resolved,closed',
        ]);

        $conversation = SupportConversation::findOrFail($id);
        $conversation->update(['status' => $request->status]);

        if ($request->status === 'resolved') {
            $conversation->markAsResolved();
        }

        return response()->json([
            'message' => 'Status updated successfully',
            'conversation' => $conversation,
        ]);
    }

    /**
     * Return conversation to AI handling
     */
    public function returnToAi($id)
    {
        $conversation = SupportConversation::findOrFail($id);
        $user = Auth::user();

        $conversation->returnToAi();

        // Add system message
        SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'sender_role' => 'agent',
            'message_content' => 'Conversation returned to AI assistant.',
            'message_type' => 'system',
        ]);

        return response()->json([
            'message' => 'Conversation returned to AI',
            'conversation' => $conversation,
        ]);
    }

    /**
     * Add internal note
     */
    public function addInternalNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $conversation = SupportConversation::findOrFail($id);
        $user = Auth::user();

        $note = SupportInternalNote::create([
            'conversation_id' => $conversation->id,
            'author_id' => $user->id,
            'author_type' => get_class($user),
            'note_content' => $request->note,
        ]);

        return response()->json([
            'message' => 'Internal note added',
            'note' => $note->load('author'),
        ]);
    }

    /**
     * Get analytics data
     */
    public function getAnalytics(Request $request)
    {
        $startDate = $request->start_date ?? now()->subDays(30);
        $endDate = $request->end_date ?? now();

        $analytics = [
            'total_conversations' => SupportConversation::whereBetween('created_at', [$startDate, $endDate])->count(),
            'resolved_conversations' => SupportConversation::whereBetween('resolved_at', [$startDate, $endDate])->count(),
            'avg_resolution_time' => $this->calculateAverageResolutionTime($startDate, $endDate),
            'conversations_by_status' => SupportConversation::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'conversations_by_user_type' => SupportConversation::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('user_type, COUNT(*) as count')
                ->groupBy('user_type')
                ->pluck('count', 'user_type')
                ->toArray(),
            'conversations_by_handling_mode' => SupportConversation::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('handling_mode, COUNT(*) as count')
                ->groupBy('handling_mode')
                ->pluck('count', 'handling_mode')
                ->toArray(),
        ];

        return response()->json($analytics);
    }

    /**
     * Get user details for a conversation
     */
    protected function getUserDetails(SupportConversation $conversation): array
    {
        $user = $conversation->user;

        if (!$user) {
            return [];
        }

        $details = [
            'id' => $user->id,
            'type' => $conversation->user_type,
        ];

        if ($conversation->user_type === 'student') {
            $details['name'] = $user->full_name;
            $details['reg_number'] = $user->reg_number;
            $details['email'] = $user->email;
            $details['phone'] = $user->phone;
            $details['course'] = $user->course->name ?? '';
            $details['branch'] = $user->branch->name ?? '';
            $details['academic_year'] = $user->academicYear->name ?? '';
        } elseif ($conversation->user_type === 'teacher') {
            $details['name'] = $user->teacher_name;
            $details['email'] = $user->email;
            $details['phone'] = $user->telephone;
            $details['department'] = $user->department;
            $details['branch'] = $user->branch->name ?? '';
        } elseif ($conversation->user_type === 'staff') {
            $details['name'] = $user->name;
            $details['email'] = $user->email;
            $details['phone'] = $user->phone;
            $details['role'] = $user->role;
        } elseif ($conversation->user_type === 'admin') {
            $details['name'] = $user->first_name . ' ' . $user->last_name;
            $details['email'] = $user->email;
        }

        return $details;
    }

    /**
     * Calculate average resolution time
     */
    protected function calculateAverageResolutionTime($startDate, $endDate): float
    {
        $resolved = SupportConversation::where('status', 'resolved')
            ->whereBetween('resolved_at', [$startDate, $endDate])
            ->whereNotNull('created_at')
            ->get();

        if ($resolved->isEmpty()) {
            return 0;
        }

        $totalMinutes = $resolved->sum(function ($conv) {
            return $conv->created_at->diffInMinutes($conv->resolved_at);
        });

        return round($totalMinutes / $resolved->count(), 2);
    }
}
