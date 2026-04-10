<?php

namespace App\Policies;

use App\Models\Babyeyi;
use App\Models\Staff;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class BabyeyiPolicy
{
    /**
     * Determine whether the user can view any babyeyi.
     */
    public function viewAny(Staff|Admin $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the babyeyi.
     */
    public function view(Staff|Admin $user, Babyeyi $babyeyi): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create babyeyi.
     */
    public function create(Staff|Admin $user): bool
    {
        // Admins can always create
        if ($user instanceof Admin) {
            return true;
        }

        // Staff secretaries can create
        if ($user instanceof Staff) {
            return $user->role === 'secretary' || $user->role === 'sec';
        }

        return false;
    }

    /**
     * Determine whether the user can update the babyeyi.
     */
    public function update(Staff|Admin $user, Babyeyi $babyeyi): bool
    {
        // Admin can always update
        if ($user instanceof Admin) {
            return true;
        }

        // Staff can only update their own
        if ($user instanceof Staff) {
            return $babyeyi->created_by === $user->id && ($user->role === 'secretary' || $user->role === 'sec');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the babyeyi.
     */
    public function delete(Staff|Admin $user, Babyeyi $babyeyi): bool
    {
        // Admin can always delete
        if ($user instanceof Admin) {
            return true;
        }

        // Staff can only delete their own draft/preview babyeyi
        if ($user instanceof Staff) {
            return $babyeyi->created_by === $user->id &&
                   in_array($babyeyi->status, ['draft', 'preview']) &&
                   ($user->role === 'secretary' || $user->role === 'sec');
        }

        return false;
    }

    /**
     * Determine whether the user can restore the babyeyi.
     */
    public function restore(Staff|Admin $user, Babyeyi $babyeyi): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can permanently delete the babyeyi.
     */
    public function forceDelete(Staff|Admin $user, Babyeyi $babyeyi): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can publish the babyeyi.
     */
    public function publish(Staff|Admin $user, Babyeyi $babyeyi): bool
    {
        // Admin can always publish
        if ($user instanceof Admin) {
            return true;
        }

        // Staff secretary can publish their own
        if ($user instanceof Staff) {
            return $babyeyi->created_by === $user->id && ($user->role === 'secretary' || $user->role === 'sec');
        }

        return false;
    }

    /**
     * Determine whether the user can download the babyeyi.
     */
    public function download(Staff|Admin $user, Babyeyi $babyeyi): bool
    {
        return true;
    }
}
