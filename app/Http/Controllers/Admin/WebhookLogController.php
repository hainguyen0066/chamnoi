<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentWebhookLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Xem log webhook cổng thanh toán (IPN/return) do frontend ghi lại.
 * Giúp trả lời "vì sao giao dịch không được cộng xu".
 */
class WebhookLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = PaymentWebhookLog::query()
            ->with('deposit')
            ->when($request->filled('gateway'), function ($query) use ($request) {
                $query->where('gateway', $request->string('gateway'));
            })
            ->when($request->filled('event'), function ($query) use ($request) {
                $query->where('event', $request->string('event'));
            })
            ->when($request->filled('direction'), function ($query) use ($request) {
                $query->where('direction', $request->string('direction'));
            })
            ->when($request->filled('sig'), function ($query) use ($request) {
                $query->where('signature_valid', $request->string('sig') === '1');
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('transaction_id', 'like', '%' . $request->string('q') . '%');
            })
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date('from'));
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date('to'));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.webhook-logs.index', compact('logs'));
    }

    public function show(PaymentWebhookLog $webhookLog): View
    {
        $webhookLog->load('deposit.user');

        return view('admin.webhook-logs.show', ['log' => $webhookLog]);
    }
}
