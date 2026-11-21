<?php
declare(strict_types=1);

use Atlas\Core\Csrf;
use Atlas\Repositories\CategoryRepository;
use Atlas\Services\AuthService;

$auth        = new AuthService();
$currentUser = $auth->currentUser();
$isLoggedIn  = $auth->check('customer');
$pageTitle   = isset($title) && is_string($title) && $title !== '' ? $title . ' - Atlas' : 'Atlas Store';

// Lấy cây danh mục active từ Dev 1 (bọc try-catch phòng trường hợp Dev 1 chưa implement xong stub)
$categories = [];
try {
    $categories = (new CategoryRepository())->tree(true);
} catch (\Throwable $e) {
    $categories = [];
}

$searchQuery = isset($_GET['q']) && is_string($_GET['q']) ? $_GET['q'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/base.css')) ?>">
    <?php if (isset($extraCss) && is_array($extraCss)): ?>
        <?php foreach ($extraCss as $cssPath): ?>
            <link rel="stylesheet" href="<?= e(url((string) $cssPath)) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body data-auth="<?= $isLoggedIn ? '1' : '0' ?>">

<header class="shop-header">
    <div class="shop-container shop-header__inner">
        <a href="<?= e(url('/')) ?>" class="shop-header__logo">ATLAS</a>

        <button type="button" class="shop-header__toggle" id="shopNavToggle" aria-label="Toggle Menu">
            &#9776;
        </button>

        <form action="<?= e(url('/products')) ?>" method="GET" class="shop-header__search">
            <input
                type="text"
                name="q"
                class="shop-input"
                placeholder="Tìm kiếm sản phẩm..."
                value="<?= e($searchQuery) ?>"
            >
            <button type="submit" class="shop-btn shop-btn--primary">Tìm</button>
        </form>

        <nav class="shop-header__actions">
            <a href="<?= e(url('/cart')) ?>" class="shop-header__cart">
                Giỏ hàng (<span data-cart-count>0</span>)
            </a>

            <?php if ($isLoggedIn && $currentUser !== null): ?>
                <div class="shop-dropdown">
                    <button type="button" class="shop-dropdown__btn">
                        <?= e($currentUser->fullName) ?> &#9662;
                    </button>
                    <div class="shop-dropdown__menu">
                        <a href="<?= e(url('/account/profile')) ?>">Hồ sơ cá nhân</a>
                        <a href="<?= e(url('/account/addresses')) ?>">Sổ địa chỉ</a>
                        <a href="<?= e(url('/account/orders')) ?>">Đơn hàng của tôi</a>
                        <a href="<?= e(url('/account/password')) ?>">Đổi mật khẩu</a>
                        <form action="<?= e(url('/logout')) ?>" method="POST" class="shop-dropdown__logout">
                            <?= Csrf::field() ?>
                            <button type="submit">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= e(url('/login')) ?>" class="shop-btn shop-btn--outline">Đăng nhập</a>
                <a href="<?= e(url('/register')) ?>" class="shop-btn shop-btn--primary">Đăng ký</a>
            <?php endif; ?>
        </nav>
    </div>

    <nav class="shop-nav" id="shopMainNav">
        <div class="shop-container">
            <ul class="shop-nav__list">
                <li><a href="<?= e(url('/')) ?>">Trang chủ</a></li>
                <li><a href="<?= e(url('/products')) ?>">Tất cả sản phẩm</a></li>
                <?php foreach ($categories as $cat): ?>
                    <li class="shop-nav__item">
                        <a href="<?= e(url('/category/' . $cat->slug)) ?>">
                            <?= e($cat->name) ?>
                        </a>
                        <?php if (!empty($cat->children)): ?>
                            <ul class="shop-nav__sub">
                                <?php foreach ($cat->children as $child): ?>
                                    <li>
                                        <a href="<?= e(url('/category/' . $child->slug)) ?>">
                                            <?= e($child->name) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</header>

<main class="shop-main">
    <div class="shop-container">
        <?php require __DIR__ . '/../partials/flash.php'; ?>
        <?= $content ?? '' ?>
    </div>
</main>

<footer class="shop-footer">
    <div class="shop-container shop-footer__inner">
        <p>&copy; <?= date('Y') ?> Atlas Store (Team 06). All rights reserved.</p>
        <div class="shop-footer__links">
            <a href="<?= e(url('/products')) ?>">Sản phẩm</a>
            <a href="<?= e(url('/cart')) ?>">Giỏ hàng</a>
            <a href="<?= e(url('/account/orders')) ?>">Tra cứu đơn hàng</a>
        </div>
    </div>
</footer>

<script>
    document.getElementById('shopNavToggle')?.addEventListener('click', function () {
        document.getElementById('shopMainNav')?.classList.toggle('is-open');
    });
</script>
<script src="<?= e(url('/assets/js/atlas-cart.js')) ?>"></script>
<?php if (isset($extraJs) && is_array($extraJs)): ?>
    <?php foreach ($extraJs as $jsPath): ?>
        <script src="<?= e(url((string) $jsPath)) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
