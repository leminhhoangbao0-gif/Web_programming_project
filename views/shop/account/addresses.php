<?php
declare(strict_types=1);
use Atlas\Core\Csrf;
?>
<div class="shop-account-layout">
    <aside class="shop-account-sidebar shop-card">
        <a href="<?= e(url('/account/profile')) ?>">Hồ sơ cá nhân</a>
        <a href="<?= e(url('/account/addresses')) ?>" class="is-active">Sổ địa chỉ</a>
        <a href="<?= e(url('/account/orders')) ?>">Đơn hàng của tôi</a>
        <a href="<?= e(url('/account/password')) ?>">Đổi mật khẩu</a>
    </aside>

    <section class="shop-account-content">
        <div class="shop-card" style="margin-bottom: 20px;">
            <h2><?= $editing ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ giao hàng mới' ?></h2>
            <?php require __DIR__ . '/../../partials/form_errors.php'; ?>

            <?php
            $formAction = $editing
                ? url('/account/addresses/' . $editing->id)
                : url('/account/addresses');
            ?>
            <form action="<?= e($formAction) ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="shop-address-grid">
                    <div class="shop-form-group">
                        <label class="shop-label">Tên người nhận *</label>
                        <input type="text" name="recipient_name" class="shop-input" value="<?= e($editing ? $editing->recipientName : old('recipient_name')) ?>" required>
                    </div>

                    <div class="shop-form-group">
                        <label class="shop-label">Số điện thoại *</label>
                        <input type="text" name="phone" class="shop-input" value="<?= e($editing ? $editing->phone : old('phone')) ?>" required>
                    </div>
                </div>

                <div class="shop-form-group">
                    <label class="shop-label">Địa chỉ cụ thể (Số nhà, tên đường) *</label>
                    <input type="text" name="address_line" class="shop-input" value="<?= e($editing ? $editing->addressLine : old('address_line')) ?>" required>
                </div>

                <div class="shop-address-grid-3">
                    <div class="shop-form-group">
                        <label class="shop-label">Phường / Xã</label>
                        <input type="text" name="ward" class="shop-input" value="<?= e($editing ? ($editing->ward ?? '') : old('ward')) ?>">
                    </div>

                    <div class="shop-form-group">
                        <label class="shop-label">Quận / Huyện</label>
                        <input type="text" name="district" class="shop-input" value="<?= e($editing ? ($editing->district ?? '') : old('district')) ?>">
                    </div>

                    <div class="shop-form-group">
                        <label class="shop-label">Tỉnh / Thành phố *</label>
                        <input type="text" name="city" class="shop-input" value="<?= e($editing ? $editing->city : old('city')) ?>" required>
                    </div>
                </div>

                <?php if (!$editing): ?>
                    <div class="shop-form-group">
                        <label>
                            <input type="checkbox" name="is_default" value="1"> Đặt làm địa chỉ mặc định
                        </label>
                    </div>
                <?php endif; ?>

                <button type="submit" class="shop-btn shop-btn--primary">
                    <?= $editing ? 'Lưu cập nhật' : 'Thêm địa chỉ' ?>
                </button>
                <?php if ($editing): ?>
                    <a href="<?= e(url('/account/addresses')) ?>" class="shop-btn shop-btn--outline">Hủy</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="shop-card">
            <h2>Danh sách địa chỉ đã lưu</h2>
            <?php if (empty($addresses)): ?>
                <p>Bạn chưa lưu địa chỉ giao hàng nào.</p>
            <?php else: ?>
                <div class="shop-address-list">
                    <?php foreach ($addresses as $addr): ?>
                        <div class="shop-address-item">
                            <div>
                                <strong><?= e($addr->recipientName) ?></strong> — <?= e($addr->phone) ?>
                                <?php if ($addr->isDefault): ?>
                                    <span class="shop-badge-default">Mặc định</span>
                                <?php endif; ?>
                                <p class="shop-address-item__text"><?= e($addr->fullText()) ?></p>
                            </div>

                            <div class="shop-address-item__actions">
                                <a href="<?= e(url('/account/addresses', ['edit' => $addr->id])) ?>" class="shop-btn shop-btn--outline">Sửa</a>

                                <?php if (!$addr->isDefault): ?>
                                    <form action="<?= e(url('/account/addresses/' . $addr->id . '/default')) ?>" method="POST">
                                        <?= Csrf::field() ?>
                                        <button type="submit" class="shop-btn shop-btn--outline">Đặt mặc định</button>
                                    </form>
                                <?php endif; ?>

                                <form action="<?= e(url('/account/addresses/' . $addr->id . '/delete')) ?>" method="POST" onsubmit="return confirm('Xóa địa chỉ này?');">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="shop-btn shop-btn--danger">Xóa</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
