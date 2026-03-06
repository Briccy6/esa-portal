<?php

namespace App\Policies;

use App\Models\QuestionBank;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuestionBankPolicy
{
    /**
     * Determine whether the user can view any question banks.
     */
    public function viewAny(User $user): bool
    {
        return $user->admin || $user->teacher || $user->staff;
    }

    /**
     * Determine whether the user can view the question bank.
     */
    public function view(User $user, QuestionBank $questionBank): bool
    {
        if ($user->admin) {
            return true;
        }

        if ($user->teacher) {
            // Teacher can view their own question banks
            return $questionBank->teacher_id === $user->teacher->id;
        }

        if ($user->staff) {
            return true; // Staff can view all question banks for administrative purposes
        }

        return false;
    }

    /**
     * Determine whether the user can create question banks.
     */
    public function create(User $user): bool
    {
        return $user->admin || $user->teacher;
    }

    /**
     * Determine whether the user can update the question bank.
     */
    public function update(User $user, QuestionBank $questionBank): bool
    {
        if ($user->admin) {
            return true;
        }

        if ($user->teacher) {
            // Teacher can update their own question banks
            return $questionBank->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the question bank.
     */
    public function delete(User $user, QuestionBank $questionBank): bool
    {
        if ($user->admin) {
            return true;
        }

        if ($user->teacher) {
            // Teacher can delete their own question banks
            return $questionBank->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine whether the user can add questions to the question bank.
     */
    public function addQuestion(User $user, QuestionBank $questionBank): bool
    {
        return $this->update($user, $questionBank);
    }

    /**
     * Determine whether the user can import questions to the question bank.
     */
    public function import(User $user, QuestionBank $questionBank): bool
    {
        return $this->update($user, $questionBank);
    }

    /**
     * Determine whether the user can export questions from the question bank.
     */
    public function export(User $user, QuestionBank $questionBank): bool
    {
        return $this->view($user, $questionBank);
    }
}
