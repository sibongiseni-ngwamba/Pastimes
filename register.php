<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Pastimes | Register';
$errors = [];

if (Auth::isLoggedIn()) {
    redirect_to('dashboard.php');
}

if (is_post()) {
    $confirmPassword = $_POST['confirm_password'] ?? '';
    if (($password = (string) ($_POST['password'] ?? '')) !== $confirmPassword) {
        $errors[] = 'Password confirmation does not match.';
    } else {
        try {
            Auth::registerUser($_POST);
            set_flash('Registration submitted. Your account is pending administrator verification before login.', 'success');
            redirect_to('login.php');
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-section">
    <div class="auth-card auth-card-wide">
        <h1>Create Your Pastimes Account</h1>
        <p>Join our community of sustainable fashion lovers</p>
        <?php foreach ($errors as $error): ?>
            <div class="inline-alert"><?= e($error); ?></div>
        <?php endforeach; ?>
        <form method="post" novalidate>
            <label>Full Name *
                <input name="full_name" type="text" value="<?= e(old('full_name')); ?>" required placeholder="Enter your full name">
            </label>

            <label>Email Address *
                <input name="email" type="email" value="<?= e(old('email')); ?>" required placeholder="your@email.com">
            </label>

            <label>Username *
                <input name="username" type="text" value="<?= e(old('username')); ?>" required placeholder="Choose a username">
            </label>

            <label>Phone Number
                <input name="phone_number" type="text" value="<?= e(old('phone_number')); ?>" placeholder="Optional contact number">
            </label>

            <label>Password *
                <div class="password-field">
                    <input name="password" type="password" minlength="8" required placeholder="Create a strong password">
                    <button type="button" class="password-toggle" data-toggle-password>Show</button>
                </div>
            </label>

            <label>Confirm Password *
                <div class="password-field">
                    <input name="confirm_password" type="password" minlength="8" required placeholder="Confirm your password">
                    <button type="button" class="password-toggle" data-toggle-password>Show</button>
                </div>
            </label>

            <label class="checkbox-row">
                <input type="checkbox" name="terms" value="1" required>
                <span>I agree to the Terms &amp; Conditions and Privacy Policy</span>
            </label>

            <button class="button button-dark button-block" type="submit">Register</button>
        </form>
        <p class="auth-link-row">Already registered? <a href="<?= e(app_url('login.php')); ?>">Log in</a></p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
