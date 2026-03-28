<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * WebRTCController — File-based signaling (bypasses Cache driver entirely)
 *
 * Root cause of the "0 signals" bug: the default cache driver may be 'array'
 * (in-memory, lost between requests) or misconfigured Redis/Memcached.
 * This implementation writes signal queues directly to storage/app/webrtc/
 * as JSON files with exclusive file locking, so every read/write hits the
 * exact same file on disk, regardless of cache configuration.
 */
class WebRTCController extends Controller
{
    const TTL = 600;

    private function dir(): string
    {
        $d = storage_path('app/webrtc');
        if (!is_dir($d)) mkdir($d, 0755, true);
        return $d;
    }

    private function path(string $key): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
        return $this->dir() . '/' . $safe . '.json';
    }

    private function read(string $key, $default = null)
    {
        $p = $this->path($key);
        if (!file_exists($p)) return $default;
        $fp = fopen($p, 'r');
        if (!$fp) return $default;
        flock($fp, LOCK_SH);
        $raw = stream_get_contents($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        $data = json_decode($raw, true);
        if (!is_array($data) || ($data['exp'] ?? 0) < time()) { @unlink($p); return $default; }
        return $data['v'] ?? $default;
    }

    private function write(string $key, $value, int $ttl = self::TTL): void
    {
        $p = $this->path($key);
        $fp = fopen($p, 'c');
        if (!$fp) return;
        flock($fp, LOCK_EX);
        ftruncate($fp, 0); rewind($fp);
        fwrite($fp, json_encode(['v' => $value, 'exp' => time() + $ttl]));
        flock($fp, LOCK_UN); fclose($fp);
    }

    private function forget(string $key): void { @unlink($this->path($key)); }

    private function push(string $key, array $item): void
    {
        $p = $this->path($key);
        $fp = fopen($p, 'c');
        if (!$fp) return;
        flock($fp, LOCK_EX);
        $raw = stream_get_contents($fp) ?: '';
        $data = json_decode($raw, true);
        $q = (is_array($data) && ($data['exp'] ?? 0) >= time()) ? ($data['v'] ?? []) : [];
        $q[] = $item;
        ftruncate($fp, 0); rewind($fp);
        fwrite($fp, json_encode(['v' => $q, 'exp' => time() + self::TTL]));
        flock($fp, LOCK_UN); fclose($fp);
    }

    private function drain(string $key): array
    {
        $p = $this->path($key);
        if (!file_exists($p)) return [];
        $fp = fopen($p, 'c');
        if (!$fp) return [];
        flock($fp, LOCK_EX);
        $raw = stream_get_contents($fp) ?: '';
        $data = json_decode($raw, true);
        $q = (is_array($data) && ($data['exp'] ?? 0) >= time()) ? ($data['v'] ?? []) : [];
        ftruncate($fp, 0); rewind($fp);
        fwrite($fp, json_encode(['v' => [], 'exp' => time() + self::TTL]));
        flock($fp, LOCK_UN); fclose($fp);
        return $q;
    }

    public function signaling(Request $request, $roomId)
    {
        $data = $request->all();
        $role = $data['role'] ?? 'client';
        $sessionId = $data['sessionId'] ?? session()->getId();
        $type = $data['type'] ?? '?';
        Log::info("WS signal room={$roomId} role={$role} type={$type} sid={$sessionId}");

        if ($role === 'host') {
            if (!empty($data['forSession'])) {
                $target = $data['forSession'];
                $key = "wrtc_c_{$roomId}_{$target}";
                $this->push($key, $data);
                Log::info("Host->client[{$target}] type={$type} key={$key}");
            } else {
                $clients = $this->read("wrtc_clients_{$roomId}", []);
                foreach ($clients as $cid) { $this->push("wrtc_c_{$roomId}_{$cid}", $data); }
                Log::info("Host broadcast type={$type} to ".count($clients)." clients");
            }
        } else {
            $key = "wrtc_h_{$roomId}";
            $this->push($key, array_merge($data, ['fromSession' => $sessionId]));
            Log::info("Client[{$sessionId}]->host type={$type}");
        }
        return response()->json(['ok' => true]);
    }

    public function pollSignaling(Request $request, $roomId)
    {
        $role = $request->query('role', 'client');
        $sessionId = $request->query('sessionId', session()->getId());

        if ($role === 'host') {
            $signals = $this->drain("wrtc_h_{$roomId}");
            if (count($signals)) Log::info("Host poll: drained ".count($signals)." signals room={$roomId}");
        } else {
            $clients = $this->read("wrtc_clients_{$roomId}", []);
            if (!in_array($sessionId, $clients)) {
                $clients[] = $sessionId;
                $this->write("wrtc_clients_{$roomId}", $clients);
                Log::info("Client auto-registered: {$sessionId} room={$roomId}");
            }
            $key = "wrtc_c_{$roomId}_{$sessionId}";
            $signals = $this->drain($key);
            if (count($signals)) Log::info("Client[{$sessionId}] poll: drained ".count($signals)." room={$roomId}");
        }
        return response()->json($signals);
    }

    public function joinRoom(Request $request, $roomId)
    {
        $sessionId = $request->input('sessionId', session()->getId());
        $name = $request->input('name') ?: $this->getUserName();

        $participants = $this->read("wrtc_parts_{$roomId}", []);
        $participants[$sessionId] = ['name' => $name, 'role' => $request->input('role', 'client'), 'joined_at' => now()->toISOString()];
        $this->write("wrtc_parts_{$roomId}", $participants);

        $clients = $this->read("wrtc_clients_{$roomId}", []);
        if (!in_array($sessionId, $clients)) { $clients[] = $sessionId; $this->write("wrtc_clients_{$roomId}", $clients); }

        $this->push("wrtc_h_{$roomId}", ['type' => 'viewer_ready', 'name' => $name, 'sessionId' => $sessionId, 'fromSession' => $sessionId, 'count' => count($participants)]);
        $this->push("wrtc_h_{$roomId}", ['type' => 'participant_joined', 'name' => $name, 'sessionId' => $sessionId, 'fromSession' => $sessionId, 'count' => count($participants)]);
        Log::info("joinRoom: room={$roomId} sid={$sessionId} name={$name}");

        return response()->json(['ok' => true, 'sessionId' => $sessionId, 'name' => $name, 'count' => count($participants)]);
    }

    public function leaveRoom(Request $request, $roomId)
    {
        $sessionId = $request->input('sessionId', session()->getId());
        $participants = $this->read("wrtc_parts_{$roomId}", []);
        $name = $participants[$sessionId]['name'] ?? 'Unknown';
        unset($participants[$sessionId]);
        $this->write("wrtc_parts_{$roomId}", $participants);
        $clients = $this->read("wrtc_clients_{$roomId}", []);
        $clients = array_values(array_filter($clients, fn($s) => $s !== $sessionId));
        $this->write("wrtc_clients_{$roomId}", $clients);
        $this->forget("wrtc_c_{$roomId}_{$sessionId}");
        $this->push("wrtc_h_{$roomId}", ['type' => 'participant_left', 'name' => $name, 'sessionId' => $sessionId, 'fromSession' => $sessionId, 'count' => count($participants)]);
        return response()->json(['ok' => true]);
    }

    public function chatMessage(Request $request, $roomId)
    {
        $request->validate(['message' => 'required|string|max:500']);
        $sessionId = $request->input('sessionId', session()->getId());
        $name = $request->input('name') ?: $this->getUserName();
        $msg = ['id' => uniqid(), 'type' => 'chat', 'message' => $request->message, 'sender' => $name, 'sessionId' => $sessionId, 'timestamp' => now()->toISOString()];
        $log = $this->read("wrtc_chat_{$roomId}", []);
        $log[] = $msg;
        if (count($log) > 200) $log = array_slice($log, -200);
        $this->write("wrtc_chat_{$roomId}", $log);
        $clients = $this->read("wrtc_clients_{$roomId}", []);
        foreach ($clients as $cid) { if ($cid !== $sessionId) $this->push("wrtc_c_{$roomId}_{$cid}", $msg); }
        $this->push("wrtc_h_{$roomId}", $msg);
        return response()->json(['ok' => true, 'id' => $msg['id']]);
    }

    public function pollChat(Request $request, $roomId)
    {
        $after = $request->query('after', '');
        $log = $this->read("wrtc_chat_{$roomId}", []);
        if ($after) $log = array_values(array_filter($log, fn($m) => ($m['timestamp'] ?? '') > $after));
        return response()->json(['messages' => array_values($log)]);
    }

    public function participants($roomId)
    {
        $p = $this->read("wrtc_parts_{$roomId}", []);
        return response()->json(['count' => count($p), 'participants' => array_values($p)]);
    }

    // Debug endpoint — remove in production
    public function debug(Request $request, $roomId)
    {
        $sid = $request->query('sessionId', '');
        $dir = $this->dir();
        $files = [];
        foreach (scandir($dir) as $f) {
            if ($f === '.' || $f === '..') continue;
            $key = str_replace('.json', '', $f);
            $val = $this->read($key);
            $files[$key] = is_array($val) ? count($val).' items' : $val;
        }
        return response()->json([
            'dir' => $dir, 'writable' => is_writable($dir),
            'files' => $files, 'php_session' => session()->getId(),
            'client_queue' => $sid ? $this->read("wrtc_c_{$roomId}_{$sid}", []) : 'no sid provided',
        ]);
    }

    private function getUserName(): string
    {
        foreach (['teacher', 'admin', 'staff', 'student', 'client'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $u = Auth::guard($guard)->user();
                $n = $u->full_name ?? $u->name ?? 'User';
                $tag = match ($guard) { 'teacher' => ' (Teacher)', 'admin' => ' (Admin)', 'staff' => ' (Staff)', default => '' };
                return $n . $tag;
            }
        }
        return 'Guest_' . substr(session()->getId(), 0, 6);
    }
}