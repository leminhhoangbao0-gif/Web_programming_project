<div class="card payment-box">
    <h1>✅ Thank you! Your order is placed.</h1>
    <p>Order code: <strong><?= e($order->orderCode) ?></strong></p>
    <p>Total: <strong><?= money($order->total) ?></strong> · <?= e($methods[$order->paymentMethod]) ?> · Payment <strong><?= e($order->paymentStatus) ?></strong></p>
    <p class="muted">We will confirm your order soon. You can follow its status in “My orders”.</p>
    <div class="row-actions">
        <a class="btn btn-primary" href="<?= url('/account/orders/' . $order->orderCode) ?>">Track order</a>
        <a class="btn" href="<?= url('/products') ?>">Continue shopping</a>
    </div>
</div>
