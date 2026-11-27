<?php
namespace Atlas\Repositories;

use Atlas\Core\Database;
use Atlas\Models\Payment;

final class PaymentRepository
{
    public function create(int $orderId, string $method, int $amount, string $status = 'pending'): int
    {
        Database::connection()->prepare('INSERT INTO payments (order_id, method, amount, status) VALUES (?,?,?,?)')
            ->execute([$orderId, $method, $amount, $status]);
        return (int)Database::connection()->lastInsertId();
    }

    public function latestForOrder(int $orderId): ?Payment
    {
        $st = Database::connection()->prepare('SELECT * FROM payments WHERE order_id = ? ORDER BY id DESC LIMIT 1');
        $st->execute([$orderId]);
        $row = $st->fetch();
        return $row ? Payment::fromRow($row) : null;
    }

    public function listForOrder(int $orderId): array
    {
        $st = Database::connection()->prepare('SELECT * FROM payments WHERE order_id = ? ORDER BY id');
        $st->execute([$orderId]);
        return array_map([Payment::class, 'fromRow'], $st->fetchAll());
    }

    public function updateStatus(int $paymentId, string $status, ?string $transactionRef = null): void
    {
        Database::connection()->prepare('UPDATE payments SET status = ?, transaction_ref = COALESCE(?, transaction_ref) WHERE id = ?')
            ->execute([$status, $transactionRef, $paymentId]);
    }
}
