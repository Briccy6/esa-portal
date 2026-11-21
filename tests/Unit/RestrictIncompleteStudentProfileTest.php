<?php

namespace Tests\Unit;

use App\Http\Middleware\RestrictIncompleteStudentProfile;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RestrictIncompleteStudentProfileTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_allows_incomplete_students_to_use_protected_routes_before_the_deadline(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-22 11:59:59', 'Africa/Kigali'));
        Auth::guard('student')->setUser(new Student());

        [$response, $nextCalled] = $this->runMiddlewareFor('student.courses');

        $this->assertTrue($nextCalled);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_it_redirects_incomplete_students_from_protected_routes_after_the_deadline(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-22 12:00:01', 'Africa/Kigali'));
        Auth::guard('student')->setUser(new Student());

        [$response, $nextCalled] = $this->runMiddlewareFor('student.courses');

        $this->assertFalse($nextCalled);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('student.dashboard'), $response->headers->get('Location'));
    }

    public function test_it_restricts_student_access_to_routes_shared_with_other_roles(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-22 12:00:01', 'Africa/Kigali'));
        Auth::guard('student')->setUser(new Student());

        [$response, $nextCalled] = $this->runMiddlewareFor('to-whom.index');

        $this->assertFalse($nextCalled);
        $this->assertSame(302, $response->getStatusCode());
    }

    public function test_it_allows_incomplete_students_to_open_the_profile_after_the_deadline(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-22 12:00:01', 'Africa/Kigali'));
        Auth::guard('student')->setUser(new Student());

        [$response, $nextCalled] = $this->runMiddlewareFor('students.profile');

        $this->assertTrue($nextCalled);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_it_allows_complete_students_to_use_protected_routes_after_the_deadline(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-22 12:00:01', 'Africa/Kigali'));
        $student = new Student();
        foreach ([
            'full_name',
            'phone',
            'email',
            'sex',
            'passport_photo',
            'identification_type',
            'id_number',
            'id_photo',
            'education_level',
        ] as $field) {
            $student->setAttribute($field, 'provided');
        }
        $student->setAttribute('passport_photo_verification_status', 'approved');
        Auth::guard('student')->setUser($student);

        [$response, $nextCalled] = $this->runMiddlewareFor('student.courses');

        $this->assertTrue($nextCalled);
        $this->assertSame(200, $response->getStatusCode());
    }

    private function runMiddlewareFor(string $routeName): array
    {
        $route = Route::getRoutes()->getByName($routeName);
        $request = Request::create(route($routeName));
        $request->setRouteResolver(fn () => $route);
        $nextCalled = false;

        $response = app(RestrictIncompleteStudentProfile::class)->handle(
            $request,
            function () use (&$nextCalled): Response {
                $nextCalled = true;

                return response('continued');
            }
        );

        return [$response, $nextCalled];
    }
}
