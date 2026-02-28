<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class YouTubeController extends Controller
{
    public function listVideos(Request $request)
    {
        $channelId = $request->input('channel_id'); // Pass channel_id as query param
        $apiKey = config('services.youtube.api_key') ?? env('YOUTUBE_API_KEY');
        $maxResults = $request->input('max_results', 10);

        if (!$channelId || !$apiKey) {
            return response()->json(['error' => 'Missing channel_id or API key'], 400);
        }

        $url = 'https://www.googleapis.com/youtube/v3/search';
        $response = Http::get($url, [
            'key' => $apiKey,
            'channelId' => $channelId,
            'part' => 'snippet',
            'order' => 'date',
            'maxResults' => $maxResults,
            'type' => 'video',
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch videos'], 500);
        }

        $videos = collect($response->json('items'))->map(function ($item) {
            return [
                'videoId' => $item['id']['videoId'] ?? null,
                'title' => $item['snippet']['title'] ?? '',
                'description' => $item['snippet']['description'] ?? '',
                'thumbnail' => $item['snippet']['thumbnails']['medium']['url'] ?? '',
                'publishedAt' => $item['snippet']['publishedAt'] ?? '',
            ];
        });

        return response()->json(['videos' => $videos]);
    }
}
