<?php
namespace App\Http\Controllers;

use App\Models\LiveClassSignal;
use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveClassSignalingController extends Controller
{
    /**
     * Teacher or student sends a signal (offer, answer, ICE, chat, hand-raise)
     */
    public function send(Request $request, $liveClassId)
    {
        $request->validate([
            'from_peer' => 'required|string|max:100',
            'to_peer'   => 'required|string|max:100',
            'type'      => 'required|string|max:50',
            'payload'   => 'required',
        ]);

        LiveClassSignal::create([
            'live_class_id' => $liveClassId,
            'from_peer'     => $request->from_peer,
            'to_peer'       => $request->to_peer,
            'type'          => $request->type,
            'payload'       => is_array($request->payload)
                                ? json_encode($request->payload)
                                : $request->payload,
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Poll for signals addressed to this peer (since last signal id)
     */
    public function poll(Request $request, $liveClassId)
    {
        $peer    = $request->query('peer');
        $since   = (int) $request->query('since', 0);

        $signals = LiveClassSignal::where('live_class_id', $liveClassId)
            ->where(function($q) use ($peer) {
                $q->where('to_peer', $peer)
                  ->orWhere('to_peer', 'all');
            })
            ->where('id', '>', $since)
            ->orderBy('id')
            ->limit(50)
            ->get(['id', 'from_peer', 'to_peer', 'type', 'payload', 'created_at']);

        // Clean old signals (keep last 500 per class)
        $maxId = LiveClassSignal::where('live_class_id', $liveClassId)->max('id');
        if ($maxId > 500) {
            LiveClassSignal::where('live_class_id', $liveClassId)
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
        // Get the last "participant-update" signal of type "all"
        $signal = LiveClassSignal::where('live_class_id', $liveClassId)
            ->where('type', 'participant-list')
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'participants' => $signal ? json_decode($signal->payload, true) : []
        ]);
    }
}