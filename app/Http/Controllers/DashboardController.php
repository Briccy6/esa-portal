<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get student token data
        $studentToken = null;
        $subscriptions = [];
        
        if ($user && $user->token) {
            $studentToken = $user->token;
        } elseif ($user) {
            // Create token record if it doesn't exist
            $studentToken = Token::firstOrCreate(
                ['student_id' => $user->id],
                [
                    'balance' => 0,
                    'total_earned' => 0,
                    'total_spent' => 0,
                ]
            );
        }
        
        // Get active subscriptions
        $subscriptions = Subscription::active()->orderBy('price')->get();
        
        return view('dashboard', compact('studentToken', 'subscriptions'));
    }
}
