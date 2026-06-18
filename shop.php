<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Pastimes | Shop';
$filters = [
    'category' => trim($_GET['category'] ?? ''),
    'brand' => trim($_GET['brand'] ?? ''),
    'size_label' => trim($_GET['size_label'] ?? ''),
    'condition_rating' => trim($_GET['condition_rating'] ?? ''),
    'min_price' => trim($_GET['min_price'] ?? ''),
    'max_price' => trim($_GET['max_price'] ?? ''),
];

if (is_post() && isset($_POST['clothes_id'])) {
    Cart::AddItem((int) $_POST['clothes_id']);
    set_flash('Item added to cart.', 'success');
    redirect_to('shop.php');
}

$sql = "SELECT * FROM tblClothes WHERE status = 'approved' AND inventory_quantity > 0";
$params = [];
$types = '';

foreach ($filters as $column => $value) {
    if ($value !== '' && !in_array($column, ['min_price', 'max_price'], true)) {
        $sql .= " AND $column = ?";
        $params[] = $value;
        $types .= 's';
    }
}

if ($filters['min_price'] !== '') {
    $sql .= ' AND sell_price >= ?';
    $params[] = (float) $filters['min_price'];
    $types .= 'd';
}

if ($filters['max_price'] !== '') {
    $sql .= ' AND sell_price <= ?';
    $params[] = (float) $filters['max_price'];
    $types .= 'd';
}

$sql .= ' ORDER BY created_at DESC';

try {
    $products = db_all($sql, $types, $params);
} catch (Throwable $exception) {
    $products = [];
}

try {
    $categories = array_column(db_all("SELECT DISTINCT category FROM tblClothes WHERE status = 'approved' ORDER BY category"), 'category');
    $brands = array_column(db_all("SELECT DISTINCT brand FROM tblClothes WHERE status = 'approved' ORDER BY brand"), 'brand');
} catch (Throwable $exception) {
    $categories = ['Dresses', 'Tops', 'Outerwear', 'Shoes', 'Accessories', 'Bottoms'];
    $brands = ['Zara', "Levi's", 'H&M', 'Mango', 'Aldo', 'Country Road'];
}

$sizes = ['XS', 'S', 'M', 'L', 'XL', '8'];
$conditions = [
    1 => 'Fair',
    2 => 'Good',
    3 => 'Very Good',
    4 => 'Excellent',
    5 => 'Like New',
];

require_once __DIR__ . '/includes/header.php';
?>
<section class="section section-tight">
    <div class="container">
        <div class="page-title-block">
            <div>
            <h1>Shop</h1>
                <p>Browse our collection of curated pre-loved fashion</p>
            </div>
        </div>
        <div class="shop-layout">
            <aside class="filters-panel">
                <h2>Filters</h2>
                <form method="get" action="<?= e(app_url('shop.php')); ?>">
                    <label>Category
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= e($category); ?>" <?= $filters['category'] === $category ? 'selected' : ''; ?>><?= e($category); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <div class="filter-group">
                        <span class="filter-label">Brand</span>
                        <?php foreach ($brands as $brand): ?>
                            <label class="checkbox-row">
                                <input type="radio" name="brand" value="<?= e($brand); ?>" <?= $filters['brand'] === $brand ? 'checked' : ''; ?>>
                                <span><?= e($brand); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="filter-group">
                        <span class="filter-label">Size</span>
                        <div class="size-pills">
                            <?php foreach ($sizes as $size): ?>
                                <label class="size-pill">
                                    <input type="radio" name="size_label" value="<?= e($size); ?>" <?= $filters['size_label'] === $size ? 'checked' : ''; ?>>
                                    <span><?= e($size); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <label>Condition
                        <select name="condition_rating">
                            <option value="">Any Condition</option>
                            <?php foreach ($conditions as $rating => $label): ?>
                                <option value="<?= e((string) $rating); ?>" <?= $filters['condition_rating'] === (string) $rating ? 'selected' : ''; ?>><?= e($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <div class="form-row">
                        <label>Min Price
                            <input type="number" name="min_price" min="0" step="1" value="<?= e($filters['min_price']); ?>" placeholder="0">
                        </label>
                        <label>Max Price
                            <input type="number" name="max_price" min="0" step="1" value="<?= e($filters['max_price']); ?>" placeholder="5000">
                        </label>
                    </div>

                    <div class="filter-actions">
                        <button class="button button-dark button-block" type="submit">Apply Filters</button>
                        <a class="button button-light button-block" href="<?= e(app_url('shop.php')); ?>">Reset</a>
                    </div>
                </form>
            </aside>

            <section class="product-results">
                <div class="results-toolbar">
                    <span><?= e((string) count($products)); ?> items found</span>
                </div>

                <div class="card-grid card-grid-three">
                    <?php foreach ($products as $product): ?>
                        <article class="product-card">
                            <img src="<?= e(app_url(product_image_path($product))); ?>" alt="<?= e($product['title']); ?>">
                            <div class="product-card-body">
                                <span class="product-brand"><?= e($product['brand']); ?></span>
                                <h3><?= e($product['title']); ?></h3>
                                <div class="product-meta-row">
                                    <span><?= e($product['category']); ?> | Size: <?= e($product['size_label']); ?></span>
                                    <span class="rating"><?= e(rating_stars((int) $product['condition_rating'])); ?></span>
                                </div>
                                <strong class="price"><?= e(format_price((float) $product['sell_price'])); ?></strong>
                                <div class="table-actions">
                                    <a class="button button-light" href="<?= e(app_url('product.php?id=' . $product['clothes_id'])); ?>">View</a>
                                    <form method="post">
                                        <input type="hidden" name="clothes_id" value="<?= e((string) $product['clothes_id']); ?>">
                                        <button class="button button-dark" type="submit" data-sell-price="<?= e(number_format((float) $product['sell_price'], 2)); ?>">Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    <?php if ($products === []): ?>
                        <div class="empty-state">
                            <h3>No items matched those filters.</h3>
                            <p>Try another category, brand, or size.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
