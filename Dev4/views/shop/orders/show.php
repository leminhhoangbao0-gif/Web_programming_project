<?php use Atlas\Models\OrderStatus;
$flow = OrderStatus::flow();
$reached = array_column($history, 'created_at', 'to_status');
$methods = (new Atlas\Services\PaymentService())->methods(); ?>
<div class="account">
    <?php require __DIR__ . '/../account/_nav.php'; ?>
    <section>
        <h1>Order <?= e($order->orderCode) ?> <span class="<?= OrderStatus::badgeClass($order->status) ?>"><?= OrderStatus::label($order->status) ?></span></h1>

        <?php if ($order->status === OrderStatus::CANCELLED): ?>
            <div class="alert alert-error">This order was cancelled on <?= e(date('d/m/Y H:i', strtotime($order->cancelledAt))) ?>.</div>
        <?php else: ?>
            <ol class="tracker">
                <?php foreach ($flow as $st): ?>
                    <li class="<?= isset($reached[$st]) ? 'done' : '' ?>">
                        <strong><?= OrderStatus::label($st) ?></strong>
                        <small><?= isset($reached[$st]) ? e(date('d/m H:i', strtotime($reached[$st]))) : '' ?></small>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>

        <div class="grid-2">
            <div class="card">
                <h3>Shipping to</h3>
                <p><strong><?= e($order->shipName) ?></strong> · <?= e($order->shipPhone) ?><br><?= e($order->shipAddress) ?></p>
                <?php if ($order->customerNote): ?><p class="muted">Note: <?= e($order->customerNote) ?></p><?php endif; ?>
            </div>
            <div class="card">
                <h3>Payment</h3>
                <p><?= e($methods[$order->paymentMethod] ?? $order->paymentMethod) ?> · <strong><?= e(ucfirst($order->paymentStatus)) ?></strong></p>
                <?php if ($order->paymentMethod !== 'cod' && in_array($order->paymentStatus, ['unpaid', 'failed'], true) && $order->status !== 'cancelled'): ?>
                    <a class="btn btn-primary" href="<?= url('/payment/' . $order->orderCode) ?>">Pay now</a>
                <?php endif; ?>
            </div>
        </div>

        <h3>Receipt</h3>
        <table class="table">
            <thead><tr><th>Product</th><th>Unit price</th><th>Qty</th><th>Total</th></tr></thead>
            <tbody>
            <?php foreach ($order->items as $i): ?>
                <tr><td><?= e($i->productName) ?></td><td><?= money($i->unitPrice) ?></td><td><?= $i->quantity ?></td><td><?= money($i->lineTotal) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="3">Subtotal</td><td><?= money($order->subtotal) ?></td></tr>
                <tr><td colspan="3">Shipping (<?= e($order->deliveryOption) ?>)</td><td><?= money($order->shippingFee) ?></td></tr>
                <tr><td colspan="3"><strong>Total</strong></td><td><strong><?= money($order->total) ?></strong></td></tr>
            </tfoot>
        </table>

        <?php if ($order->status === OrderStatus::PENDING): ?>
            <form method="post" action="<?= url('/account/orders/' . $order->orderCode . '/cancel') ?>" onsubmit="return confirm('Cancel this order?')">
                <?= csrf_field() ?><button class="btn btn-danger">Cancel order</button>
            </form>
        <?php endif; ?>
    </section>
</div>
