<?php
namespace Atlas\Models;

final class CartItem
{
    public function __construct(
        public int $productId,
        public int $quantity,
        public ?Product $product = null,
        public bool $adjusted = false,
    ) {}

    public function lineTotal(): int { return $this->product ? $this->product->price * $this->quantity : 0; }
}
