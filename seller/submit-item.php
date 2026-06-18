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
$pageTitle = 'Pastimes | Submit Item';
$errors = [];

if (is_post()) {
    $title = trim($_POST['title'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $sizeLabel = trim($_POST['size_label'] ?? '');
    $conditionRating = (int) ($_POST['condition_rating'] ?? 3);
    $sellPrice = (float) ($_POST['sell_price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $imagePath = trim($_POST['image_path'] ?? 'assets/images/products/fallback-product.jpg');

    if ($title === '' || $brand === '' || $category === '' || $gender === '' || $sizeLabel === '' || $description === '' || $sellPrice <= 0) {
        $errors[] = 'Please complete all required listing fields.';
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

        execute_sql(
            'INSERT INTO tblClothes (seller_id, title, brand, category, gender, size_label, condition_rating, sell_price, description, image_path, inventory_quantity, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, "pending")',
            'isssssidss',
            [(int) $user['user_id'], $title, $brand, $category, $gender, $sizeLabel, $conditionRating, $sellPrice, $description, $imagePath]
        );

        $listingId = (int) db()->insert_id;
        execute_sql(
            'INSERT INTO tblProductImage (clothes_id, image_path, sort_order, alt_text) VALUES (?, ?, 1, ?)',
            'iss',
            [$listingId, $imagePath, $title]
        );

        set_flash('Item submitted. It now appears as pending until the administrator approves it.', 'success');
        redirect_to('seller/my-listings.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container form-wrap">
        <div class="form-card">
            <h1>Submit Item</h1>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-error"><?= e($error); ?></div>
            <?php endforeach; ?>
            <form method="post" enctype="multipart/form-data">
                <label>Title *</label>
                <input name="title" required>
                <div class="form-row">
                    <div><label>Brand *</label><input name="brand" required></div>
                    <div><label>Category *</label><input name="category" required></div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Gender *</label>
                        <select name="gender" required>
                            <option value="">Select gender</option>
                            <option value="Women">Women</option>
                            <option value="Men">Men</option>
                            <option value="Unisex">Unisex</option>
                        </select>
                    </div>
                    <div><label>Size *</label><input name="size_label" required></div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Condition Rating *</label>
                        <select name="condition_rating" required>
                            <option value="1">1 - Fair</option>
                            <option value="2">2 - Good</option>
                            <option value="3">3 - Very Good</option>
                            <option value="4" selected>4 - Excellent</option>
                            <option value="5">5 - Like New</option>
                        </select>
                    </div>
                    <div><label>Sell Price *</label><input name="sell_price" type="number" min="10" step="0.01" required></div>
                </div>
                <label>Upload Image *</label>
                <input type="file" name="product_image" accept="image/*">
                <input type="hidden" name="image_path" value="assets/images/products/fallback-product.jpg">
                <label>Description *</label>
                <textarea name="description" required></textarea>
                <input type="submit" value="Submit Listing">
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
