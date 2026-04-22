<?php

namespace App\Services;

use App\Enums\Plan;
use App\Models\Subscription;
use App\Models\User;

class PlanService
{
    /** Returns the user's current active plan (falls back to Free). */
    public function plan(User $user): Plan
    {
        $sub = $user->subscription;

        if (! $sub || ! $sub->isActive() || $sub->isExpired()) {
            return Plan::Free;
        }

        return $sub->plan;
    }

    /** Returns current usage counts for the user. */
    public function usage(User $user): array
    {
        return [
            'tasks'      => $user->tasks()->count(),
            'notes'      => $user->notes()->count(),
            'categories' => $user->categories()->count(),
            'storage_mb' => $this->getStorageMbInUse($user),
        ];
    }

    /**
     * Calculates the estimated storage in use by the user in Megabytes.
     * Evaluates the Avatar file size and the length of Note contents.
     */
    public function getStorageMbInUse(User $user): float
    {
        $bytes = 0;

        // 1. Avatar size
        if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
            $path = str_replace('/storage/', '', $user->avatar);
            if (\Storage::disk('public')->exists($path)) {
                $bytes += \Storage::disk('public')->size($path);
            }
        }

        // 2. Notes content size (bytes approximation from text length)
        $notesBytes = \DB::table('notes')
            ->where('user_id', $user->id)
            ->selectRaw('SUM(LENGTH(content)) as size')
            ->value('size');

        $bytes += (int) $notesBytes;

        return round($bytes / (1024 * 1024), 2);
    }

    /**
     * Checks if the user can create another resource.
     * Returns true if allowed, false if limit reached.
     */
    public function canCreate(User $user, string $resource): bool
    {
        $plan  = $this->plan($user);
        $limit = $plan->limit($resource);

        if ($limit === null) {
            return true; // unlimited
        }

        $count = match ($resource) {
            'tasks'      => $user->tasks()->count(),
            'notes'      => $user->notes()->count(),
            'categories' => $user->categories()->count(),
            default      => 0,
        };

        return $count < $limit;
    }

    /** Returns a human-readable limit message. */
    public function limitMessage(string $resource): string
    {
        return match ($resource) {
            'tasks'      => 'Você atingiu o limite de tarefas do seu plano. Delete algumas tarefas para continuar ou faça o upgrade para o Pro.',
            'notes'      => 'Você atingiu o limite de notas do seu plano.',
            'categories' => 'Você atingiu o limite de categorias do seu plano.',
            'storage_mb' => 'Limita de armazenamento cheio! Por favor, faça um upgrade no plano para anexar mais ficheiros.',
            default      => 'Limite do plano atingido.',
        };
    }

    /** Ensures a user has a subscription record (creates Free if missing). */
    public function ensureSubscription(User $user): Subscription
    {
        return $user->subscription ?? Subscription::create([
            'user_id' => $user->id,
            'plan'    => Plan::Free,
            'status'  => 'active',
        ]);
    }

    /** Activates a Pro subscription from an AbacatePay billing event. */
    public function activatePro(
        User   $user,
        string $billingId,
        string $customerId,
        ?\DateTimeInterface $periodEndsAt = null
    ): Subscription {
        $sub = $this->ensureSubscription($user);

        $sub->update([
            'plan'                   => Plan::Pro,
            'status'                 => 'active',
            'abacatepay_billing_id'  => $billingId,
            'abacatepay_customer_id' => $customerId,
            'current_period_ends_at' => $periodEndsAt ?? now()->addMonth(),
            'cancelled_at'           => null,
        ]);

        return $sub->fresh();
    }

    /** Renews the current period by one month. */
    public function renewPro(Subscription $sub, ?\DateTimeInterface $periodEndsAt = null): void
    {
        $sub->update([
            'status'                 => 'active',
            'current_period_ends_at' => $periodEndsAt ?? now()->addMonth(),
        ]);
    }

    /** Cancels the subscription at period end. */
    public function cancel(Subscription $sub): void
    {
        $sub->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
