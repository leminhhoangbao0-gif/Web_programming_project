<?php
declare(strict_types=1);
use Atlas\Core\Csrf;
?>
<div class="shop-account-layout">
    <aside class="shop-account-sidebar shop-card">
        <a href="<?= e(url('/account/profile')) ?>" class="is-active">Hồ sơ cá nhân</a>
        <a href="<?= e(url('/account/addresses')) ?>">Sổ địa chỉ</a>
        <a href="<?= e(url('/account/orders')) ?>">Đơn hàng của tôi</a>
        <a href="<?= e(url('/account/password')) ?>">Đổi mật khẩu</a>
    </aside>

    <section class="shop-account-content shop-card">
        <h2>Hồ sơ cá nhân</h2>
        <?php require __DIR__ . '/../../partials/form_errors.php'; ?>

        <form action="<?= e(url('/account/profile')) ?>" method="POST">
            <?= Csrf::field() ?>

            <div class="shop-form-group">
                <label class="shop-label">Email</label>
                <input type="email" class="shop-input" value="<?= e($user->email) ?>" disabled>
            </div>

            <div class="shop-form-group">
                <label class="shop-label" for="full_name">Họ và tên *</label>
                <input type="text" id="full_name" name="full_name" class="shop-input" value="<?= e(old('full_name', $user->fullName)) ?>" required>
            </div>

            <div class="shop-form-group">
                <label class="shop-label" for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" class="shop-input" value="<?= e(old('phone', $user->phone ?? '')) ?>">
            </div>

            <button type="submit" class="shop-btn shop-btn--primary">Lưu thay đổi</button>
        </form>
    </section>
</div>
