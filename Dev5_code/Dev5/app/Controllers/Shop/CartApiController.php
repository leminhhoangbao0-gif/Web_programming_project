<?php
namespace Atlas\Controllers\Shop;

use Atlas\Core\{Csrf, Request, Response};
use Atlas\Services\{AuthService, CartService};

final class CartApiController
{
    private function userId(): int { return (new AuthService())->currentUser()->id; }

    private function run(Request $r, callable $fn): never
    {
        if ($r->method() === 'POST') Csrf::verify($r);
        try {
            Response::ok($fn(new CartService(), $r->json())->toArray());
        } catch (\DomainException $e) {
            Response::error($e->getMessage(), error_message($e->getMessage()));
        }
    }

    public function get(Request $r): void { $this->run($r, fn($s) => $s->getCart($this->userId())); }
    public function add(Request $r): void { $this->run($r, fn($s, $j) => $s->add($this->userId(), (int)($j['product_id'] ?? 0), (int)($j['quantity'] ?? 1))); }
    public function update(Request $r): void { $this->run($r, fn($s, $j) => $s->update($this->userId(), (int)($j['product_id'] ?? 0), (int)($j['quantity'] ?? 0))); }
    public function remove(Request $r): void { $this->run($r, fn($s, $j) => $s->remove($this->userId(), (int)($j['product_id'] ?? 0))); }
    public function merge(Request $r): void { $this->run($r, fn($s, $j) => $s->mergeGuestCart($this->userId(), (array)($j['items'] ?? []))); }

    public function preview(Request $r): void { $this->run($r, fn($s, $j) => $s->previewGuestCart((array)($j['items'] ?? []))); }
}
