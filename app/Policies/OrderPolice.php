<?php

namespace App\Policies;

use app\DTOs\OrderData;
use app\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolice
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        if ($order->created_by !== $user->id) {
            return false;
        }

        if ($user->hasRole('super-admin') || $user->hasRole('manager')) {
            return true;
        }

        return $order->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        if ($order->status !== OrderStatus::PENDING) {
            return false;
        }
        if ($user->hasRole('manager') || $user->hasRole('super-admin')) {
            return true;
        }
        return $order->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return false;
    }

    public function confirm(User $user, Order $order): bool
    {
        return $order->status === OrderStatus::PENDING;
    }

    public function cancelPending(User $user, Order $order): bool
    {
        return $order->status === OrderStatus::PENDING;
    }

    public function cancelConfirmed(User $user, Order $order): bool
    {
        
        return $order->status === OrderStatus::CONFIRMED;
    }

    public function complete(User $user, Order $order): bool
    {
        return $order->status === OrderStatus::CONFIRMED;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }
}
