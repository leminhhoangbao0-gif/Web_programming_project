<div class="card payment-box">
    <h1>Pay for order <?= e($order->orderCode) ?></h1>
    <p>Amount: <strong class="price big"><?= money($order->total) ?></strong></p>
    <?php if ($order->paymentMethod === 'bank_qr'): ?>
        <div class="fake-qr" aria-label="Demo QR code"></div>
        <p>Bank: <strong>Atlas Demo Bank</strong> · Account: <strong>0000 1234 5678</strong><br>Transfer note: <strong><?= e($order->orderCode) ?></strong></p>
    <?php else: ?>
        <div class="fake-wallet">📱 Atlas Pay (mock e-wallet)</div>
        <p>Confirm the payment in your e-wallet app.</p>
    <?php endif; ?>
    <p class="muted">This is a simulation. Choose the result below:</p>
    <div class="row-actions">
        <form method="post" action="<?= url("/payment/{$order->orderCode}/simulate") ?>"><?= csrf_field() ?><input type="hidden" name="outcome" value="success"><button class="btn btn-primary">Simulate success</button></form>
        <form method="post" action="<?= url("/payment/{$order->orderCode}/simulate") ?>"><?= csrf_field() ?><input type="hidden" name="outcome" value="failed"><button class="btn btn-danger">Simulate failure</button></form>
    </div>
</div>
