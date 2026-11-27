<div class="pdp">
    <img class="pdp-image" src="<?= e($product->imageUrl()) ?>" alt="<?= e($product->name) ?>">
    <div class="pdp-info">
        <p class="muted"><?= e($product->categoryName) ?></p>
        <h1><?= e($product->name) ?></h1>
        <p class="price big"><?= money($product->price) ?></p>
        <p class="stock <?= $product->isInStock() ? '' : 'out' ?>"><?= e($product->stockLabel()) ?></p>
        <?php if ($product->isInStock()): ?>
            <div class="qty-row">
                <label>Quantity <input type="number" id="pdp-qty" value="1" min="1" max="<?= $product->stockQuantity ?>" data-qty-cap></label>
                <button class="btn btn-primary" data-add-to-cart data-product-id="<?= $product->id ?>" data-qty-input="#pdp-qty">Add to cart</button>
            </div>
        <?php endif; ?>
        <?php if ($product->description): ?><h3>Description</h3><p><?= nl2br(e($product->description)) ?></p><?php endif; ?>
        <?php if ($product->specifications): ?><h3>Specifications</h3><pre class="specs"><?= e($product->specifications) ?></pre><?php endif; ?>
    </div>
</div>
<?php if ($related): ?>
    <h2>Related products</h2>
    <div class="product-grid"><?php foreach ($related as $p) require __DIR__ . '/../../partials/product_card.php'; ?></div>
<?php endif; ?>
