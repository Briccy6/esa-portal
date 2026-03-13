<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExamPolicy
{
    /**
     * Determine whether the user can view any exams.
     */
    public function viewAny(User $user): bool
    {
        return $user->admin || $user->teacher || $user->staff;
    }

    /**
     * Determine whether the user can view the exam.
     */
    public function view(User $user, Exam $exam): bool
    {
        if ($user->admin) {
            return true;
        }

        if ($user->teacher) {
            // Teacher can view exams they created or are assigned to
            return $exam->teacher_id === $user->teacher->id ||
                   $exam->courses()->whereHas('teachers', function($query) use ($user) {
                       $query->where('teachers.id', $user->teacher->id);
                   })->exists() ||
                   $exam->lessons()->whereHas('teacher', function($query) use ($user) {
                       $query->where('teachers.id', $user->teacher->id);
                   })->exists();
        }

        if ($user->staff) {
            return true; // Staff can view all exams for administrative purposes
        }

        return false;
    }

    /**
     * Determine whether the user can create exams.
     */
    public function create(User $user): bool
    {
        return $user->admin || $user->teacher;
    }

    /**
     * Determine whether the user can update the exam.
     */
    public function update(User $user, Exam $exam): bool
    {
        if ($user->admin) {
            return true;
        }

        if ($user->teacher) {
            // Teacher can update exams they created or are assigned to
            return $exam->teacher_id === $user->teacher->id ||
                   $exam->courses()->whereHas('teachers', function($query) use ($user) {
                       $query->where('teachers.id', $user->teacher->id);
                   })->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the exam.
     */
    public function delete(User $user, Exam $exam): bool
    {
        if ($user->admin) {
            return true;
        }

        if ($user->teacher) {
            // Teacher can delete exams they created
            return $exam->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine whether the user can manage exam questions.
     */
    public function manage(User $user, Exam $exam): bool
    {
        return $this->update($user, $exam);
    }

    /**
     * Determine whether the user can view exam results.
     */
    public function viewResults(User $user, Exam $exam): bool
    {
        if ($user->admin) {
            return true;
        }

        if ($user->teacher) {
            return $this->update($user, $exam);
        }

        if ($user->staff) {
            return true; // Staff can view results for administrative purposes
        }

        return false;
    }

    /**
     * Determine whether the user can view exam analytics.
     */
    public function viewAnalytics(User $user, Exam $exam): bool
    {
        if ($user->admin) {
            return true;
        }

        if ($user->teacher) {
            return $this->update($user, $exam);
        }

        return false;
    }

    /**
     * Determine whether the user can export exam results.
     */
    public function export(User $user, Exam $exam): bool
    {
        return $this->viewResults($user, $exam);
    }

    /**
     * Determine whether the user can grade exams.
     */
    public function grade(User $user, Exam $exam): bool
    {
        if ($user->admin) {
            return true;
        }

        if ($user->teacher) {
            return $this->update($user, $exam);
        }

        return false;
    }

    /**
     * Determine whether the user can release exam results.
     */
    public function releaseResults(User $user, Exam $exam): bool
    {
        return $this->update($user, $exam);
    }
}
