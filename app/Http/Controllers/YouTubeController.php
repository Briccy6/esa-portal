<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Illuminate\Support\Facades\Session;

class YouTubeController extends Controller
{
    public function redirectToYouTube()
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function handleYouTubeCallback(Request $request)
    {
        $client = $this->getClient();
        if ($request->has('code')) {
            $accessToken = $client->fetchAccessTokenWithAuthCode($request->code);
            Session::put('youtube_access_token', $accessToken);
            return redirect()->route('youtube.upload.form');
        }
        return redirect()->route('dashboard')->with('error', 'YouTube authorization failed.');
    }

    public function showUploadForm()
    {
        return view('youtube.upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:51200', // max 50MB
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $client = $this->getClient();
        $client->setAccessToken(Session::get('youtube_access_token'));

        if ($client->isAccessTokenExpired()) {
            return redirect()->route('youtube.redirect');
        }

        $youtube = new Google_Service_YouTube($client);

        $snippet = new Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($request->title);
        $snippet->setDescription($request->description ?? '');
        $snippet->setTags(['ESA', 'School', 'Education']);

        $status = new Google_Service_YouTube_VideoStatus();
        $status->privacyStatus = 'public';

        $video = new Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        $videoPath = $request->file('video')->getPathname();

        $chunkSizeBytes = 1 * 1024 * 1024; // 1MB

        $client->setDefer(true);

        $insertRequest = $youtube->videos->insert('status,snippet', $video);

        $media = new \Google\Http\MediaFileUpload(
            $client,
            $insertRequest,
            'video/*',
            null,
            true,
            $chunkSizeBytes
        );
        $media->setFileSize(filesize($videoPath));

        $status = false;
        $handle = fopen($videoPath, "rb");
        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }
        fclose($handle);

        $client->setDefer(false);

        return redirect()->route('youtube.upload.form')->with('success', 'Video uploaded to YouTube!');
    }

    private function getClient()
    {
        $client = new Google_Client();
        $client->setClientId(env('YOUTUBE_CLIENT_ID'));
        $client->setClientSecret(env('YOUTUBE_CLIENT_SECRET'));
        $client->setRedirectUri(env('YOUTUBE_REDIRECT_URI'));
        $client->addScope(Google_Service_YouTube::YOUTUBE_UPLOAD);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        return $client;
    }
}