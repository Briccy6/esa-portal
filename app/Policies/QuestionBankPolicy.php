<?php

namespace App\Policies;

use App\Models\QuestionBank;
use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Teacher;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class QuestionBankPolicy
{
    /**
     * Determine whether the user can view any question banks.
     * User may be Admin, Staff, or Teacher (multi-guard auth).
     */
    public function viewAny(Authenticatable $user): bool
    {
        return $user instanceof Admin || $user instanceof Teacher || $user instanceof Staff;
    }

    /**
     * Determine whether the user can view the question bank.
     */
    public function view(Authenticatable $user, QuestionBank $questionBank): bool
    {
        if ($user instanceof Admin || $user instanceof Staff) {
            return true;
        }

        if ($user instanceof Teacher) {
            return $questionBank->teacher_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create question banks.
     */
    public function create(Authenticatable $user): bool
    {
        return $user instanceof Admin || $user instanceof Teacher;
    }

    /**
     * Determine whether the user can update the question bank.
     */
    public function update(Authenticatable $user, QuestionBank $questionBank): bool
    {
        if ($user instanceof Admin) {
            return true;
        }

        if ($user instanceof Teacher) {
            return $questionBank->teacher_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the question bank.
     */
    public function delete(Authenticatable $user, QuestionBank $questionBank): bool
    {
        if ($user instanceof Admin) {
            return true;
        }

        if ($user instanceof Teacher) {
            return $questionBank->teacher_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can add questions to the question bank.
     */
    public function addQuestion(Authenticatable $user, QuestionBank $questionBank): bool
    {
        return $this->update($user, $questionBank);
    }

    /**
     * Determine whether the user can import questions to the question bank.
     */
    public function import(Authenticatable $user, QuestionBank $questionBank): bool
    {
        return $this->update($user, $questionBank);
    }

    /**
     * Determine whether the user can export questions from the question bank.
     */
    public function export(Authenticatable $user, QuestionBank $questionBank): bool
    {
        return $this->view($user, $questionBank);
    }
}
