<?php
namespace Atlas\Controllers\Shop;

use Atlas\Core\{Paginator, Request, Response, View};
use Atlas\Repositories\{CatalogQuery, CategoryRepository};

final class CatalogController
{
    public function index(Request $r): void { $this->listing($r, null); }

    public function category(Request $r): void
    {
        $category = (new CategoryRepository())->findBySlug((string)$r->param('slug'));
        if (!$category || !$category->isActive) Response::notFound();
        $this->listing($r, $category);
    }

    private function listing(Request $r, $category): void
    {
        $filters = array_intersect_key($_GET, array_flip(['q', 'category_id', 'price_min', 'price_max', 'sort', 'in_stock']));
        if ($category) $filters['category_id'] = $category->id;
        $page = max(1, (int)$r->query('page', 1));
        $result = (new CatalogQuery())->search($filters, $page);
        View::render('shop/catalog/index', [
            'title' => $category ? $category->name : 'All products', 'category' => $category,
            'products' => $result['items'], 'total' => $result['total'], 'filters' => $filters,
            'paginator' => new Paginator($result['total'], $page, 12), 'path' => $r->path(),
            'categories' => (new CategoryRepository())->tree(true),
        ]);
    }

    public function show(Request $r): void
    {
        $q = new CatalogQuery();
        $product = $q->findPublicBySlug((string)$r->param('slug')) ?? Response::notFound();
        View::render('shop/catalog/show', ['title' => $product->name, 'product' => $product, 'related' => $q->related($product)]);
    }
}
