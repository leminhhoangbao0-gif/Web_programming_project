<?php
namespace Atlas\Repositories;

use Atlas\Core\Database;

final class CartRepository
{
    public function getOrCreateCartId(int $userId): int
    {
        $pdo = Database::connection();
        $st = $pdo->prepare('SELECT id FROM carts WHERE user_id = ?');
        $st->execute([$userId]);
        $id = $st->fetchColumn();
        if ($id) return (int)$id;
        $pdo->prepare('INSERT INTO carts (user_id) VALUES (?)')->execute([$userId]);
        return (int)$pdo->lastInsertId();
    }

    public function items(int $cartId): array
    {
        $st = Database::connection()->prepare('SELECT product_id, quantity FROM cart_items WHERE cart_id = ? ORDER BY id');
        $st->execute([$cartId]);
        return array_map('intval', array_column($st->fetchAll(), 'quantity', 'product_id'));
    }

    public function setQuantity(int $cartId, int $productId, int $qty): void
    {
        $pdo = Database::connection();
        if ($qty <= 0) {
            $pdo->prepare('DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?')->execute([$cartId, $productId]);
            return;
        }
        $pdo->prepare('INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?,?,?) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)')
            ->execute([$cartId, $productId, $qty]);
    }

    public function clear(int $cartId): void
    {
        Database::connection()->prepare('DELETE FROM cart_items WHERE cart_id = ?')->execute([$cartId]);
    }
}
