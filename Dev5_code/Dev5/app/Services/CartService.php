<?php
namespace Atlas\Services;

use Atlas\Models\{Cart, CartItem};
use Atlas\Repositories\{CartRepository, ProductRepository};

final class CartService
{
    private CartRepository $carts;
    private ProductRepository $products;

    public function __construct()
    {
        $this->carts = new CartRepository();
        $this->products = new ProductRepository();
    }

    public function getCart(int $userId): Cart
    {
        $cartId = $this->carts->getOrCreateCartId($userId);
        $cart = $this->build($this->carts->items($cartId), function (int $pid, int $qty) use ($cartId) {
            $this->carts->setQuantity($cartId, $pid, $qty);
        });
        $cart->id = $cartId;
        $cart->userId = $userId;
        return $cart;
    }

    public function add(int $userId, int $productId, int $qty): Cart
    {
        $product = $this->products->findById($productId);
        if (!$product || !$product->isPublic()) throw new \DomainException('PRODUCT_UNAVAILABLE');
        $cartId = $this->carts->getOrCreateCartId($userId);
        $newQty = ($this->carts->items($cartId)[$productId] ?? 0) + max(1, $qty);
        if ($newQty > $product->stockQuantity) throw new \DomainException('OUT_OF_STOCK');
        $this->carts->setQuantity($cartId, $productId, $newQty);
        return $this->getCart($userId);
    }

    public function update(int $userId, int $productId, int $qty): Cart
    {
        $this->carts->setQuantity($this->carts->getOrCreateCartId($userId), $productId, $qty);
        return $this->getCart($userId);
    }

    public function remove(int $userId, int $productId): Cart { return $this->update($userId, $productId, 0); }

    public function mergeGuestCart(int $userId, array $guestItems): Cart
    {
        $cartId = $this->carts->getOrCreateCartId($userId);
        $saved = $this->carts->items($cartId);
        foreach ($this->normalize($guestItems) as $pid => $qty) {
            $p = $this->products->findById($pid);
            if (!$p || !$p->isPublic() || $p->stockQuantity <= 0) continue;
            $this->carts->setQuantity($cartId, $pid, min(($saved[$pid] ?? 0) + $qty, $p->stockQuantity));
        }
        return $this->getCart($userId);
    }

    public function previewGuestCart(array $guestItems): Cart
    {
        return $this->build($this->normalize($guestItems), null);
    }

    private function normalize(array $guestItems): array
    {
        $q = [];
        foreach ($guestItems as $item) {
            $pid = (int)($item['product_id'] ?? 0);
            $qty = (int)($item['quantity'] ?? 0);
            if ($pid > 0 && $qty > 0) $q[$pid] = ($q[$pid] ?? 0) + $qty;
        }
        return $q;
    }

    private function build(array $quantities, ?callable $persist): Cart
    {
        $cart = new Cart();
        $products = $this->products->findManyByIds(array_keys($quantities));
        foreach ($quantities as $pid => $qty) {
            $p = $products[$pid] ?? null;
            if (!$p || !$p->isPublic() || $p->stockQuantity <= 0) {
                $cart->notices[] = ($p ? $p->name : 'A product') . ' is no longer available and was removed.';
                if ($persist) $persist($pid, 0);
                continue;
            }
            $item = new CartItem($pid, $qty, $p);
            if ($qty > $p->stockQuantity) {
                $item->quantity = $p->stockQuantity;
                $item->adjusted = true;
                $cart->notices[] = "Only {$p->stockQuantity} left of {$p->name} – quantity reduced.";
                if ($persist) $persist($pid, $p->stockQuantity);
            }
            $cart->items[] = $item;
        }
        return $cart;
    }
}
