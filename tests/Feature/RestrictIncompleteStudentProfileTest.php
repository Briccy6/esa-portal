<?php

namespace Tests\Feature;

use App\Http\Middleware\RestrictIncompleteStudentProfile;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RestrictIncompleteStudentProfileTest extends TestCase
{
    public function test_incomplete_student_can_load_the_support_chat_for_profile_help(): void
    {
        Carbon::setTestNow(Student::profileCompletionDeadline()->addMinute());
        Auth::guard('student')->setUser(new Student());

        $route = new Route('GET', '/api/chat/conversation', [
            'uses' => 'SupportChatController@getConversation',
            'as' => 'api.chat.conversation',
        ]);
        $route->middleware('auth:student,teacher,staff,admin');

        $request = Request::create('/api/chat/conversation');
        $request->setRouteResolver(fn () => $route);

        try {
            $response = app(RestrictIncompleteStudentProfile::class)
                ->handle($request, fn () => response('support-chat-available'));

            $this->assertSame('support-chat-available', $response->getContent());
        } finally {
            Carbon::setTestNow();
        }
    }
}
