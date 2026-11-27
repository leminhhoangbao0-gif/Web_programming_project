<?php if ($featured): ?>
<section class="slider" data-slider>
    <?php foreach ($featured as $i => $p): ?>
        <div class="slide <?= $i === 0 ? 'active' : '' ?>">
            <img src="<?= e($p->imageUrl()) ?>" alt="">
            <div class="slide-text">
                <span class="muted">Featured</span>
                <h2><?= e($p->name) ?></h2>
                <p class="price"><?= money($p->price) ?></p>
                <a class="btn btn-primary" href="<?= url('/product/' . $p->slug) ?>">View product</a>
            </div>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<h2>Shop by category</h2>
<div class="category-shortcuts">
    <?php foreach ($categories as $c): ?>
        <a class="card" href="<?= url('/category/' . $c->slug) ?>"><?= e($c->name) ?></a>
    <?php endforeach; ?>
</div>

<h2>New arrivals</h2>
<div class="product-grid">
    <?php foreach ($newest as $p) require __DIR__ . '/../../partials/product_card.php'; ?>
</div>
