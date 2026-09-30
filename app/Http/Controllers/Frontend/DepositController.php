<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\VnpayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function create(): View
    {
        return view('frontend.napthe', [
            'promotionPercents' => Deposit::PROMOTION_PERCENTS,
            'vndPerXu'          => Deposit::VND_PER_XU,
        ]);
    }

    public function store(Request $request, VnpayService $vnpay): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:10000', 'max:1000000000'],
        ]);

        $user   = $request->user();
        $amount = (int) $validated['amount'];

        $promotionPercent = match (true) {
            $amount >= 100_000_000 => 20,
            $amount >= 10_000_000  => 10,
            $amount >= 1_000_000   => 5,
            default                => 0,
        };

        $txnRef = 'VNPAY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $deposit = Deposit::create([
            'user_id'                => $user->id,
            'account'                => $user->email,
            'type'                   => 'xu',
            'method'                 => 'vnpay',
            'amount'                 => $amount,
            'promotion_percent'      => $promotionPercent,
            'amount_received'        => Deposit::calculateAmountReceived($amount, $promotionPercent),
            'status'                 => 'pending',
            'source'                 => 'gateway',
            'gateway_transaction_id' => $txnRef,
        ]);

        try {
            $paymentUrl = $vnpay->buildPaymentUrl(
                $txnRef,
                $amount,
                'Nap xu tai khoan ' . $user->id,
                $request->ip()
            );

            return redirect($paymentUrl);
        } catch (\RuntimeException $e) {
            $deposit->markFailed(['error' => $e->getMessage()]);

            return back()->with('error', $e->getMessage());
        }
    }
}
