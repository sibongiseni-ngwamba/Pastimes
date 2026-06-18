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
$pageTitle = 'Pastimes | Admin Messages';
$errors = [];

if (is_post()) {
    $receiverId = (int) ($_POST['receiver_user_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $messageBody = trim($_POST['message_body'] ?? '');

    if ($receiverId <= 0 || $title === '' || $messageBody === '') {
        $errors[] = 'Select a recipient, title, and message body.';
    } else {
        execute_sql(
            'INSERT INTO tblMessage (sender_admin_id, receiver_user_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, ?, ?, 0, 0)',
            'iiss',
            [(int) $admin['admin_id'], $receiverId, $title, $messageBody]
        );
        set_flash('Admin message sent successfully.', 'success');
        redirect_to('admin/messages.php');
    }
}

$messages = db_all(
    'SELECT m.*,
            sender_user.full_name AS sender_user_name,
            sender_admin.full_name AS sender_admin_name,
            receiver_user.full_name AS receiver_user_name,
            receiver_admin.full_name AS receiver_admin_name
     FROM tblMessage m
     LEFT JOIN tblUser sender_user ON sender_user.user_id = m.sender_user_id
     LEFT JOIN tblAdmin sender_admin ON sender_admin.admin_id = m.sender_admin_id
     LEFT JOIN tblUser receiver_user ON receiver_user.user_id = m.receiver_user_id
     LEFT JOIN tblAdmin receiver_admin ON receiver_admin.admin_id = m.receiver_admin_id
     ORDER BY m.created_at DESC'
);

$recipients = db_all("SELECT user_id, full_name, role, seller_status FROM tblUser WHERE is_active = 1 AND role <> 'admin' ORDER BY full_name");

require_once __DIR__ . '/../includes/header.php';
?>
<section class="section section-tight">
    <div class="container page-title-block">
        <div>
            <h1>Admin Communication Center</h1>
            <p>Send delivery follow-ups, resolve order issues, and speak to buyers or sellers directly.</p>
        </div>
        <div class="dashboard-actions">
            <a class="button button-sand" href="<?= e(app_url('admin/broadcast.php')); ?>">Broadcast Message</a>
        </div>
    </div>

    <div class="container seller-layout">
        <section class="dashboard-card">
            <h2>All Conversations</h2>
            <?php foreach ($messages as $message): ?>
                <article class="message-snippet">
                    <div class="message-header">
                        <strong><?= e($message['title']); ?></strong>
                        <span><?= e($message['created_at']); ?></span>
                    </div>
                    <p class="muted">
                        From: <?= e($message['sender_user_name'] ?? $message['sender_admin_name'] ?? 'Admin'); ?> |
                        To: <?= e($message['receiver_user_name'] ?? $message['receiver_admin_name'] ?? 'Broadcast'); ?>
                    </p>
                    <p><?= e(substr((string) $message['message_body'], 0, 140)); ?><?= strlen((string) $message['message_body']) > 140 ? '...' : ''; ?></p>
                </article>
            <?php endforeach; ?>
        </section>

        <aside class="dashboard-card">
            <h2>Send Message</h2>
            <?php foreach ($errors as $error): ?>
                <div class="inline-alert"><?= e($error); ?></div>
            <?php endforeach; ?>
            <form method="post" class="stacked-form">
                <label>Recipient
                    <select name="receiver_user_id" required>
                        <option value="">Select buyer or seller</option>
                        <?php foreach ($recipients as $recipient): ?>
                            <option value="<?= e((string) $recipient['user_id']); ?>">
                                <?= e($recipient['full_name'] . ' (' . $recipient['role'] . ($recipient['seller_status'] === 'approved' ? ', seller' : '') . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Title
                    <input name="title" required>
                </label>
                <label>Message
                    <textarea name="message_body" required></textarea>
                </label>
                <button class="button button-dark button-block" type="submit">Send</button>
            </form>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
