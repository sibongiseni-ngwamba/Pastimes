<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Pastimes | Login';
$errors = [];
$accountType = strtolower(trim($_POST['account_type'] ?? $_GET['role'] ?? 'customer'));

if (!in_array($accountType, ['customer', 'admin'], true)) {
    $accountType = 'customer';
}

if (current_admin()) {
    redirect_to('admin/index.php');
}

if (Auth::isLoggedIn()) {
    redirect_to('dashboard.php');
}

if (is_post()) {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';
    $accountType = strtolower(trim($_POST['account_type'] ?? $accountType));

    if (!in_array($accountType, ['customer', 'admin'], true)) {
        $accountType = 'customer';
    }

    if ($identity === '' || $password === '') {
        $errors[] = 'Username or email address, and password are required.';
    } else {
        try {
            if ($accountType === 'admin') {
                Auth::loginAdmin($identity, $password);
                set_flash('Administrator logged in successfully.', 'success');
                redirect_to('admin/index.php');
            } else {
                Auth::loginUser($identity, $password);
                set_flash('Login successful.', 'success');
                redirect_to('dashboard.php');
            }
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-section">
    <div class="auth-card auth-card-wide">
        <h1>Welcome Back</h1>
        <p>Sign in to your Pastimes account</p>
        <?php if (isset($_SESSION['last_checkout'])): ?>
            <div class="inline-alert inline-alert-success">
                <strong>Your order has been placed successfully.</strong><br>
                Order Number: <?= e((string) ($_SESSION['last_checkout']['order_reference'] ?? '')); ?><br>
                Session ID: <?= e((string) ($_SESSION['last_checkout']['session_reference'] ?? '')); ?><br>
                Please sign in again whenever you are ready to continue shopping.
            </div>
            <?php unset($_SESSION['last_checkout']); ?>
        <?php endif; ?>
        <?php foreach ($errors as $error): ?>
            <div class="inline-alert"><?= e($error); ?></div>
        <?php endforeach; ?>
        <form method="post" novalidate>
            <label>Sign In As
                <select name="account_type">
                    <option value="customer" <?= $accountType === 'customer' ? 'selected' : ''; ?>>Customer</option>
                    <option value="admin" <?= $accountType === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                </select>
            </label>

            <label>Username or Email *
                <input name="identity" type="text" value="<?= e(old('identity')); ?>" required placeholder="Enter your username or email">
            </label>

            <label>Password *
                <div class="password-field">
                    <input name="password" type="password" required placeholder="Enter your password">
                    <button type="button" class="password-toggle" data-toggle-password>Show</button>
                </div>
            </label>

            <div class="auth-meta">
                <label class="checkbox-row">
                    <input type="checkbox" name="remember_me" value="1">
                    <span>Remember me</span>
                </label>
                <a class="text-link" href="<?= e(app_url('forgot-password.php')); ?>">Forgot Password?</a>
            </div>
            <button class="button button-dark button-block" type="submit">Login</button>
        </form>
        <p class="auth-link-row">No account yet? <a href="<?= e(app_url('register.php')); ?>">Create one</a></p>
        <?php if ($accountType === 'admin'): ?>
            <p class="auth-link-row">Administrator access opens the admin panel after sign in.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
