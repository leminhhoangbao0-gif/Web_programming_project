<div class="catalog">
    <aside class="filters card">
        <form method="get" action="<?= url($path) ?>">
            <?php if (!empty($filters['q'])): ?><input type="hidden" name="q" value="<?= e($filters['q']) ?>"><?php endif; ?>
            <?php if (!$category): ?>
                <label>Category
                    <select name="category_id">
                        <option value="">All</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c->id ?>" <?= ($filters['category_id'] ?? '') == $c->id ? 'selected' : '' ?>><?= e($c->name) ?></option>
                            <?php foreach ($c->children as $ch): ?>
                                <option value="<?= $ch->id ?>" <?= ($filters['category_id'] ?? '') == $ch->id ? 'selected' : '' ?>>— <?= e($ch->name) ?></option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </label>
            <?php endif; ?>
            <label>Min price <input type="number" name="price_min" min="0" step="1000" value="<?= e($filters['price_min'] ?? '') ?>"></label>
            <label>Max price <input type="number" name="price_max" min="0" step="1000" value="<?= e($filters['price_max'] ?? '') ?>"></label>
            <label class="check"><input type="checkbox" name="in_stock" value="1" <?= !empty($filters['in_stock']) ? 'checked' : '' ?>> In stock only</label>
            <label>Sort
                <select name="sort">
                    <?php foreach (['newest' => 'Newest', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low'] as $k => $label): ?>
                        <option value="<?= $k ?>" <?= ($filters['sort'] ?? 'newest') === $k ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn btn-primary btn-block">Apply</button>
            <a class="btn btn-block" href="<?= url($path) ?>">Reset</a>
        </form>
    </aside>
    <section>
        <h1><?= e($title) ?><?php if (!empty($filters['q'])): ?> – “<?= e($filters['q']) ?>”<?php endif; ?></h1>
        <p class="muted"><?= $total ?> product(s)</p>
        <?php if (!$products): ?>
            <div class="empty"><p>No products match your filters.</p></div>
        <?php endif; ?>
        <div class="product-grid">
            <?php foreach ($products as $p) require __DIR__ . '/../../partials/product_card.php'; ?>
        </div>
        <?php $query = $filters; unset($query['category_id']); if (!$category && !empty($filters['category_id'])) $query['category_id'] = $filters['category_id'];
        require __DIR__ . '/../../partials/pagination.php'; ?>
    </section>
</div>
