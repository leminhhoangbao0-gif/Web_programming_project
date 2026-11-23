<?php
declare(strict_types=1);
use Atlas\Core\Csrf;
?>
<div class="shop-account-layout">
    <aside class="shop-account-sidebar shop-card">
        <a href="<?= e(url('/account/profile')) ?>">Hồ sơ cá nhân</a>
        <a href="<?= e(url('/account/addresses')) ?>">Sổ địa chỉ</a>
        <a href="<?= e(url('/account/orders')) ?>">Đơn hàng của tôi</a>
        <a href="<?= e(url('/account/password')) ?>" class="is-active">Đổi mật khẩu</a>
    </aside>

    <section class="shop-account-content shop-card">
        <h2>Đổi mật khẩu</h2>
        <?php require __DIR__ . '/../../partials/form_errors.php'; ?>

        <form action="<?= e(url('/account/password')) ?>" method="POST">
            <?= Csrf::field() ?>

            <div class="shop-form-group">
                <label class="shop-label" for="current_password">Mật khẩu hiện tại *</label>
                <input type="password" id="current_password" name="current_password" class="shop-input" required>
            </div>

            <div class="shop-form-group">
                <label class="shop-label" for="new_password">Mật khẩu mới (tối thiểu 8 ký tự) *</label>
                <input type="password" id="new_password" name="new_password" class="shop-input" required>
            </div>

            <div class="shop-form-group">
                <label class="shop-label" for="new_password_confirmation">Xác nhận mật khẩu mới *</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="shop-input" required>
            </div>

            <button type="submit" class="shop-btn shop-btn--primary">Cập nhật mật khẩu</button>
        </form>
    </section>
</div>
