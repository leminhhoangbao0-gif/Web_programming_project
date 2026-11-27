<?php $cart = $s['cart']; ?>
<h1>Checkout</h1>
<?php if ($cart->isEmpty()): ?>
    <div class="empty"><p>Your cart is empty.</p><a class="btn btn-primary" href="<?= url('/products') ?>">Continue shopping</a></div>
<?php else: ?>
<?php foreach ($cart->notices as $n): ?><div class="alert alert-error"><?= e($n) ?></div><?php endforeach; ?>
<?php require __DIR__ . '/../../partials/form_errors.php'; ?>
<form method="post" action="<?= url('/checkout') ?>" class="checkout">
    <?= csrf_field() ?>
    <div>
        <section class="card">
            <h2>1. Shipping address</h2>
            <?php $chosen = old('address_id', $s['addresses'] ? (string)$s['addresses'][0]->id : 'new'); ?>
            <?php foreach ($s['addresses'] as $a): ?>
                <label class="option"><input type="radio" name="address_id" value="<?= $a->id ?>" <?= $chosen === (string)$a->id ? 'checked' : '' ?>>
                    <span><strong><?= e($a->recipientName) ?></strong> · <?= e($a->phone) ?><br><?= e($a->fullText()) ?></span></label>
            <?php endforeach; ?>
            <label class="option"><input type="radio" name="address_id" value="new" <?= $chosen === 'new' ? 'checked' : '' ?>> <span>Use a new address</span></label>
            <div class="new-address" data-new-address>
                <?php $a = null; require __DIR__ . '/../account/_address_fields.php'; ?>
                <label class="check"><input type="checkbox" name="save_address" value="1" checked> Save to my address book</label>
            </div>
        </section>
        <section class="card">
            <h2>2. Delivery</h2>
            <label class="option"><input type="radio" checked disabled> <span>Standard delivery (3–5 days) – <?= money($s['shipping_fee']) ?></span></label>
            <label>Note for the shop (optional) <textarea name="customer_note" maxlength="500"><?= e(old('customer_note')) ?></textarea></label>
        </section>
        <section class="card">
            <h2>3. Payment</h2>
            <?php foreach ($methods as $code => $label): ?>
                <label class="option"><input type="radio" name="payment_method" value="<?= $code ?>" <?= old('payment_method', 'cod') === $code ? 'checked' : '' ?>> <span><?= e($label) ?></span></label>
            <?php endforeach; ?>
            <p class="muted">Payments are simulated for this project – no real money is charged.</p>
        </section>
    </div>
    <aside class="card summary">
        <h2>Order summary</h2>
        <?php foreach ($cart->items as $i): ?>
            <div class="summary-row"><span><?= e($i->product->name) ?> × <?= $i->quantity ?></span><span><?= money($i->lineTotal()) ?></span></div>
        <?php endforeach; ?>
        <hr>
        <div class="summary-row"><span>Subtotal</span><span><?= money($s['subtotal']) ?></span></div>
        <div class="summary-row"><span>Shipping</span><span><?= money($s['shipping_fee']) ?></span></div>
        <div class="summary-row total"><span>Total</span><span><?= money($s['total']) ?></span></div>
        <button class="btn btn-primary btn-block">Place order</button>
        <a class="btn btn-block" href="<?= url('/cart') ?>">Back to cart</a>
    </aside>
</form>
<?php endif; ?>
