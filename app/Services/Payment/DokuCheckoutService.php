<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class DokuCheckoutService
{
    public function createCheckoutPayment(Order $order): array
    {
        $requestId = (string) Str::uuid();
        $requestTimestamp = now('UTC')->format('Y-m-d\TH:i:s\Z');
        $requestTarget = '/checkout/v1/payment';
        $payload = $this->buildPayload($order);
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);

        if ($body === false) {
            throw new RuntimeException('Gagal membentuk payload DOKU.');
        }

        $digest = base64_encode(hash('sha256', $body, true));
        $signature = $this->buildSignature($requestId, $requestTimestamp, $requestTarget, $digest);

        try {
            $response = Http::baseUrl($this->baseUrl())
                ->acceptJson()
                ->withHeaders([
                    'Client-Id' => $this->clientId(),
                    'Request-Id' => $requestId,
                    'Request-Timestamp' => $requestTimestamp,
                    'Signature' => $signature,
                    'Digest' => $digest,
                ])
                ->withBody($body, 'application/json')
                ->post($requestTarget);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Gagal terhubung ke DOKU: '.$exception->getMessage(), 0, $exception);
        }

        $responseData = $response->json();

        if (! $response->successful()) {
            $message = data_get($responseData, 'error_messages.0')
                ?? data_get($responseData, 'message.0')
                ?? $response->body();

            throw new RuntimeException('DOKU menolak request pembayaran: '.$message);
        }

        if (data_get($responseData, 'message.0') !== 'SUCCESS') {
            $message = is_array(data_get($responseData, 'message'))
                ? implode(', ', data_get($responseData, 'message'))
                : (string) data_get($responseData, 'message', 'Unknown error');

            throw new RuntimeException('DOKU mengembalikan status tidak berhasil: '.$message);
        }

        return [
            'request_id' => $requestId,
            'request_timestamp' => $requestTimestamp,
            'request_target' => $requestTarget,
            'signature' => $signature,
            'digest' => $digest,
            'payload' => $payload,
            'body' => $body,
            'response' => $responseData,
            'payment_url' => data_get($responseData, 'response.payment.url'),
            'expired_at' => $this->parseExpiredAt(data_get($responseData, 'response.payment.expired_date')),
        ];
    }

    public function checkPaymentStatus(string $invoiceNumber): array
    {
        $requestId = (string) Str::uuid();
        $requestTimestamp = now('UTC')->format('Y-m-d\TH:i:s\Z');
        $requestTarget = '/orders/v1/status/'.rawurlencode($invoiceNumber);
        $signature = $this->buildSignature($requestId, $requestTimestamp, $requestTarget);

        try {
            $response = Http::baseUrl($this->baseUrl())
                ->acceptJson()
                ->withHeaders([
                    'Client-Id' => $this->clientId(),
                    'Request-Id' => $requestId,
                    'Request-Timestamp' => $requestTimestamp,
                    'Signature' => $signature,
                ])
                ->get($requestTarget);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Gagal mengecek status DOKU: '.$exception->getMessage(), 0, $exception);
        }

        $responseData = $response->json() ?? [];

        if (! $response->successful()) {
            $message = data_get($responseData, 'error_messages.0')
                ?? data_get($responseData, 'message.0')
                ?? $response->body();

            throw new RuntimeException('DOKU menolak cek status pembayaran: '.$message);
        }

        return $responseData;
    }

    public function applyPaymentStatus(Payment $payment, array $payload): void
    {
        $transactionStatus = strtoupper((string) (
            data_get($payload, 'transaction.status')
            ?? data_get($payload, 'transaction.status_code')
            ?? data_get($payload, 'response.transaction.status')
            ?? data_get($payload, 'response.transaction.status_code')
            ?? data_get($payload, 'status')
        ));
        $orderStatus = strtoupper((string) (
            data_get($payload, 'order.status')
            ?? data_get($payload, 'response.order.status')
        ));

        DB::transaction(function () use ($payment, $payload, $transactionStatus, $orderStatus): void {
            $payment->loadMissing('order');
            $order = $payment->order;

            if (in_array($transactionStatus, ['SUCCESS', 'PAID', 'SETTLEMENT', 'CAPTURE', '00'], true) || in_array($orderStatus, ['SUCCESS', 'PAID', 'ORDER_PAID'], true)) {
                $paidAt = data_get($payload, 'transaction.date') ?? data_get($payload, 'response.transaction.date');
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

            if (in_array($transactionStatus, ['EXPIRED', 'ORDER_EXPIRED'], true) || $orderStatus === 'ORDER_EXPIRED') {
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

            if (in_array($transactionStatus, ['PENDING', '01'], true)) {
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
    }
    public function verifyNotification(Request $request, string $requestTarget): bool
    {
        $clientId = (string) $request->header('Client-Id', '');
        $requestId = (string) $request->header('Request-Id', '');
        $requestTimestamp = (string) $request->header('Request-Timestamp', '');
        $signature = (string) $request->header('Signature', '');
        $body = $request->getContent();

        if ($clientId === '' || $requestId === '' || $requestTimestamp === '' || $signature === '') {
            return false;
        }

        if ($clientId !== $this->clientId()) {
            return false;
        }

        $digest = base64_encode(hash('sha256', $body, true));
        $expectedSignature = $this->buildSignature($requestId, $requestTimestamp, $requestTarget, $digest);

        return hash_equals($expectedSignature, $signature);
    }

    private function buildPayload(Order $order): array
    {
        $order->loadMissing(['items.product', 'customer', 'user']);

        $customer = $order->customer;

        return [
            'order' => [
                'amount' => (int) round((float) ($order->total_amount ?? $order->grand_total)),
                'invoice_number' => $order->order_number ?? $order->order_code,
                'currency' => 'IDR',
                'auto_redirect' => true,
                'callback_url' => route('orders.show', $order),
                'callback_url_cancel' => route('orders.show', $order),
                'callback_url_result' => route('orders.show', $order),
                'notification_url' => config('services.doku.notification_url') ?: route('payments.doku.notification'),
                'line_items' => $order->items->map(function ($item): array {
                    $product = $item->product;

                    return [
                        'id' => (string) $item->product_id,
                        'name' => $product?->name ?? 'Produk',
                        'quantity' => (int) $item->qty,
                        'price' => (int) round((float) $item->price),
                        'sku' => $product?->sku ?? (string) $item->product_id,
                        'category' => $product?->category ?? optional($product?->categoryModel)->name ?? 'general',
                    ];
                })->values()->all(),
            ],
            'payment' => [
                'payment_due_date' => (int) config('services.doku.payment_due_date', 60),
            ],
            'customer' => [
                'id' => 'customer-'.$order->customer_id,
                'name' => $order->recipient_name ?: $customer?->name ?: $order->user?->name,
                'email' => $order->user?->email ?? $customer?->email,
                'phone' => $order->phone ?? $order->recipient_phone ?? $customer?->phone,
                'address' => $order->shipping_address,
                'city' => $order->shipping_city,
                'country' => 'ID',
                'postcode' => $order->shipping_postal_code,
            ],
        ];
    }

    private function buildSignature(string $requestId, string $requestTimestamp, string $requestTarget, ?string $digest = null): string
    {
        $signatureParts = [
            'Client-Id:'.$this->clientId(),
            'Request-Id:'.$requestId,
            'Request-Timestamp:'.$requestTimestamp,
            'Request-Target:'.$requestTarget,
        ];

        if ($digest !== null) {
            $signatureParts[] = 'Digest:'.$digest;
        }

        $stringToSign = implode("\n", $signatureParts);

        return 'HMACSHA256='.base64_encode(hash_hmac('sha256', $stringToSign, $this->secretKey(), true));
    }

    private function parseExpiredAt(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        return Carbon::createFromFormat('YmdHis', $value, 'Asia/Jakarta');
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.doku.base_url', 'https://api-sandbox.doku.com'), '/');
    }

    private function clientId(): string
    {
        $clientId = (string) config('services.doku.client_id', '');

        if ($clientId === '') {
            throw new RuntimeException('DOKU client ID belum diatur.');
        }

        return $clientId;
    }

    private function secretKey(): string
    {
        $secretKey = (string) config('services.doku.secret_key', '');

        if ($secretKey === '') {
            throw new RuntimeException('DOKU secret key belum diatur.');
        }

        return $secretKey;
    }
}




