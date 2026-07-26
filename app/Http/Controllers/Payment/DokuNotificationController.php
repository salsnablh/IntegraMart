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
        $invoiceNumber = data_get($payload, 'order.invoice_number');
        $transactionStatus = strtoupper((string) data_get($payload, 'transaction.status', ''));

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

        if ($payment->status === 'success') {
            return response()->json(['message' => 'OK']);
        }

        DB::transaction(function () use ($payment, $payload, $transactionStatus): void {
            $payment->loadMissing('order');
            $order = $payment->order;

            if ($transactionStatus === 'SUCCESS') {
                $paidAt = data_get($payload, 'transaction.date');
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => $paidAt ? Carbon::parse($paidAt) : now(),
                    'raw_response' => $payload,
                ]);

                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'completed',
                    ]);
                }

                return;
            }

            if ($transactionStatus === 'EXPIRED' || strtoupper((string) data_get($payload, 'order.status', '')) === 'ORDER_EXPIRED') {
                $payment->update([
                    'status' => 'expired',
                    'raw_response' => $payload,
                ]);

                if ($order) {
                    $order->update([
                        'payment_status' => 'expired',
                        'status' => 'expired',
                    ]);
                }

                return;
            }

            if ($transactionStatus === 'PENDING') {
                $payment->update([
                    'status' => 'pending',
                    'raw_response' => $payload,
                ]);

                return;
            }

            $payment->update([
                'raw_response' => $payload,
            ]);
        });

        return response()->json(['message' => 'OK']);
    }
}