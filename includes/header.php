<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

$user = current_user();
$admin = current_admin();
$flash = get_flash();
$currentPath = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '');
$stylesVersion = @filemtime(__DIR__ . '/../css/styles.css') ?: time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Pastimes'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(app_url('css/styles.css?v=' . $stylesVersion)); ?>">
    <script defer src="<?= e(app_url('js/app.js')); ?>"></script>
</head>
<body>
<header class="site-header">
    <div class="container nav-shell">
        <a class="brand-mark" href="<?= e(app_url('index.php')); ?>">Pastimes</a>
        <nav class="site-nav">
            <a class="nav-link<?= $currentPath === 'shop.php' ? ' is-active' : ''; ?>" href="<?= e(app_url('shop.php')); ?>">Shop</a>
            <a class="nav-link<?= $currentPath === 'about.php' ? ' is-active' : ''; ?>" href="<?= e(app_url('about.php')); ?>">About</a>
            <a class="nav-link<?= $currentPath === 'contact.php' ? ' is-active' : ''; ?>" href="<?= e(app_url('contact.php')); ?>">Contact</a>
            <?php if ($user): ?>
                <a class="nav-link<?= $currentPath === 'dashboard.php' ? ' is-active' : ''; ?>" href="<?= e(app_url('dashboard.php')); ?>">Dashboard</a>
                <a class="nav-link<?= $currentPath === 'purchase-history.php' ? ' is-active' : ''; ?>" href="<?= e(app_url('purchase-history.php')); ?>">Purchase History</a>
                <a class="nav-link<?= $currentPath === 'messages.php' ? ' is-active' : ''; ?>" href="<?= e(app_url('messages.php')); ?>">Messages</a>
                <?php if (($user['seller_status'] ?? 'none') === 'approved'): ?>
                    <a class="nav-link<?= str_contains($currentPath, 'seller') ? ' is-active' : ''; ?>" href="<?= e(app_url('seller/dashboard.php')); ?>">Seller Tools</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
        <div class="nav-actions">
            <a class="cart-link" href="<?= e(app_url('cart.php')); ?>">Cart (<?= e((string) cart_count()); ?>)</a>
            <?php if ($admin): ?>
                <a class="button button-dark button-small admin-top-button" href="<?= e(app_url('admin/index.php')); ?>">Admin Panel</a>
                <a class="button button-light button-small header-action-button" href="<?= e(app_url('admin/messages.php')); ?>">Admin Messages</a>
                <span class="nav-user">Admin <?= e($admin['full_name']); ?></span>
                <a class="nav-link" href="<?= e(app_url('logout.php?scope=admin')); ?>">Logout</a>
            <?php elseif ($user): ?>
                <span class="nav-user nav-user-with-avatar">
                    <span class="nav-avatar-frame">
                        <img class="nav-avatar" src="<?= e(app_url(user_avatar_path($user))); ?>" alt="<?= e($user['full_name']); ?> profile picture">
                    </span>
                    <span><?= e($user['full_name']); ?></span>
                </span>
                <a class="nav-link" href="<?= e(app_url('logout.php')); ?>">Logout</a>
            <?php else: ?>
                <a class="button button-light button-small header-action-button login-top-button<?= $currentPath === 'login.php' ? ' is-active' : ''; ?>" href="<?= e(app_url('login.php')); ?>">Login</a>
                <a class="button button-sand button-small header-action-button register-top-button<?= $currentPath === 'register.php' ? ' is-active' : ''; ?>" href="<?= e(app_url('register.php')); ?>">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']); ?>">
        <div class="container">
            <?= e($flash['message']); ?>
        </div>
    </div>
<?php endif; ?>
<main class="page-shell">
