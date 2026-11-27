<?php
namespace Atlas\Models;

final class Cart
{
    public ?int $id = null;
    public ?int $userId = null;
    public array $items = [];
    public array $notices = [];

    public function subtotal(): int { return array_sum(array_map(fn(CartItem $i) => $i->lineTotal(), $this->items)); }
    public function count(): int { return array_sum(array_map(fn(CartItem $i) => $i->quantity, $this->items)); }
    public function isEmpty(): bool { return !$this->items; }

    public function quantities(): array
    {
        $q = [];
        foreach ($this->items as $i) $q[$i->productId] = $i->quantity;
        return $q;
    }

    public function toArray(): array
    {
        return [
            'items' => array_map(fn(CartItem $i) => [
                'product_id' => $i->productId,
                'name'       => $i->product->name,
                'slug'       => $i->product->slug,
                'image_url'  => $i->product->imageUrl(),
                'unit_price' => $i->product->price,
                'quantity'   => $i->quantity,
                'line_total' => $i->lineTotal(),
                'stock'      => $i->product->stockQuantity,
                'adjusted'   => $i->adjusted,
            ], $this->items),
            'subtotal' => $this->subtotal(),
            'count'    => $this->count(),
            'notices'  => $this->notices,
        ];
    }
}
