<?php

namespace App\Policies;

use App\Models\Queue;
use App\Models\User;

class QueuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff', 'Doctor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Queue $queue): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff', 'Doctor']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff']);
    }

    /**
     * Determine whether the user can call a queue.
     */
    public function call(User $user, Queue $queue): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff']) &&
               in_array($queue->status, ['waiting', 'skipped']);
    }

    /**
     * Determine whether the user can manage queue status.
     */
    public function manage(User $user, Queue $queue): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff']) &&
               in_array($queue->status, ['called', 'waiting']);
    }

    /**
     * Determine whether the user can recall a skipped queue.
     */
    public function recall(User $user, Queue $queue): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff']) &&
               $queue->status === 'skipped';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Queue $queue): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Queue $queue): bool
    {
        return $user->hasRole('Admin');
    }
}
