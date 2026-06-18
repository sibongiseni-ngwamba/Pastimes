<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$user = require_user_login();
ensure_user_profile_image_column();
$pageTitle = 'Pastimes | Profile';
$errors = [];
$currentProfileImagePath = (string) ($user['profile_image_path'] ?? '');

if (is_post()) {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phoneNumber = trim($_POST['phone_number'] ?? '');
    $uploadedProfileImagePath = null;

    if ($fullName === '' || $email === '') {
        $errors[] = 'Full name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    } else {
        $duplicate = db_one('SELECT user_id FROM tblUser WHERE email = ? AND user_id <> ?', 'si', [$email, (int) $user['user_id']]);
        if ($duplicate) {
            $errors[] = 'That email address is already in use by another account.';
        } else {
            if (isset($_FILES['profile_picture']) && (int) ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                try {
                    $uploadedProfileImagePath = store_profile_image_upload($_FILES['profile_picture']);
                } catch (Throwable $exception) {
                    $errors[] = $exception->getMessage();
                }
            }

            if ($errors === []) {
                $parts = preg_split('/\s+/', $fullName, 2);
                $firstName = $parts[0] ?? $fullName;
                $lastName = $parts[1] ?? ($parts[0] ?? '');

                try {
                    if ($uploadedProfileImagePath !== null) {
                        execute_sql(
                            'UPDATE tblUser SET full_name = ?, first_name = ?, last_name = ?, email = ?, phone_number = ?, profile_image_path = ? WHERE user_id = ?',
                            'ssssssi',
                            [$fullName, $firstName, $lastName, $email, $phoneNumber, $uploadedProfileImagePath, (int) $user['user_id']]
                        );
                    } else {
                        execute_sql(
                            'UPDATE tblUser SET full_name = ?, first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE user_id = ?',
                            'sssssi',
                            [$fullName, $firstName, $lastName, $email, $phoneNumber, (int) $user['user_id']]
                        );
                    }

                    if ($uploadedProfileImagePath !== null) {
                        delete_profile_image_file($currentProfileImagePath);
                    }

                    set_flash('Profile updated successfully.', 'success');
                    redirect_to('profile.php');
                } catch (Throwable $exception) {
                    if ($uploadedProfileImagePath !== null) {
                        delete_profile_image_file($uploadedProfileImagePath);
                    }

                    $errors[] = $exception->getMessage();
                }
            } elseif ($uploadedProfileImagePath !== null) {
                delete_profile_image_file($uploadedProfileImagePath);
            }
        }
    }
}

$user = current_user();
$avatarPath = user_avatar_path($user);
$formFullName = old('full_name', $user['full_name']);
$formEmail = old('email', $user['email']);
$formPhoneNumber = old('phone_number', (string) ($user['phone_number'] ?? ''));
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card profile-card">
            <div class="profile-head">
                <span class="profile-avatar-frame">
                    <img class="profile-avatar" src="<?= e(app_url($avatarPath)); ?>" alt="<?= e($user['full_name']); ?> profile picture">
                </span>
                <div>
                    <h1>Profile</h1>
                    <p>Update your account details and upload a profile picture.</p>
                </div>
            </div>

            <div class="mini-card">
                <strong><?= e($user['full_name']); ?></strong><br>
                <span class="muted"><?= e($user['username']); ?> | <?= e($user['email']); ?></span><br>
                <span class="muted">Role: <?= e($user['role']); ?> | Seller: <?= e($user['seller_status']); ?></span>
            </div>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-error"><?= e($error); ?></div>
            <?php endforeach; ?>

            <form method="post" enctype="multipart/form-data" class="profile-form">
                <label>Profile Picture
                    <input class="profile-picture-input" name="profile_picture" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
                </label>

                <label>Full Name *
                    <input name="full_name" value="<?= e($formFullName); ?>" required>
                </label>

                <label>Email *
                    <input name="email" type="email" value="<?= e($formEmail); ?>" required>
                </label>

                <label>Phone Number
                    <input name="phone_number" value="<?= e($formPhoneNumber); ?>">
                </label>

                <input type="submit" value="Save Changes">
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
