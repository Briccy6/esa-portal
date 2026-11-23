<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Student;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class AcademyKnowledgeBuilder
{
    public function build(User $user): string
    {
        return $this->buildForAuthenticatedAccount($user, 'web');
    }

    public function roleForAuthenticatedAccount(Authenticatable $account, string $guard): string
    {
        return match (strtolower($guard)) {
            'admin' => 'admin',
            'student' => 'student',
            'teacher' => 'teacher',
            'client' => 'client',
            'web' => 'applicant',
            'staff' => $this->staffRole($account),
            default => 'restricted',
        };
    }

    public function buildForAuthenticatedAccount(Authenticatable $account, string $guard): string
    {
        $role = $this->roleForAuthenticatedAccount($account, $guard);
        $roleKnowledge = config("ai_knowledge_base.roles.{$role}");
        if (!is_array($roleKnowledge)) {
            $role = 'restricted';
            $roleKnowledge = config('ai_knowledge_base.roles.restricted', []);
        }

        $guardrails = config('ai_knowledge_base.guardrails', []);
        $shared = config('ai_knowledge_base.shared_documentation', []);
        $permissions = $roleKnowledge['permissions'] ?? [];
        $status = $this->accountStatus($account, $guard);

        return implode("\n\n", [
            'IDENTITY AND SCOPE' . "\n"
                . 'You are the support assistant for '
                . config('ai_knowledge_base.application_name', 'Excellent Servant Academy')
                . '. Use only the application documentation and authorized workflows in this instruction.',
            "SECURITY AND RESPONSE RULES\n- " . implode("\n- ", array_merge($guardrails, $shared)),
            "AUTHENTICATED ACCOUNT\n"
                . '- Guard: ' . $guard . "\n"
                . '- Server-resolved role: ' . ($roleKnowledge['label'] ?? 'restricted account') . "\n"
                . '- Authorized capabilities for this role only:' . "\n- "
                . implode("\n- ", $permissions ?: ['No restricted workflow documentation is available.']),
            "CURRENT ACCOUNT STATUS\n- " . implode("\n- ", $status),
            "AUTHORIZED WORKFLOWS\n" . $this->formatWorkflows($roleKnowledge['workflows'] ?? []),
        ]);
    }

    private function staffRole(Authenticatable $account): string
    {
        if (!$account instanceof Staff) {
            return 'restricted';
        }

        return match (strtolower(trim((string) $account->getAttribute('role')))) {
            'accountant' => 'accountant',
            'dos' => 'dos',
            'it' => 'it',
            'sec', 'secretary' => 'secretary',
            default => 'restricted',
        };
    }

    private function accountStatus(Authenticatable $account, string $guard): array
    {
        if ($guard === 'student' && $account instanceof Student) {
            $missingFields = $account->missingProfileFields();

            return [
                'Profile completeness: ' . ($account->hasCompletedProfile() ? 'complete' : 'incomplete'),
                'Missing profile requirements: ' . ($missingFields === [] ? 'none' : implode(', ', $missingFields)),
                'Identification-document verification: ' . $this->verificationStatus(
                    $account->id_photo,
                    $account->id_photo_verification_status,
                    ['pending', 'approved']
                ),
                'Profile-photo verification: ' . $this->verificationStatus(
                    $account->passport_photo,
                    $account->passport_photo_verification_status,
                    ['pending', 'approved', 'rejected', 'manual_review']
                ),
                'Registration fee recorded as paid: ' . $this->yesNo($account->payed_registration_fees),
                'Enrollment term assigned: ' . $this->yesNo($account->enrollment_term_id !== null),
                'Student transaction statuses: ' . $this->studentTransactionStatusSummary($account),
                'Account blocked: ' . $this->yesNo($account->is_blocked),
                'Active subscription: ' . $this->activeSubscriptionStatus($account),
                'Fee-status limit: transaction statuses and the registration-fee flag do not establish that all term fees are paid.',
            ];
        }

        if ($guard === 'client' && $account instanceof Client) {
            return [
                'Account active: ' . $this->yesNo($account->is_active),
                'Account verification: ' . $this->allowedStatus(
                    $account->verification_status,
                    ['pending', 'verified'],
                    'unknown'
                ),
                'Email verified: ' . $this->yesNo($account->email_verified_at !== null),
                'Active subscription: ' . ($account->exists
                    ? $this->yesNo($account->hasActiveSubscription())
                    : 'unknown'),
            ];
        }

        if ($guard === 'web' && $account instanceof User) {
            return [
                'Email verified: ' . $this->yesNo($account->email_verified_at !== null),
                'Course assigned: ' . $this->yesNo($account->course_id !== null),
                'Academic year assigned: ' . $this->yesNo($account->academic_year_id !== null),
            ];
        }

        return ['No additional account status is exposed to the assistant.'];
    }

    private function verificationStatus(mixed $document, mixed $status, array $allowedStatuses): string
    {
        if (blank($document)) {
            return 'not submitted';
        }

        return $this->allowedStatus($status, $allowedStatuses, 'not confirmed');
    }

    private function allowedStatus(mixed $status, array $allowedStatuses, string $fallback): string
    {
        $normalized = strtolower(trim((string) $status));

        return in_array($normalized, $allowedStatuses, true) ? $normalized : $fallback;
    }

    private function yesNo(mixed $value): string
    {
        if ($value === null) {
            return 'unknown';
        }

        return (bool) $value ? 'yes' : 'no';
    }

    private function activeSubscriptionStatus(Student $student): string
    {
        if (!$student->exists) {
            return 'unknown';
        }

        return $student->hasActiveSubscription() ? 'yes' : 'no';
    }

    private function studentTransactionStatusSummary(Student $student): string
    {
        if (!$student->exists) {
            return 'unavailable';
        }

        $transactions = $student->transactions()
            ->select('type', 'status')
            ->selectRaw('COUNT(*) AS status_count')
            ->groupBy('type', 'status')
            ->get();

        if ($transactions->isEmpty()) {
            return 'no transaction records';
        }

        return $transactions
            ->map(function ($transaction): string {
                $type = $this->allowedStatus(
                    $transaction->type,
                    ['registration_fee', 'school_fee', 'trip_fee', 'practical_fee', 'student_card'],
                    'other fee'
                );
                $status = $this->allowedStatus(
                    $transaction->status,
                    ['pending', 'successful', 'paid', 'failed', 'refunded'],
                    'other'
                );

                return "{$type}: {$status} ({$transaction->status_count})";
            })
            ->implode('; ');
    }

    private function formatWorkflows(array $workflows): string
    {
        if ($workflows === []) {
            return '- No restricted workflow documentation is available for this account.';
        }

        $sections = [];
        foreach ($workflows as $workflow) {
            $steps = $workflow['steps'] ?? [];
            $numberedSteps = [];
            foreach ($steps as $index => $step) {
                $numberedSteps[] = ($index + 1) . '. ' . $step;
            }

            $sections[] = implode("\n", [
                '- ' . ($workflow['name'] ?? 'Workflow'),
                '  Prerequisites: ' . implode(' ', $workflow['prerequisites'] ?? ['Use the signed-in account and follow portal authorization.']),
                '  UI steps:',
                $numberedSteps === [] ? '    Use the navigation available to this account.' : '    ' . implode("\n    ", $numberedSteps),
            ]);
        }

        return implode("\n", $sections);
    }
}
