<?php
namespace Atlas\Repositories;

use Atlas\Core\Database;
use Atlas\Models\Product;

final class CatalogQuery
{
    private const SELECT = 'SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id';
    private const PUBLIC = "p.status = 'active' AND p.deleted_at IS NULL AND c.is_active = 1";

    public function search(array $filters, int $page, int $perPage = 12): array
    {
        $where = [self::PUBLIC];
        $args = [];
        if (!empty($filters['q'])) { $where[] = '(p.name LIKE ? OR p.description LIKE ?)'; $args[] = '%' . $filters['q'] . '%'; $args[] = '%' . $filters['q'] . '%'; }
        if (!empty($filters['category_id'])) {
            $ids = (new CategoryRepository())->descendantIds((int)$filters['category_id']);
            $where[] = 'p.category_id IN (' . implode(',', array_map('intval', $ids)) . ')';
        }
        if (($filters['price_min'] ?? '') !== '') { $where[] = 'p.price >= ?'; $args[] = (int)$filters['price_min']; }
        if (($filters['price_max'] ?? '') !== '') { $where[] = 'p.price <= ?'; $args[] = (int)$filters['price_max']; }
        if (!empty($filters['in_stock'])) $where[] = 'p.stock_quantity > 0';
        $order = match ($filters['sort'] ?? 'newest') {
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            default => 'p.created_at DESC, p.id DESC',
        };

        $whereSql = ' WHERE ' . implode(' AND ', $where);
        $pdo = Database::connection();
        $st = $pdo->prepare('SELECT COUNT(*) FROM products p JOIN categories c ON c.id = p.category_id' . $whereSql);
        $st->execute($args);
        $total = (int)$st->fetchColumn();
        $offset = max(0, ($page - 1) * $perPage);
        $st = $pdo->prepare(self::SELECT . $whereSql . " ORDER BY $order LIMIT $perPage OFFSET $offset");
        $st->execute($args);
        return ['items' => array_map([Product::class, 'fromRow'], $st->fetchAll()), 'total' => $total];
    }

    public function findPublicBySlug(string $slug): ?Product
    {
        $st = Database::connection()->prepare(self::SELECT . ' WHERE p.slug = ? AND ' . self::PUBLIC);
        $st->execute([$slug]);
        $row = $st->fetch();
        return $row ? Product::fromRow($row) : null;
    }

    private function list(string $extra, string $order, int $limit, array $args = []): array
    {
        $st = Database::connection()->prepare(self::SELECT . ' WHERE ' . self::PUBLIC . $extra . " ORDER BY $order LIMIT " . (int)$limit);
        $st->execute($args);
        return array_map([Product::class, 'fromRow'], $st->fetchAll());
    }

    public function featured(int $limit = 8): array { return $this->list(' AND p.is_featured = 1', 'p.id DESC', $limit); }

    public function newest(int $limit = 8): array { return $this->list('', 'p.created_at DESC, p.id DESC', $limit); }

    public function related(Product $p, int $limit = 4): array
    {
        return $this->list(' AND p.category_id = ? AND p.id <> ?', 'RAND()', $limit, [$p->categoryId, $p->id]);
    }
}
