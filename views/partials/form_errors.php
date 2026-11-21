<?php
declare(strict_types=1);

use Atlas\Core\Session;

$errors = Session::flash('errors');
?>

<?php if (is_array($errors) && !empty($errors)): ?>
    <div class="shop-alert shop-alert--error" role="alert">
        <ul class="shop-error-list">
            <?php foreach ($errors as $msg): ?>
                <li><?= e((string) $msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
