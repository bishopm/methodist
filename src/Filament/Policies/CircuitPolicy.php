<?php

namespace Bishopm\Methodist\Filament\Policies;

use Bishopm\Methodist\Models\Circuit;
use Illuminate\Auth\Access\Response;

use Bishopm\Methodist\Models\User;

class CircuitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Circuit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Circuit $circuit): bool
    {
        return $user->checkPermissionTo('view Circuit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Circuit');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Circuit $circuit): bool
    {
        return $user->checkPermissionTo('update Circuit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Circuit $circuit): bool
    {
        return $user->checkPermissionTo('delete Circuit');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Circuit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Circuit $circuit): bool
    {
        return $user->checkPermissionTo('restore Circuit');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Circuit');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Circuit $circuit): bool
    {
        return $user->checkPermissionTo('replicate Circuit');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Circuit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Circuit $circuit): bool
    {
        return $user->checkPermissionTo('force-delete Circuit');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Circuit');
    }
}
