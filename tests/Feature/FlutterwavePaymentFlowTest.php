<?php

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentTransaction;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->academicYear = AcademicYear::create([
        'year' => '2026-2027',
        'starting_date' => '2026-01-01',
        'expiry_date' => '2027-12-31',
        'is_ongoing' => true,
    ]);

    $this->branch = Branch::create([
        'name' => 'Kigali Branch',
        'location' => 'Kigali',
    ]);

    $this->course = Course::create([
        'course_name' => 'Computer Science',
        'abbreviation' => 'CS',
        'course_type' => 'Science',
        'course_code' => 'CS-101',
        'registration_fee' => 12500,
        'tuition_fee' => 100000,
        'practical_fee' => 30000,
        'trip_fee' => 15000,
    ]);

    config([
        'services.flutterwave.public_key' => 'test-public-key',
        'services.flutterwave.secret_key' => 'test-secret-key',
    ]);
});

it('uses Flutterwave Rwanda payment options and surfaces gateway rejection details', function () {
    $student = Student::create([
        'full_name' => 'Jane Doe',
        'sex' => 'Female',
        'email' => 'jane@example.com',
        'phone' => '0780000000',
        'branch_id' => $this->branch->id,
        'course_id' => $this->course->id,
        'reg_number' => 'REG-001',
        'password' => bcrypt('password'),
        'academic_year_id' => $this->academicYear->id,
    ]);

    Http::fake([
        'api.flutterwave.com/v3/payments' => Http::response([
            'status' => 'error',
            'message' => 'Invalid payment options',
        ], 400),
    ]);

    $response = $this->actingAs($student, 'student')->postJson(route('student.fees.pay.process'), [
        'fee_type' => 'school_fee',
        'pay_amount' => 5000,
    ]);

    $response->assertStatus(400)
        ->assertJsonPath('message', 'Flutterwave could not start this payment: Invalid payment options');

    Http::assertSent(function (HttpRequest $request) {
        return str_ends_with($request->url(), '/v3/payments')
            && $request['payment_options'] === 'card,mobilemoneyrwanda';
    });

    expect(StudentTransaction::query()->value('status'))->toBe('failed');
});

it('completes student registration through the public verified callback', function () {
    Storage::fake('public');
    Http::fake([
        'api.flutterwave.com/v3/payments' => Http::response([
            'status' => 'success',
            'data' => ['link' => 'https://checkout.flutterwave.com/test'],
        ]),
    ]);

    $response = $this->post(route('students.flutterwave.initialize'), [
        'amount' => 1,
        'total_fee' => 1,
        'fee_type' => 'registration_fee',
        'full_name' => 'Alex Student',
        'sex' => 'Other',
        'email' => 'alex@example.com',
        'phone' => '0780000000',
        'password' => 'password',
        'identification_type' => 'ID',
        'identification_number' => 'ID-12345',
        'education_level' => 's6',
        'branch_id' => $this->branch->id,
        'course_id' => $this->course->id,
        'academic_year_id' => $this->academicYear->id,
        'passport_photo' => UploadedFile::fake()->image('passport.png'),
    ], ['Accept' => 'application/json']);

    $response->assertOk()
        ->assertJsonPath('data.payment_link', 'https://checkout.flutterwave.com/test');

    $txRef = $response->json('data.transaction_reference');
    Http::assertSent(function (HttpRequest $request) use ($txRef) {
        return str_ends_with($request->url(), '/v3/payments')
            && (float) $request['amount'] === 12500.0
            && $request['currency'] === 'RWF'
            && $request['customer']['phonenumber'] === '+250780000000'
            && $request['redirect_url'] === route('students.flutterwave.callback')
            && $request['tx_ref'] === $txRef;
    });

    Http::fake([
        'api.flutterwave.com/v3/transactions/12345/verify' => Http::response([
            'status' => 'success',
            'data' => [
                'id' => 12345,
                'tx_ref' => $txRef,
                'status' => 'successful',
                'amount' => 12500,
                'currency' => 'RWF',
            ],
        ]),
    ]);

    $callbackResponse = $this->get(route('students.flutterwave.callback', [
        'tx_ref' => $txRef,
        'transaction_id' => 12345,
    ]));

    $callbackResponse->assertRedirect(route('student.dashboard'));
    $this->assertAuthenticated('student');

    $student = Student::where('email', 'alex@example.com')->firstOrFail();
    expect($student->academic_year_id)->toBe($this->academicYear->id)
        ->and($student->sex)->toBe('Other')
        ->and($student->id_number)->toBe('ID-12345')
        ->and($student->education_level)->toBe('s6')
        ->and($student->payed_registration_fees)->toBeTrue();

    expect(StudentTransaction::where('tx_ref', $txRef)->value('status'))->toBe('paid');
});
