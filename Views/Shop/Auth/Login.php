<?php
declare(strict_types=1);
use Atlas\Core\Csrf;
?>
<div class="shop-auth-box shop-card">
    <h1 class="shop-auth-box__title">Đăng nhập</h1>

    <?php require __DIR__ . '/../../partials/form_errors.php'; ?>

    <form action="<?= e(url('/login')) ?>" method="POST">
        <?= Csrf::field() ?>
        <input type="hidden" name="redirect" value="<?= e($redirect ?? '/') ?>">

        <div class="shop-form-group">
            <label class="shop-label" for="email">Email</label>
            <input type="email" id="email" name="email" class="shop-input" value="<?= e(old('email')) ?>" required>
        </div>

        <div class="shop-form-group">
            <label class="shop-label" for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" class="shop-input" required>
        </div>

        <button type="submit" class="shop-btn shop-btn--primary shop-auth-box__submit">Đăng nhập</button>
    </form>

    <p class="shop-auth-box__footer">
        Chưa có tài khoản? <a href="<?= e(url('/register')) ?>">Đăng ký ngay</a>
    </p>
</div>
