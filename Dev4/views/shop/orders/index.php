<?php use Atlas\Models\OrderStatus; ?>
<div class="account">
    <?php require __DIR__ . '/../account/_nav.php'; ?>
    <section>
        <h1>My orders</h1>
        <?php if (!$orders): ?><div class="empty"><p>You have no orders yet.</p><a class="btn btn-primary" href="<?= url('/products') ?>">Start shopping</a></div><?php else: ?>
        <table class="table">
            <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= e($o->orderCode) ?></td>
                    <td><?= e(date('d/m/Y H:i', strtotime($o->createdAt))) ?></td>
                    <td><?= money($o->total) ?></td>
                    <td><span class="<?= OrderStatus::badgeClass($o->status) ?>"><?= OrderStatus::label($o->status) ?></span></td>
                    <td><a href="<?= url('/account/orders/' . $o->orderCode) ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php $path = '/account/orders'; require __DIR__ . '/../../partials/pagination.php'; endif; ?>
    </section>
</div>
