<?php

namespace App\Http\Controllers\Web;

use App\Enums\Plan;
use App\Http\Controllers\Controller;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function __construct(private PlanService $plans) {}

    /** Billing overview page (inside settings). */
    public function index()
    {
        $user  = Auth::user();
        $sub   = $this->plans->ensureSubscription($user);
        $plan  = $this->plans->plan($user);
        $usage = $this->plans->usage($user);

        return view('billing.index', compact('user', 'sub', 'plan', 'usage'));
    }

    /** Create an AbacatePay billing and redirect to payment URL. */
    public function checkout(Request $request, string $planSlug)
    {
        $user = Auth::user();

        $period = match ($planSlug) {
            'pro-monthly' => 'monthly',
            'pro-annual'  => 'annual',
            Plan::Pro->value => 'monthly', // legacy fallback
            default => null,
        };

        if ($period === null) {
            abort(404);
        }

        if ($user->onPro()) {
            return redirect()->route('billing')->with('info', 'Você já possui o plano Pro.');
        }

        try {
            \AbacatePay\Clients\Client::setToken(config('services.abacatepay.token'));

            $billingClient  = new \AbacatePay\Clients\BillingClient();
            $customerClient = new \AbacatePay\Clients\CustomerClient();

            // Create or retrieve customer
            $customer = $customerClient->create(new \AbacatePay\Resources\Customer([
                'name'  => $user->name,
                'email' => $user->email,
            ]));

            $billing = $billingClient->create(new \AbacatePay\Resources\Billing([
                'frequency' => \AbacatePay\Enums\Billing\Frequencies::ONE_TIME,
                'methods'   => [
                    \AbacatePay\Enums\Billing\Methods::PIX,
                ],
                'products' => [
                    new \AbacatePay\Resources\Billing\Product([
                        'external_id' => 'taskletto-pro-' . $period,
                        'name'        => $period === 'annual' ? 'Taskletto Pro (Anual)' : 'Taskletto Pro (Mensal)',
                        'description' => $period === 'annual'
                            ? 'Acesso ilimitado ao Taskletto Pro — plano anual (R$ 9,99/mês)'
                            : 'Acesso ilimitado ao Taskletto Pro — plano mensal',
                        'quantity'    => 1,
                        'price'       => Plan::Pro->priceCents($period),
                    ]),
                ],
                'customer'    => $customer,
                'return_url'  => route('billing.success'),
                'cancel_url'  => route('billing'),
                'metadata'    => ['user_id' => $user->id],
            ]));

            // Store the billing ID on the subscription for webhook matching
            $sub = $this->plans->ensureSubscription($user);
            $sub->update([
                'abacatepay_billing_id'  => $billing->id,
                'abacatepay_customer_id' => $customer->id,
            ]);

            return redirect($billing->url);

        } catch (\Throwable $e) {
            Log::error('AbacatePay checkout error', ['error' => $e->getMessage(), 'user' => $user->id]);
            return back()->with('error', 'Erro ao criar sessão de pagamento. Tente novamente.');
        }
    }

    /** Return page after successful payment. */
    public function success()
    {
        return view('billing.success');
    }

    /** Cancel the Pro subscription. */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $sub  = $user->subscription;

        if (! $sub || ! $sub->isActive() || $user->plan() === Plan::Free) {
            return back()->with('error', 'Nenhuma assinatura ativa para cancelar.');
        }

        $this->plans->cancel($sub);

        return back()->with('success', 'Assinatura cancelada. Você continua com acesso Pro até o fim do período atual.');
    }
}
