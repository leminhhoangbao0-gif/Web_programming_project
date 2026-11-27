<?php
namespace Atlas\Services;

use Atlas\Core\Database;
use Atlas\Models\{Order, Payment};
use Atlas\Repositories\{OrderRepository, PaymentRepository};

final class PaymentService
{
    public function methods(): array
    {
        return ['cod' => 'Cash on delivery', 'bank_qr' => 'Bank transfer / QR', 'mock_ewallet' => 'E-wallet (mock)'];
    }

    public function simulate(Order $order, string $outcome): Payment
    {
        if ($order->paymentMethod === 'cod' || $order->paymentStatus === 'paid' || $order->status === 'cancelled') {
            throw new \DomainException('PAYMENT_NOT_ALLOWED');
        }
        $payments = new PaymentRepository();
        return Database::transaction(function () use ($order, $outcome, $payments) {
            $latest = $payments->latestForOrder($order->id);
            $paymentId = ($latest && $latest->status === 'pending') ? $latest->id
                : $payments->create($order->id, $order->paymentMethod, $order->total);
            $ok = $outcome === 'success';
            $ref = 'SIM-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $payments->updateStatus($paymentId, $ok ? 'success' : 'failed', $ref);
            (new OrderRepository())->setPaymentStatus($order->id, $ok ? 'paid' : 'failed');
            return $payments->latestForOrder($order->id);
        });
    }
}
