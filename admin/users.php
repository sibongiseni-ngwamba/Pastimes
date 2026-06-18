<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
$admin = require_admin_login();
$pageTitle = 'Pastimes | Admin Users';
$errors = [];

$blockAdminShopper = static function (?array $userRow): bool {
    return !$userRow || (($userRow['role'] ?? '') !== 'admin');
};

if (isset($_GET['verify'])) {
    $targetUser = db_one('SELECT user_id, role FROM tblUser WHERE user_id = ?', 'i', [(int) $_GET['verify']]);
    if (!$blockAdminShopper($targetUser)) {
        set_flash('That account is reserved for administrator shopping and cannot be changed from the customer list.', 'error');
        redirect_to('admin/users.php');
    }
    // Verifying a customer updates login eligibility and sends a visible confirmation message for the demo flow.
    execute_sql('UPDATE tblUser SET customer_status = "verified" WHERE user_id = ?', 'i', [(int) $_GET['verify']]);
    execute_sql('INSERT INTO tblMessage (sender_admin_id, receiver_user_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, "Customer verification", "Your customer profile has been verified. You may now log in.", 0, 0)', 'ii', [(int) $admin['admin_id'], (int) $_GET['verify']]);
    set_flash('Customer verified successfully.', 'success');
    redirect_to('admin/users.php');
}

if (isset($_GET['activate'])) {
    $targetUser = db_one('SELECT user_id, role FROM tblUser WHERE user_id = ?', 'i', [(int) $_GET['activate']]);
    if (!$blockAdminShopper($targetUser)) {
        set_flash('That account is reserved for administrator shopping and cannot be changed from the customer list.', 'error');
        redirect_to('admin/users.php');
    }
    execute_sql('UPDATE tblUser SET is_active = 1 WHERE user_id = ?', 'i', [(int) $_GET['activate']]);
    set_flash('User activated.', 'success');
    redirect_to('admin/users.php');
}

if (isset($_GET['deactivate'])) {
    $targetUser = db_one('SELECT user_id, role FROM tblUser WHERE user_id = ?', 'i', [(int) $_GET['deactivate']]);
    if (!$blockAdminShopper($targetUser)) {
        set_flash('That account is reserved for administrator shopping and cannot be changed from the customer list.', 'error');
        redirect_to('admin/users.php');
    }
    execute_sql('UPDATE tblUser SET is_active = 0 WHERE user_id = ?', 'i', [(int) $_GET['deactivate']]);
    set_flash('User deactivated.', 'success');
    redirect_to('admin/users.php');
}

if (isset($_GET['delete'])) {
    $targetUser = db_one('SELECT user_id, role FROM tblUser WHERE user_id = ?', 'i', [(int) $_GET['delete']]);
    if (!$blockAdminShopper($targetUser)) {
        set_flash('That account is reserved for administrator shopping and cannot be deleted from the customer list.', 'error');
        redirect_to('admin/users.php');
    }
    execute_sql('DELETE FROM tblUser WHERE user_id = ?', 'i', [(int) $_GET['delete']]);
    set_flash('User deleted.', 'success');
    redirect_to('admin/users.php');
}

if (is_post()) {
    $mode = $_POST['mode'] ?? 'add';
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $status = trim($_POST['customer_status'] ?? 'pending');

    if ($fullName === '' || $email === '' || $username === '') {
        $errors[] = 'Full name, email, and username are required.';
    } else {
        $parts = preg_split('/\s+/', $fullName, 2);
        $firstName = $parts[0] ?? $fullName;
        $lastName = $parts[1] ?? $parts[0] ?? '';

        if ($mode === 'add') {
            $tempHash = password_hash('12345678', PASSWORD_BCRYPT);
            execute_sql(
                'INSERT INTO tblUser (full_name, first_name, last_name, username, email, password_hash, phone_number, role, customer_status, seller_status, is_active)
                 VALUES (?, ?, ?, ?, ?, ?, "", "customer", ?, "none", 1)',
                'sssssss',
                [$fullName, $firstName, $lastName, $username, $email, $tempHash, $status]
            );
            set_flash('Customer added with temporary password 12345678.', 'success');
        }

        if ($mode === 'update') {
            $userId = (int) ($_POST['user_id'] ?? 0);
            $targetUser = db_one('SELECT user_id, role FROM tblUser WHERE user_id = ?', 'i', [$userId]);
            if (!$blockAdminShopper($targetUser)) {
                set_flash('That account is reserved for administrator shopping and cannot be changed from the customer list.', 'error');
                redirect_to('admin/users.php');
            }
            execute_sql('UPDATE tblUser SET full_name = ?, first_name = ?, last_name = ?, username = ?, email = ?, customer_status = ? WHERE user_id = ?', 'ssssssi', [$fullName, $firstName, $lastName, $username, $email, $status, $userId]);
            set_flash('Customer updated successfully.', 'success');
        }

        redirect_to('admin/users.php');
    }
}

$editUser = isset($_GET['edit']) ? db_one('SELECT * FROM tblUser WHERE user_id = ? AND role <> "admin"', 'i', [(int) $_GET['edit']]) : null;
$users = db_all('SELECT * FROM tblUser WHERE role <> "admin" ORDER BY created_at DESC');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container split-layout">
        <div class="content-main">
            <h1>Manage Customers</h1>
            <table>
                <thead><tr><th>Name</th><th>Username</th><th>Status</th><th>Role</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                    <td><?= e($user['full_name']); ?></td>
                    <td><?= e($user['username']); ?></td>
                    <td><?= status_badge($user['customer_status']); ?></td>
                    <td><?= e($user['role']); ?></td>
                    <td>
                        <a href="<?= e(app_url('admin/users.php?edit=' . $user['user_id'])); ?>">Edit</a> |
                        <a href="<?= e(app_url('admin/users.php?verify=' . $user['user_id'])); ?>">Verify</a> |
                        <a href="<?= e(app_url('admin/users.php?activate=' . $user['user_id'])); ?>">Activate</a> |
                        <a href="<?= e(app_url('admin/users.php?deactivate=' . $user['user_id'])); ?>">Deactivate</a> |
                        <a href="<?= e(app_url('admin/users.php?delete=' . $user['user_id'])); ?>" onclick="return confirm('Delete this user?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <aside class="content-side">
            <div class="form-card">
                <h3><?= $editUser ? 'Update Customer' : 'Add Customer'; ?></h3>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-error"><?= e($error); ?></div>
                <?php endforeach; ?>
                <form method="post">
                    <input type="hidden" name="mode" value="<?= $editUser ? 'update' : 'add'; ?>">
                    <input type="hidden" name="user_id" value="<?= e((string) ($editUser['user_id'] ?? '0')); ?>">
                    <label>Full Name</label>
                    <input name="full_name" value="<?= e($editUser['full_name'] ?? ''); ?>" required>
                    <label>Email</label>
                    <input name="email" type="email" value="<?= e($editUser['email'] ?? ''); ?>" required>
                    <label>Username</label>
                    <input name="username" value="<?= e($editUser['username'] ?? ''); ?>" required>
                    <label>Customer Status</label>
                    <select name="customer_status">
                        <?php foreach (['pending', 'verified', 'rejected'] as $status): ?>
                            <option value="<?= e($status); ?>" <?= ($editUser['customer_status'] ?? 'pending') === $status ? 'selected' : ''; ?>><?= e(ucfirst($status)); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="<?= $editUser ? 'Update User' : 'Add User'; ?>">
                </form>
            </div>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
