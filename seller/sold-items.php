<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_seller_login();
$pageTitle = 'Pastimes | Sold Items';
$soldItems = db_all("SELECT * FROM tblClothes WHERE seller_id = ? AND status = 'sold' ORDER BY updated_at DESC", 'i', [(int) $user['user_id']]);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1>Sold Items</h1>
        <?php if ($soldItems): ?>
            <table>
                <thead><tr><th>Title</th><th>Price</th><th>Updated</th></tr></thead>
                <tbody>
                <?php foreach ($soldItems as $item): ?>
                    <tr>
                        <td><?= e($item['title']); ?></td>
                        <td>R<?= e(number_format((float) $item['sell_price'], 2)); ?></td>
                        <td><?= e($item['updated_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">No sold items yet.</div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
