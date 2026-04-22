<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(private PlanService $plans) {}

    public function abacatepay(Request $request)
    {
        $payload = $request->all();
        $event   = $payload['event'] ?? null;
        $data    = $payload['data'] ?? [];

        Log::info('AbacatePay webhook', ['event' => $event]);

        try {
            match ($event) {
                'billing.paid' => $this->handleActivated($data),
                default        => null,
            };
        } catch (\Throwable $e) {
            Log::error('AbacatePay webhook error', ['event' => $event, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'processing_error'], 500);
        }

        return response()->json(['ok' => true]);
    }

    private function handleActivated(array $data): void
    {
        $sub = $this->findSubscription($data);
        if (! $sub) return;

        $this->plans->activatePro(
            $sub->user,
            $data['id'] ?? $sub->abacatepay_billing_id,
            $data['customer']['id'] ?? $sub->abacatepay_customer_id,
            isset($data['next_billing_at']) ? new \DateTime($data['next_billing_at']) : null,
        );
    }

    private function handleRenewed(array $data): void
    {
        $sub = $this->findSubscription($data);
        if (! $sub) return;

        $this->plans->renewPro(
            $sub,
            isset($data['next_billing_at']) ? new \DateTime($data['next_billing_at']) : null,
        );
    }

    private function handleCancelled(array $data): void
    {
        $sub = $this->findSubscription($data);
        if (! $sub) return;

        $this->plans->cancel($sub);
    }

    private function findSubscription(array $data): ?Subscription
    {
        $billingId = $data['id'] ?? null;

        if ($billingId) {
            $sub = Subscription::where('abacatepay_billing_id', $billingId)->first();
            if ($sub) return $sub;
        }

        // Fallback: match by user_id in metadata
        $userId = $data['metadata']['user_id'] ?? null;
        if ($userId) {
            $user = User::find($userId);
            return $user?->subscription;
        }

        return null;
    }
}
