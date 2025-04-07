<?php

namespace Bishopm\Methodist\Filament\Policies;

use Bishopm\Methodist\Models\Society;
use Illuminate\Auth\Access\Response;

use Bishopm\Methodist\Models\User;

class SocietyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Society');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Society $society): bool
    {
        return $user->checkPermissionTo('view Society');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Society');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Society $society): bool
    {
        return $user->checkPermissionTo('update Society');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Society $society): bool
    {
        return $user->checkPermissionTo('delete Society');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Society');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Society $society): bool
    {
        return $user->checkPermissionTo('restore Society');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Society');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Society $society): bool
    {
        return $user->checkPermissionTo('replicate Society');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Society');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Society $society): bool
    {
        return $user->checkPermissionTo('force-delete Society');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Society');
    }
}
