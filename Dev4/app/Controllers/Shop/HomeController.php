<?php
namespace Atlas\Controllers\Shop;

use Atlas\Core\{Request, View};
use Atlas\Repositories\{CatalogQuery, CategoryRepository};

final class HomeController
{
    public function index(Request $r): void
    {
        $q = new CatalogQuery();
        View::render('shop/home/index', [
            'title' => 'Atlas', 'featured' => $q->featured(5), 'newest' => $q->newest(8),
            'categories' => (new CategoryRepository())->tree(true),
        ]);
    }
}
