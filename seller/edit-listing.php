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
$pageTitle = 'Pastimes | Edit Listing';
$listing = db_one('SELECT * FROM tblClothes WHERE clothes_id = ? AND seller_id = ?', 'ii', [(int) ($_GET['id'] ?? 0), (int) $user['user_id']]);

if (!$listing) {
    set_flash('Listing not found.', 'error');
    redirect_to('seller/my-listings.php');
}

if (is_post()) {
    $title = trim($_POST['title'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $sizeLabel = trim($_POST['size_label'] ?? '');
    $conditionRating = (int) ($_POST['condition_rating'] ?? 3);
    $sellPrice = (float) ($_POST['sell_price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    $imagePath = (string) $listing['image_path'];
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
            execute_sql('UPDATE tblProductImage SET image_path = ? WHERE clothes_id = ? AND sort_order = 1', 'si', [$imagePath, (int) $listing['clothes_id']]);
        }
    }

    execute_sql(
        'UPDATE tblClothes SET title = ?, brand = ?, category = ?, gender = ?, size_label = ?, condition_rating = ?, sell_price = ?, description = ?, image_path = ? WHERE clothes_id = ? AND seller_id = ?',
        'sssssidssii',
        [$title, $brand, $category, $gender, $sizeLabel, $conditionRating, $sellPrice, $description, $imagePath, (int) $listing['clothes_id'], (int) $user['user_id']]
    );

    set_flash('Listing updated successfully.', 'success');
    redirect_to('seller/my-listings.php');
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card">
            <h1>Edit Listing</h1>
            <form method="post" enctype="multipart/form-data">
                <label>Title</label>
                <input name="title" value="<?= e($listing['title']); ?>" required>
                <div class="form-row">
                    <div><label>Brand</label><input name="brand" value="<?= e($listing['brand']); ?>" required></div>
                    <div><label>Category</label><input name="category" value="<?= e($listing['category']); ?>" required></div>
                </div>
                <div class="form-row">
                    <div><label>Gender</label><input name="gender" value="<?= e($listing['gender']); ?>" required></div>
                    <div><label>Size</label><input name="size_label" value="<?= e($listing['size_label']); ?>" required></div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Condition Rating</label>
                        <select name="condition_rating" required>
                            <?php foreach ([1 => 'Fair', 2 => 'Good', 3 => 'Very Good', 4 => 'Excellent', 5 => 'Like New'] as $rating => $label): ?>
                                <option value="<?= e((string) $rating); ?>" <?= (int) $listing['condition_rating'] === $rating ? 'selected' : ''; ?>><?= e($rating . ' - ' . $label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div><label>Sell Price</label><input name="sell_price" type="number" step="0.01" min="10" value="<?= e((string) $listing['sell_price']); ?>" required></div>
                </div>
                <label>Replace Image</label>
                <input type="file" name="product_image" accept="image/*">
                <label>Description</label>
                <textarea name="description" required><?= e($listing['description']); ?></textarea>
                <input type="submit" value="Save Listing">
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
