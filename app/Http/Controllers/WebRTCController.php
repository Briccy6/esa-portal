<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * WebRTCController — Cache-based signaling (no Pusher/Echo/log needed)
 *
 * Signal flow:
 *   Teacher  POST role=host  → fanned out to each client's queue
 *             ↳ if forSession is set, only that client receives it (ICE / offer targeting)
 *   Student  POST role=client → queued for host to read
 *   Both     GET  ?role=host|client&sessionId=xxx → drains their queue
 */
class WebRTCController extends Controller
{
    const TTL = 600;

    // ──────────────────────────────────────────────────────────
    // POST /webrtc/signaling/{roomId}
    // ──────────────────────────────────────────────────────────
    public function signaling(Request $request, $roomId)
    {
        $data      = $request->all();
        $role      = $data['role']      ?? 'client';
        $sessionId = $data['sessionId'] ?? session()->getId();

        // Debug logging
        \Log::info("WebRTC signaling: roomId={$roomId}, role={$role}, sessionId={$sessionId}", ['data' => $data]);

        // No authentication checks - WebRTC routes are now open
        \Log::info("WebRTC: Processing signal without authentication restrictions");

        if ($role === 'host') {
            // ── FIX #1: if forSession is set, route only to that specific client ──
            // This prevents ICE candidates / offers meant for one student
            // from being delivered to every other connected student.
            if (!empty($data['forSession'])) {
                $targetSession = $data['forSession'];
                $this->push("webrtc_to_client_{$roomId}_{$targetSession}", $data);
                \Log::info("Host sent targeted signal to client: {$targetSession}", ['type' => $data['type'] ?? 'unknown']);
            } else {
                // Broadcast to every connected client (e.g. a general host message)
                $clients = Cache::get("webrtc_clients_{$roomId}", []);
                \Log::info("Host broadcasting to " . count($clients) . " clients", ['clients' => $clients]);
                foreach ($clients as $clientSession) {
                    $this->push("webrtc_to_client_{$roomId}_{$clientSession}", $data);
                    \Log::info("Host sent broadcast signal to client: {$clientSession}");
                }
            }
        } else {
            // Student → queue for host
            $this->push("webrtc_to_host_{$roomId}", array_merge($data, [
                'fromSession' => $sessionId,
            ]));
            \Log::info("Client sent signal to host from: {$sessionId}");
        }

        return response()->json(['ok' => true]);
    }

    // ──────────────────────────────────────────────────────────
    // GET /webrtc/signaling/{roomId}?role=host|client&sessionId=xxx
    // ──────────────────────────────────────────────────────────
    public function pollSignaling(Request $request, $roomId)
    {
        $role      = $request->query('role', 'client');
        $sessionId = $request->query('sessionId', session()->getId());

        // Debug logging
        \Log::info("WebRTC pollSignaling: roomId={$roomId}, role={$role}, sessionId={$sessionId}");

        // No authentication checks - WebRTC routes are now open
        \Log::info("WebRTC: Processing poll without authentication restrictions");

        if ($role === 'host') {
            $key     = "webrtc_to_host_{$roomId}";
            $signals = Cache::get($key, []);
            Cache::put($key, [], self::TTL);

            \Log::info("Host polling: found " . count($signals) . " signals", ['signals' => $signals]);

        } else {
            // ── FIX #2: Do NOT auto-push viewer_ready here ──
            // viewer_ready is sent explicitly by joinRoom() on the client JS side.
            // Auto-registering here caused a second viewer_ready signal to fire on
            // every single poll (every 2 s), making the teacher create duplicate peers.
            // We still register the sessionId in the clients list so host can fan-out
            // ICE/offers to this client, but we do NOT push viewer_ready again.
            $clients = Cache::get("webrtc_clients_{$roomId}", []);
            if (!in_array($sessionId, $clients)) {
                $clients[] = $sessionId;
                Cache::put("webrtc_clients_{$roomId}", $clients, self::TTL);
                \Log::info("New client silently registered in clients list: {$sessionId}");
                // NOTE: viewer_ready is sent by joinRoom() JS call, not here.
            }

            $key     = "webrtc_to_client_{$roomId}_{$sessionId}";
            $signals = Cache::get($key, []);
            Cache::put($key, [], self::TTL);

            \Log::info("Client polling: found " . count($signals) . " signals for {$sessionId}", ['signals' => $signals]);
        }

        return response()->json($signals);
    }

    // ──────────────────────────────────────────────────────────
    // POST /webrtc/join/{roomId}
    // ──────────────────────────────────────────────────────────
    public function joinRoom(Request $request, $roomId)
    {
        $sessionId = session()->getId();
        $name      = $this->getUserName();

        $participants               = Cache::get("webrtc_participants_{$roomId}", []);
        $participants[$sessionId]   = [
            'name'      => $name,
            'role'      => $request->input('role', 'client'),
            'joined_at' => now()->toISOString(),
        ];
        Cache::put("webrtc_participants_{$roomId}", $participants, self::TTL);

        // Add to client list
        $clients = Cache::get("webrtc_clients_{$roomId}", []);
        if (!in_array($sessionId, $clients)) {
            $clients[] = $sessionId;
            Cache::put("webrtc_clients_{$roomId}", $clients, self::TTL);
        }

        // ── FIX #3: Push viewer_ready here (from joinRoom) so the host creates
        // exactly ONE peer per student join. The pollSignaling no longer pushes
        // viewer_ready, so there is no duplication.
        $this->push("webrtc_to_host_{$roomId}", [
            'type'        => 'viewer_ready',
            'name'        => $name,
            'sessionId'   => $sessionId,
            'fromSession' => $sessionId,
            'count'       => count($participants),
        ]);

        // Also push participant_joined for the participant list UI
        $this->push("webrtc_to_host_{$roomId}", [
            'type'        => 'participant_joined',
            'name'        => $name,
            'sessionId'   => $sessionId,
            'fromSession' => $sessionId,
            'count'       => count($participants),
        ]);

        return response()->json([
            'ok'        => true,
            'sessionId' => $sessionId,
            'name'      => $name,
            'count'     => count($participants),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /webrtc/leave/{roomId}
    // ──────────────────────────────────────────────────────────
    public function leaveRoom(Request $request, $roomId)
    {
        $sessionId    = session()->getId();
        $participants = Cache::get("webrtc_participants_{$roomId}", []);
        $name         = $participants[$sessionId]['name'] ?? 'Unknown';

        unset($participants[$sessionId]);
        Cache::put("webrtc_participants_{$roomId}", $participants, self::TTL);

        $clients = Cache::get("webrtc_clients_{$roomId}", []);
        $clients = array_values(array_filter($clients, fn($s) => $s !== $sessionId));
        Cache::put("webrtc_clients_{$roomId}", $clients, self::TTL);
        Cache::forget("webrtc_to_client_{$roomId}_{$sessionId}");

        $this->push("webrtc_to_host_{$roomId}", [
            'type'        => 'participant_left',
            'name'        => $name,
            'sessionId'   => $sessionId,
            'fromSession' => $sessionId,
            'count'       => count($participants),
        ]);

        return response()->json(['ok' => true]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /webrtc/chat/{roomId}
    // ──────────────────────────────────────────────────────────
    public function chatMessage(Request $request, $roomId)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $sessionId = session()->getId();
        $name      = $this->getUserName();
        $msg = [
            'id'        => uniqid(),
            'type'      => 'chat',
            'message'   => $request->message,
            'sender'    => $name,
            'sessionId' => $sessionId,
            'timestamp' => now()->toISOString(),
        ];

        // Persist in shared chat log
        $log   = Cache::get("webrtc_chat_{$roomId}", []);
        $log[] = $msg;
        if (count($log) > 200) $log = array_slice($log, -200);
        Cache::put("webrtc_chat_{$roomId}", $log, self::TTL);

        // Push to every other participant's queue
        $clients = Cache::get("webrtc_clients_{$roomId}", []);
        foreach ($clients as $cs) {
            if ($cs !== $sessionId) {
                $this->push("webrtc_to_client_{$roomId}_{$cs}", $msg);
            }
        }
        // Also push to host queue so teacher sees student messages
        $this->push("webrtc_to_host_{$roomId}", $msg);

        return response()->json(['ok' => true, 'id' => $msg['id']]);
    }

    // ──────────────────────────────────────────────────────────
    // GET /webrtc/chat/{roomId}?after=ISO_TIMESTAMP
    // ──────────────────────────────────────────────────────────
    public function pollChat(Request $request, $roomId)
    {
        $after = $request->query('after', '');
        $log   = Cache::get("webrtc_chat_{$roomId}", []);

        if ($after) {
            $log = array_values(array_filter($log, fn($m) => ($m['timestamp'] ?? '') > $after));
        }

        return response()->json(['messages' => array_values($log)]);
    }

    // ──────────────────────────────────────────────────────────
    // GET /webrtc/participants/{roomId}
    // ──────────────────────────────────────────────────────────
    public function participants($roomId)
    {
        $p = Cache::get("webrtc_participants_{$roomId}", []);
        return response()->json(['count' => count($p), 'participants' => array_values($p)]);
    }

    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────
    private function push(string $key, array $signal): void
    {
        $q   = Cache::get($key, []);
        $q[] = $signal;
        Cache::put($key, $q, self::TTL);
    }

    private function getUserName(): string
    {
        foreach (['teacher', 'admin', 'staff', 'student', 'client'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $u   = Auth::guard($guard)->user();
                $n   = $u->full_name ?? $u->name ?? 'User';
                $tag = match ($guard) {
                    'teacher' => ' (Teacher)',
                    'admin'   => ' (Admin)',
                    'staff'   => ' (Staff)',
                    default   => '',
                };
                return $n . $tag;
            }
        }
        return 'Guest_' . substr(session()->getId(), 0, 6);
    }
}