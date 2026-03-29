<?php
namespace App\Http\Controllers;

use App\Models\LiveClassSignal;
use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveClassSignalingController extends Controller
{
    /**
     * Clients use /live-signaling/class-{id}/... but live_class_id in DB is numeric.
     */
    private function resolveNumericLiveClassId($liveClassId): int
    {
        if (is_numeric($liveClassId)) {
            return (int) $liveClassId;
        }
        if (is_string($liveClassId) && preg_match('/^class-(\d+)$/i', $liveClassId, $m)) {
            return (int) $m[1];
        }

        return (int) $liveClassId;
    }

    /**
     * Teacher or student sends a signal (offer, answer, ICE, chat, hand-raise)
     */
    public function send(Request $request, $liveClassId)
    {
        // Debug logging
        \Log::info('LiveClassSignalingController::send called', [
            'liveClassId' => $liveClassId,
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            $request->validate([
                'from_peer' => 'required|string|max:100',
                'to_peer'   => 'required|string|max:100',
                'type'      => 'required|string|max:50',
                'payload'   => 'required',
            ]);

            \Log::info('Validation passed');

            $classId = $this->resolveNumericLiveClassId($liveClassId);

            LiveClassSignal::create([
                'live_class_id' => $classId,
                'from_peer'     => $request->from_peer,
                'to_peer'       => $request->to_peer,
                'type'          => $request->type,
                'payload'       => is_array($request->payload)
                                    ? json_encode($request->payload)
                                    : $request->payload,
            ]);

            \Log::info('Signal created successfully');

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            \Log::error('Error in LiveClassSignalingController::send', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }
    }

    /**
     * Poll for signals addressed to this peer (since last signal id)
     */
    public function poll(Request $request, $liveClassId)
    {
        $peer    = $request->query('peer');
        $since   = (int) $request->query('since', 0);

        $classId = $this->resolveNumericLiveClassId($liveClassId);

        $signals = LiveClassSignal::where('live_class_id', $classId)
            ->where(function($q) use ($peer) {
                $q->where('to_peer', $peer)
                  ->orWhere('to_peer', 'all');
            })
            ->where('id', '>', $since)
            ->orderBy('id')
            ->limit(50)
            ->get(['id', 'from_peer', 'to_peer', 'type', 'payload', 'created_at']);

        // Clean old signals (keep last 500 per class)
        $maxId = LiveClassSignal::where('live_class_id', $classId)->max('id');
        if ($maxId > 500) {
            LiveClassSignal::where('live_class_id', $classId)
                ->where('id', '<', $maxId - 500)
                ->delete();
        }

        return response()->json([
            'signals' => $signals->map(fn($s) => [
                'id'        => $s->id,
                'from_peer' => $s->from_peer,
                'to_peer'   => $s->to_peer,
                'type'      => $s->type,
                'payload'   => json_decode($s->payload, true) ?? $s->payload,
                'time'      => $s->created_at->toIso8601String(),
            ])
        ]);
    }

    /**
     * Get current participants list
     */
    public function participants($liveClassId)
    {
        $classId = $this->resolveNumericLiveClassId($liveClassId);

        $participants = \App\Models\LiveClassParticipant::where('live_class_id', $classId)
            ->where('is_active', true)
            ->whereNull('left_at')
            ->get()
            ->map(function ($p) {
                return [
                    'name' => $p->name ?? 'Unknown',
                    'role' => ucfirst($p->user_type),
                    'socketId' => $p->id,
                ];
            });

        return response()->json($participants);
    }
}