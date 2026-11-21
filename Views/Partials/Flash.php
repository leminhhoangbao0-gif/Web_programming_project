<?php
declare(strict_types=1);

use Atlas\Core\Session;

$success = Session::flash('success');
$error   = Session::flash('error');
?>

<?php if (is_string($success) && $success !== ''): ?>
    <div class="shop-alert shop-alert--success" role="alert">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<?php if (is_string($error) && $error !== ''): ?>
    <div class="shop-alert shop-alert--error" role="alert">
        <?= e($error) ?>
    </div>
<?php endif; ?>
