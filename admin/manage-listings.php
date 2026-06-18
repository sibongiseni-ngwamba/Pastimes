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
$pageTitle = 'Pastimes | Manage Listings';
$errors = [];

if (isset($_GET['delete'])) {
    execute_sql('DELETE FROM tblProductImage WHERE clothes_id = ?', 'i', [(int) $_GET['delete']]);
    execute_sql('DELETE FROM tblClothes WHERE clothes_id = ?', 'i', [(int) $_GET['delete']]);
    set_flash('Listing deleted.', 'success');
    redirect_to('admin/manage-listings.php');
}

$editListing = isset($_GET['edit']) ? db_one('SELECT * FROM tblClothes WHERE clothes_id = ?', 'i', [(int) $_GET['edit']]) : null;

if (is_post()) {
    $mode = $_POST['mode'] ?? 'add';
    $sellerId = (int) ($_POST['seller_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $sizeLabel = trim($_POST['size_label'] ?? '');
    $conditionRating = (int) ($_POST['condition_rating'] ?? 3);
    $sellPrice = (float) ($_POST['sell_price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'approved');
    $inventoryQuantity = max(1, (int) ($_POST['inventory_quantity'] ?? 1));
    $imagePath = trim($_POST['image_path'] ?? 'assets/images/products/fallback-product.jpg');

    if ($title === '' || $brand === '' || $category === '' || $gender === '' || $sizeLabel === '' || $description === '' || $sellPrice <= 0) {
        $errors[] = 'Please complete all required fields.';
    } else {
        if (!empty($_FILES['product_image']['name']) && is_uploaded_file($_FILES['product_image']['tmp_name'])) {
            $uploadDir = __DIR__ . '/../assets/images/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $fileName = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . ($extension ? '.' . strtolower($extension) : '.jpg');
            $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $destination)) {
                $imagePath = 'assets/images/uploads/' . $fileName;
            }
        }

        if ($mode === 'update') {
            $listingId = (int) ($_POST['clothes_id'] ?? 0);
            if ($sellerId > 0) {
                execute_sql(
                    'UPDATE tblClothes SET seller_id = ?, title = ?, brand = ?, category = ?, gender = ?, size_label = ?, condition_rating = ?, sell_price = ?, description = ?, image_path = ?, status = ?, inventory_quantity = ? WHERE clothes_id = ?',
                    'isssssidsssii',
                    [$sellerId, $title, $brand, $category, $gender, $sizeLabel, $conditionRating, $sellPrice, $description, $imagePath, $status, $inventoryQuantity, $listingId]
                );
            } else {
                execute_sql(
                    'UPDATE tblClothes SET seller_id = NULL, title = ?, brand = ?, category = ?, gender = ?, size_label = ?, condition_rating = ?, sell_price = ?, description = ?, image_path = ?, status = ?, inventory_quantity = ? WHERE clothes_id = ?',
                    'sssssidsssii',
                    [$title, $brand, $category, $gender, $sizeLabel, $conditionRating, $sellPrice, $description, $imagePath, $status, $inventoryQuantity, $listingId]
                );
            }

            $existingImage = db_one('SELECT image_id FROM tblProductImage WHERE clothes_id = ? ORDER BY sort_order ASC LIMIT 1', 'i', [$listingId]);
            if ($existingImage) {
                execute_sql('UPDATE tblProductImage SET image_path = ?, alt_text = ? WHERE image_id = ?', 'ssi', [$imagePath, $title, (int) $existingImage['image_id']]);
            } else {
                execute_sql('INSERT INTO tblProductImage (clothes_id, image_path, sort_order, alt_text) VALUES (?, ?, 1, ?)', 'iss', [$listingId, $imagePath, $title]);
            }

            set_flash('Listing updated successfully.', 'success');
        } else {
            execute_sql(
                'INSERT INTO tblClothes (seller_id, title, brand, category, gender, size_label, condition_rating, sell_price, description, image_path, inventory_quantity, status)
                 VALUES (' . ($sellerId > 0 ? '?' : 'NULL') . ', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $sellerId > 0 ? 'isssssidssis' : 'sssssidssis',
                $sellerId > 0 ? [$sellerId, $title, $brand, $category, $gender, $sizeLabel, $conditionRating, $sellPrice, $description, $imagePath, $inventoryQuantity, $status] : [$title, $brand, $category, $gender, $sizeLabel, $conditionRating, $sellPrice, $description, $imagePath, $inventoryQuantity, $status]
            );

            $listingId = (int) db()->insert_id;
            execute_sql('INSERT INTO tblProductImage (clothes_id, image_path, sort_order, alt_text) VALUES (?, ?, 1, ?)', 'iss', [$listingId, $imagePath, $title]);
            set_flash('Listing added successfully.', 'success');
        }

        redirect_to('admin/manage-listings.php');
    }
}

$sellers = db_all("SELECT user_id, full_name FROM tblUser WHERE seller_status = 'approved' ORDER BY full_name");
$listings = db_all('SELECT c.*, u.full_name AS seller_name FROM tblClothes c LEFT JOIN tblUser u ON u.user_id = c.seller_id ORDER BY c.created_at DESC');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container split-layout">
        <div class="content-main">
            <h1>Manage Clothing</h1>
            <table>
                <thead><tr><th>Title</th><th>Seller</th><th>Status</th><th>Price</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($listings as $listing): ?>
                    <tr>
                        <td><?= e($listing['title']); ?></td>
                        <td><?= e($listing['seller_name'] ?? 'Admin Listing'); ?></td>
                        <td><?= status_badge($listing['status']); ?></td>
                        <td><?= e(format_price((float) $listing['sell_price'])); ?></td>
                        <td>
                            <a href="<?= e(app_url('admin/manage-listings.php?edit=' . $listing['clothes_id'])); ?>">Edit</a> |
                            <a href="<?= e(app_url('admin/manage-listings.php?delete=' . $listing['clothes_id'])); ?>" onclick="return confirm('Delete this listing?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <aside class="content-side">
            <div class="form-card">
                <h3><?= $editListing ? 'Edit Clothing' : 'Add Clothing'; ?></h3>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-error"><?= e($error); ?></div>
                <?php endforeach; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="mode" value="<?= $editListing ? 'update' : 'add'; ?>">
                    <input type="hidden" name="clothes_id" value="<?= e((string) ($editListing['clothes_id'] ?? '0')); ?>">
                    <label>Seller</label>
                    <select name="seller_id">
                        <option value="0">Admin listing / no seller</option>
                        <?php foreach ($sellers as $seller): ?>
                            <option value="<?= e((string) $seller['user_id']); ?>" <?= (int) ($editListing['seller_id'] ?? 0) === (int) $seller['user_id'] ? 'selected' : ''; ?>><?= e($seller['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Title</label>
                    <input name="title" value="<?= e($editListing['title'] ?? ''); ?>" required>
                    <div class="form-row">
                        <div><label>Brand</label><input name="brand" value="<?= e($editListing['brand'] ?? ''); ?>" required></div>
                        <div><label>Category</label><input name="category" value="<?= e($editListing['category'] ?? ''); ?>" required></div>
                    </div>
                    <div class="form-row">
                        <div><label>Gender</label><input name="gender" value="<?= e($editListing['gender'] ?? ''); ?>" required></div>
                        <div><label>Size</label><input name="size_label" value="<?= e($editListing['size_label'] ?? ''); ?>" required></div>
                    </div>
                    <div class="form-row">
                        <div>
                            <label>Condition</label>
                            <select name="condition_rating" required>
                                <?php foreach ([1 => 'Fair', 2 => 'Good', 3 => 'Very Good', 4 => 'Excellent', 5 => 'Like New'] as $rating => $label): ?>
                                    <option value="<?= e((string) $rating); ?>" <?= (int) ($editListing['condition_rating'] ?? 4) === $rating ? 'selected' : ''; ?>><?= e($rating . ' - ' . $label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div><label>Price</label><input name="sell_price" type="number" step="0.01" min="10" value="<?= e((string) ($editListing['sell_price'] ?? '')); ?>" required></div>
                    </div>
                    <label>Inventory Quantity</label>
                    <input name="inventory_quantity" type="number" min="1" value="<?= e((string) ($editListing['inventory_quantity'] ?? 1)); ?>" required>
                    <label>Status</label>
                    <select name="status">
                        <?php foreach (['pending', 'approved', 'sold', 'rejected'] as $listingStatus): ?>
                            <option value="<?= e($listingStatus); ?>" <?= ($editListing['status'] ?? 'approved') === $listingStatus ? 'selected' : ''; ?>><?= e(ucfirst($listingStatus)); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Image Upload</label>
                    <input type="file" name="product_image" accept="image/*">
                    <label>Image Path</label>
                    <input name="image_path" value="<?= e($editListing['image_path'] ?? 'assets/images/products/fallback-product.jpg'); ?>">
                    <label>Description</label>
                    <textarea name="description" required><?= e($editListing['description'] ?? ''); ?></textarea>
                    <button class="button button-dark button-block" type="submit"><?= $editListing ? 'Update Clothing' : 'Add Clothing'; ?></button>
                </form>
            </div>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
