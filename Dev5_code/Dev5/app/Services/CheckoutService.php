<?php
namespace Atlas\Services;

use Atlas\Core\{Config, Database};
use Atlas\Models\Order;
use Atlas\Repositories\{AddressRepository, CartRepository, OrderRepository, PaymentRepository};

final class CheckoutService
{
    public function summary(int $userId): array
    {
        $cart = (new CartService())->getCart($userId);
        $subtotal = $cart->subtotal();
        $fee = $cart->isEmpty() ? 0 : (int)Config::get('shop.shipping_fee', 30000);
        return [
            'cart' => $cart, 'subtotal' => $subtotal, 'shipping_fee' => $fee, 'discount' => 0,
            'total' => $subtotal + $fee, 'addresses' => (new AddressRepository())->listByUser($userId),
        ];
    }

    public function placeOrder(int $userId, array $shipping, string $paymentMethod): Order
    {
        if (!array_key_exists($paymentMethod, (new PaymentService())->methods())) throw new \DomainException('INVALID_PAYMENT_METHOD');

        $orderId = Database::transaction(function () use ($userId, $shipping, $paymentMethod) {
            $cart = (new CartService())->getCart($userId);
            if ($cart->isEmpty()) throw new \DomainException('CART_EMPTY');
            foreach ($cart->items as $i) if ($i->adjusted) throw new \DomainException('INSUFFICIENT_STOCK');
            if ((new InventoryService())->findShortages($cart->quantities(), true)) throw new \DomainException('INSUFFICIENT_STOCK');

            $fee = (int)Config::get('shop.shipping_fee', 30000);
            $subtotal = $cart->subtotal();
            $items = array_map(fn($i) => [
                'product_id' => $i->productId, 'product_name' => $i->product->name, 'unit_price' => $i->product->price,
                'quantity' => $i->quantity, 'line_total' => $i->lineTotal(),
            ], $cart->items);

            $orders = new OrderRepository();
            $id = $orders->create([
                'user_id' => $userId, 'subtotal' => $subtotal, 'shipping_fee' => $fee, 'discount_amount' => 0, 'tax_amount' => 0,
                'total' => $subtotal + $fee, 'ship_name' => $shipping['ship_name'], 'ship_phone' => $shipping['ship_phone'],
                'ship_address' => $shipping['ship_address'], 'delivery_option' => $shipping['delivery_option'] ?? 'standard',
                'payment_method' => $paymentMethod, 'customer_note' => ($shipping['customer_note'] ?? '') ?: null,
            ], $items);
            (new PaymentRepository())->create($id, $paymentMethod, $subtotal + $fee);
            (new CartRepository())->clear($cart->id);
            return $id;
        });
        return (new OrderRepository())->findById($orderId);
    }
}
