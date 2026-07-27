<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\DokuCheckoutService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DokuNotificationController extends Controller
{
    public function __invoke(Request $request, DokuCheckoutService $dokuCheckoutService): JsonResponse
    {
        if (! $dokuCheckoutService->verifyNotification($request, '/payments/doku/notification')) {
            return response()->json([
                'message' => 'Invalid signature',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $payload = $request->json()->all();
        $invoiceNumber = data_get($payload, 'order.invoice_number')
            ?? data_get($payload, 'response.order.invoice_number')
            ?? data_get($payload, 'order.invoice');

        if (! $invoiceNumber) {
            return response()->json([
                'message' => 'Invoice number is required',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $payment = Payment::query()
            ->where('external_id', $invoiceNumber)
            ->latest()
            ->first();

        if (! $payment) {
            $order = Order::query()
                ->where('order_number', $invoiceNumber)
                ->orWhere('order_code', $invoiceNumber)
                ->first();

            $payment = $order?->payments()->latest()->first();
        }

        if (! $payment) {
            return response()->json([
                'message' => 'Payment not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if (in_array($payment->status, ['paid', 'success'], true)) {
            return response()->json(['message' => 'OK']);
        }

        $dokuCheckoutService->applyPaymentStatus($payment, $payload);

        return response()->json(['message' => 'OK']);
    }
}

