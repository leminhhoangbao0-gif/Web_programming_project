<?php
declare(strict_types=1);
use Atlas\Core\Csrf;
?>
<div class="shop-auth-box shop-card">
    <h1 class="shop-auth-box__title">Đăng ký tài khoản</h1>

    <?php require __DIR__ . '/../../partials/form_errors.php'; ?>

    <form action="<?= e(url('/register')) ?>" method="POST">
        <?= Csrf::field() ?>

        <div class="shop-form-group">
            <label class="shop-label" for="full_name">Họ và tên *</label>
            <input type="text" id="full_name" name="full_name" class="shop-input" value="<?= e(old('full_name')) ?>" required>
        </div>

        <div class="shop-form-group">
            <label class="shop-label" for="email">Email *</label>
            <input type="email" id="email" name="email" class="shop-input" value="<?= e(old('email')) ?>" required>
        </div>

        <div class="shop-form-group">
            <label class="shop-label" for="phone">Số điện thoại (tùy chọn)</label>
            <input type="text" id="phone" name="phone" class="shop-input" placeholder="09xxxxxxxx" value="<?= e(old('phone')) ?>">
        </div>

        <div class="shop-form-group">
            <label class="shop-label" for="password">Mật khẩu (tối thiểu 8 ký tự) *</label>
            <input type="password" id="password" name="password" class="shop-input" required>
        </div>

        <div class="shop-form-group">
            <label class="shop-label" for="password_confirmation">Xác nhận mật khẩu *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="shop-input" required>
        </div>

        <button type="submit" class="shop-btn shop-btn--primary shop-auth-box__submit">Đăng ký</button>
    </form>

    <p class="shop-auth-box__footer">
        Đã có tài khoản? <a href="<?= e(url('/login')) ?>">Đăng nhập</a>
    </p>
</div>
