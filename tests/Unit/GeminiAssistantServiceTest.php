<?php

namespace Tests\Unit;

use App\Models\Staff;
use App\Models\Student;
use App\Services\AcademyKnowledgeBuilder;
use App\Services\GeminiAssistantService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiAssistantServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gemini.api_key' => 'test-key',
            'services.gemini.model' => 'gemini-test',
        ]);
    }

    public function test_it_sends_only_student_knowledge_and_current_student_status_as_system_instruction(): void
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => 'Open your student profile.']],
                    ],
                ]],
            ]),
        ]);

        $student = new Student([
            'full_name' => 'Test Student',
            'phone' => '0780000000',
            'email' => 'student@example.test',
            'sex' => 'Female',
            'passport_photo' => 'profile.jpg',
            'identification_type' => 'ID',
            'id_number' => '1234567890123456',
            'id_photo' => 'id.png',
            'id_photo_verification_status' => 'pending',
            'passport_photo_verification_status' => 'approved',
            'education_level' => 'Secondary',
            'payed_registration_fees' => false,
            'enrollment_term_id' => 3,
            'is_blocked' => false,
        ]);

        $answer = app(GeminiAssistantService::class)->generateForAuthenticatedAccount(
            $student,
            'student',
            'Ndemeza nte indangamuntu yanjye?',
            [['role' => 'user', 'text' => 'I am completing registration.']]
        );

        $this->assertSame('Open your student profile.', $answer);
        Http::assertSent(function (Request $request): bool {
            $data = $request->data();
            $instruction = data_get($data, 'systemInstruction.parts.0.text', '');
            $contents = $data['contents'];

            return str_contains($instruction, 'Server-resolved role: Student account')
                && str_contains($instruction, 'Identification-document verification: pending')
                && str_contains($instruction, 'Registration fee recorded as paid: no')
                && str_contains($instruction, '/student/profile')
                && str_contains($instruction, 'same language')
                && str_contains($instruction, 'Politely decline general knowledge')
                && !str_contains($instruction, '/admin/dashboard')
                && !str_contains($instruction, 'account-locking controls authorized for IT staff')
                && $contents[0]['role'] === 'user'
                && $contents[1]['role'] === 'user'
                && $contents[1]['parts'][0]['text'] === 'Ndemeza nte indangamuntu yanjye?';
        });
    }

    public function test_it_resolves_staff_subroles_without_injecting_other_role_workflows(): void
    {
        $builder = app(AcademyKnowledgeBuilder::class);
        $accountant = new Staff(['role' => 'Accountant']);

        $instruction = $builder->buildForAuthenticatedAccount($accountant, 'staff');

        $this->assertStringContainsString('Staff account with Accountant role', $instruction);
        $this->assertStringContainsString('/staff/accountant/dashboard', $instruction);
        $this->assertStringNotContainsString('/staff/it', $instruction);
        $this->assertStringNotContainsString('Create a teacher assignment', $instruction);
    }

}
