<article class="product-card">
    <a href="<?= url('/product/' . $p->slug) ?>"><img src="<?= e($p->imageUrl()) ?>" alt="<?= e($p->name) ?>" loading="lazy"></a>
    <div class="product-card-body">
        <a class="product-name" href="<?= url('/product/' . $p->slug) ?>"><?= e($p->name) ?></a>
        <div class="price"><?= money($p->price) ?></div>
        <div class="stock <?= $p->isInStock() ? '' : 'out' ?>"><?= e($p->stockLabel()) ?></div>
        <button class="btn btn-primary btn-block" data-add-to-cart data-product-id="<?= $p->id ?>" <?= $p->isInStock() ? '' : 'disabled' ?>>Add to cart</button>
    </div>
</article>
