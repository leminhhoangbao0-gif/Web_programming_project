<?php
namespace Atlas\Controllers\Shop;

use Atlas\Core\{Request, View};

final class CartController
{
    public function index(Request $r): void { View::render('shop/cart/index', ['title' => 'Your cart']); }
}
