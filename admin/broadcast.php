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
$pageTitle = 'Pastimes | Broadcast';
$errors = [];

if (is_post()) {
    $title = trim($_POST['title'] ?? '');
    $messageBody = trim($_POST['message_body'] ?? '');
    if ($title === '' || $messageBody === '') {
        $errors[] = 'Title and message body are required.';
    } else {
        $users = db_all("SELECT user_id FROM tblUser WHERE role <> 'admin'");
        foreach ($users as $user) {
            execute_sql('INSERT INTO tblMessage (sender_admin_id, receiver_user_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, ?, ?, 1, 0)', 'iiss', [(int) $admin['admin_id'], (int) $user['user_id'], $title, $messageBody]);
        }
        set_flash('Broadcast sent to all users.', 'success');
        redirect_to('admin/messages.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card">
            <h1>Broadcast Message</h1>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-error"><?= e($error); ?></div>
            <?php endforeach; ?>
            <form method="post">
                <label>Title</label>
                <input name="title" required>
                <label>Message</label>
                <textarea name="message_body" required></textarea>
                <input type="submit" value="Send Broadcast">
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
