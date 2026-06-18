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
$pageTitle = 'Pastimes | Messages';
$errors = [];

if (is_post()) {
    $receiverId = (int) ($_POST['receiver_user_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $messageBody = trim($_POST['message_body'] ?? '');
    $relatedOrderId = (int) ($_POST['related_order_id'] ?? 0);

    if ($receiverId <= 0 || $title === '' || $messageBody === '') {
        $errors[] = 'Select a recipient, title, and message body.';
    } else {
        if ($relatedOrderId > 0) {
            execute_sql(
                'INSERT INTO tblMessage (sender_user_id, receiver_user_id, related_order_id, title, message_body, is_broadcast, is_read)
                 VALUES (?, ?, ?, ?, ?, 0, 0)',
                'iiiss',
                [(int) $user['user_id'], $receiverId, $relatedOrderId, $title, $messageBody]
            );
        } else {
            execute_sql(
                'INSERT INTO tblMessage (sender_user_id, receiver_user_id, title, message_body, is_broadcast, is_read)
                 VALUES (?, ?, ?, ?, 0, 0)',
                'iiss',
                [(int) $user['user_id'], $receiverId, $title, $messageBody]
            );
        }
        set_flash('Message sent successfully.', 'success');
        redirect_to('messages.php');
    }
}

$inbox = db_all(
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
     WHERE m.receiver_user_id = ? OR m.sender_user_id = ? OR m.receiver_admin_id = ? OR m.is_broadcast = 1
     ORDER BY m.created_at DESC',
    'iii',
    [(int) $user['user_id'], (int) $user['user_id'], (int) $user['user_id']]
);

$isSeller = (($user['seller_status'] ?? 'none') === 'approved' || ($user['role'] ?? '') === 'seller');
$recipientLabel = $isSeller ? 'Choose a buyer' : 'Choose a seller';
$recipientUsers = $isSeller
    ? db_all("SELECT user_id, full_name FROM tblUser WHERE customer_status = 'verified' AND is_active = 1 AND role <> 'admin' ORDER BY full_name")
    : db_all("SELECT user_id, full_name FROM tblUser WHERE seller_status = 'approved' AND is_active = 1 AND role <> 'admin' ORDER BY full_name");
$orders = db_all('SELECT order_id, order_reference FROM tblOrder WHERE user_id = ? ORDER BY created_at DESC', 'i', [(int) $user['user_id']]);

require_once __DIR__ . '/includes/header.php';
?>
<section class="section section-tight">
    <div class="container page-title-block">
        <div>
            <h1>Messages</h1>
            <p>Internal marketplace conversations and admin notices.</p>
        </div>
    </div>

    <div class="container seller-layout">
        <section class="dashboard-card">
            <h2>Inbox</h2>
            <?php foreach ($inbox as $message): ?>
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
                    <a class="text-link" href="<?= e(app_url('conversation.php?id=' . (int) $message['message_id'])); ?>">Open conversation</a>
                </article>
            <?php endforeach; ?>

            <?php if ($inbox === []): ?>
                <div class="empty-state">No messages yet.</div>
            <?php endif; ?>
        </section>

        <aside class="dashboard-card">
            <h2>Send Message</h2>
            <?php foreach ($errors as $error): ?>
                <div class="inline-alert"><?= e($error); ?></div>
            <?php endforeach; ?>
            <form method="post" class="stacked-form">
                <label>Recipient
                    <select name="receiver_user_id" required>
                        <option value=""><?= e($recipientLabel); ?></option>
                        <?php foreach ($recipientUsers as $recipient): ?>
                            <option value="<?= e((string) $recipient['user_id']); ?>"><?= e($recipient['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Related Order
                    <select name="related_order_id">
                        <option value="">Optional</option>
                        <?php foreach ($orders as $order): ?>
                            <option value="<?= e((string) $order['order_id']); ?>"><?= e($order['order_reference'] ?: generate_order_reference_number((int) $order['order_id'])); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Title
                    <input name="title" required>
                </label>
                <label>Message
                    <textarea name="message_body" required></textarea>
                </label>
                <button class="button button-dark button-block" type="submit">Send Message</button>
            </form>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
