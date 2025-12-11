<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TelegramNotification;
use Illuminate\Auth\Access\HandlesAuthorization;

class TelegramNotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_telegram::notification');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TelegramNotification $telegramNotification): bool
    {
        return $user->can('view_telegram::notification');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_telegram::notification');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TelegramNotification $telegramNotification): bool
    {
        return $user->can('update_telegram::notification');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TelegramNotification $telegramNotification): bool
    {
        return $user->can('delete_telegram::notification');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_telegram::notification');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, TelegramNotification $telegramNotification): bool
    {
        return $user->can('force_delete_telegram::notification');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_telegram::notification');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, TelegramNotification $telegramNotification): bool
    {
        return $user->can('restore_telegram::notification');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_telegram::notification');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, TelegramNotification $telegramNotification): bool
    {
        return $user->can('replicate_telegram::notification');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_telegram::notification');
    }
}
