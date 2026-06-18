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
$pageTitle = 'Pastimes | Conversation';
$messageId = (int) ($_GET['id'] ?? 0);
$threadMessage = $messageId > 0 ? db_one(
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
     WHERE m.message_id = ? AND (m.receiver_user_id = ? OR m.sender_user_id = ? OR m.is_broadcast = 1)',
    'iii',
    [$messageId, (int) $user['user_id'], (int) $user['user_id']]
) : null;

$counterpartId = null;
$counterpartType = null;
$counterpartName = 'Conversation';

if ($threadMessage) {
    if ((int) ($threadMessage['sender_user_id'] ?? 0) === (int) $user['user_id'] && (int) ($threadMessage['receiver_user_id'] ?? 0) > 0) {
        $counterpartType = 'user';
        $counterpartId = (int) $threadMessage['receiver_user_id'];
        $counterpartName = (string) ($threadMessage['receiver_user_name'] ?? 'Conversation');
    } elseif ((int) ($threadMessage['sender_user_id'] ?? 0) === (int) $user['user_id'] && (int) ($threadMessage['receiver_admin_id'] ?? 0) > 0) {
        $counterpartType = 'admin';
        $counterpartId = (int) $threadMessage['receiver_admin_id'];
        $counterpartName = (string) ($threadMessage['receiver_admin_name'] ?? 'Admin');
    } elseif ((int) ($threadMessage['receiver_user_id'] ?? 0) === (int) $user['user_id'] && (int) ($threadMessage['sender_user_id'] ?? 0) > 0) {
        $counterpartType = 'user';
        $counterpartId = (int) $threadMessage['sender_user_id'];
        $counterpartName = (string) ($threadMessage['sender_user_name'] ?? 'Conversation');
    } elseif ((int) ($threadMessage['receiver_user_id'] ?? 0) === (int) $user['user_id'] && (int) ($threadMessage['sender_admin_id'] ?? 0) > 0) {
        $counterpartType = 'admin';
        $counterpartId = (int) $threadMessage['sender_admin_id'];
        $counterpartName = (string) ($threadMessage['sender_admin_name'] ?? 'Admin');
    } else {
        $counterpartName = 'Broadcast';
    }
}

if (is_post() && $counterpartId && $counterpartType) {
    $replyTitle = trim($_POST['title'] ?? '');
    $replyBody = trim($_POST['message_body'] ?? '');
    if ($replyTitle !== '' && $replyBody !== '') {
        if ($counterpartType === 'admin') {
            execute_sql(
                'INSERT INTO tblMessage (sender_user_id, receiver_admin_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, ?, ?, 0, 0)',
                'iiss',
                [(int) $user['user_id'], $counterpartId, $replyTitle, $replyBody]
            );
        } else {
            execute_sql(
                'INSERT INTO tblMessage (sender_user_id, receiver_user_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, ?, ?, 0, 0)',
                'iiss',
                [(int) $user['user_id'], $counterpartId, $replyTitle, $replyBody]
            );
        }
        set_flash('Reply sent.', 'success');
        redirect_to('conversation.php?id=' . $messageId);
    }
}

$thread = [];
if ($counterpartId && $counterpartType === 'admin') {
    $thread = db_all(
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
         WHERE (m.sender_user_id = ? AND m.receiver_admin_id = ?)
            OR (m.sender_admin_id = ? AND m.receiver_user_id = ?)
         ORDER BY m.created_at ASC',
        'iiii',
        [(int) $user['user_id'], $counterpartId, $counterpartId, (int) $user['user_id']]
    );
} elseif ($counterpartId) {
    $thread = db_all(
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
         WHERE (m.sender_user_id = ? AND m.receiver_user_id = ?) OR (m.sender_user_id = ? AND m.receiver_user_id = ?)
         ORDER BY m.created_at ASC',
        'iiii',
        [(int) $user['user_id'], $counterpartId, $counterpartId, (int) $user['user_id']]
    );
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card">
            <h1><?= e($counterpartName); ?></h1>
            <?php if ($threadMessage && !$counterpartId): ?>
                <div class="message-snippet">
                    <p><strong><?= e($threadMessage['title']); ?></strong></p>
                    <p class="muted">From: <?= e($threadMessage['sender_admin_name'] ?? $threadMessage['sender_user_name'] ?? 'Admin'); ?></p>
                    <p><?= nl2br(e($threadMessage['message_body'])); ?></p>
                </div>
            <?php endif; ?>

            <?php foreach ($thread as $message): ?>
                <div class="message-snippet">
                    <div class="message-header">
                        <strong><?= e($message['title']); ?></strong>
                        <span><?= e($message['created_at']); ?></span>
                    </div>
                    <p><?= nl2br(e($message['message_body'])); ?></p>
                </div>
            <?php endforeach; ?>

            <?php if ($counterpartId && $counterpartType): ?>
                <form method="post" class="stacked-form">
                    <label>Reply Title
                        <input name="title" required>
                    </label>
                    <label>Reply Message
                        <textarea name="message_body" required></textarea>
                    </label>
                    <button class="button button-dark button-block" type="submit">Send Reply</button>
                </form>
            <?php else: ?>
                <p class="muted">Broadcast messages are one-way announcements. Direct admin and user conversations can be replied to.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
