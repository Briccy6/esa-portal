<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LiveClassApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the api middleware group. Make something great!
|
*/

// Live Class API Routes for Node.js Integration
Route::prefix('live-class')->group(function () {
    
    // Session validation for Node.js server
    Route::post('/validate-session', [LiveClassApiController::class, 'validateSession']);
    
    // Get live class details
    Route::get('/{meetingId}', [LiveClassApiController::class, 'getLiveClass']);
    
    // Update live class status
    Route::post('/{meetingId}/status', [LiveClassApiController::class, 'updateStatus']);
    
    // Get live class participants
    Route::get('/{meetingId}/participants', [LiveClassApiController::class, 'getParticipants']);
});

// Health check for Node.js server
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'app' => 'ESA Portal',
        'version' => '1.0.0'
    ]);
});
