<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Pastimes | Home';

try {
    $featuredProducts = db_all("SELECT * FROM tblClothes WHERE status = 'approved' ORDER BY created_at DESC LIMIT 4");
} catch (Throwable $exception) {
    $featuredProducts = [];
}

if ($featuredProducts === []) {
    $featuredProducts = [
        ['clothes_id' => 1, 'brand' => 'Zara', 'title' => 'Floral Summer Dress', 'size_label' => 'M', 'category' => 'Dresses', 'condition_rating' => 4, 'sell_price' => 450, 'image_path' => 'assets/images/products/floral-dress.jpg'],
        ['clothes_id' => 2, 'brand' => "Levi's", 'title' => 'Vintage Denim Jacket', 'size_label' => 'L', 'category' => 'Outerwear', 'condition_rating' => 5, 'sell_price' => 750, 'image_path' => 'assets/images/products/denim-jacket.jpg'],
        ['clothes_id' => 3, 'brand' => 'H&M', 'title' => 'Silk Blouse', 'size_label' => 'S', 'category' => 'Tops', 'condition_rating' => 4, 'sell_price' => 280, 'image_path' => 'assets/images/products/silk-blouse.jpg'],
        ['clothes_id' => 4, 'brand' => 'Mango', 'title' => 'Tailored Wool Coat', 'size_label' => 'M', 'category' => 'Outerwear', 'condition_rating' => 5, 'sell_price' => 1200, 'image_path' => 'assets/images/products/wool-coat.jpg'],
    ];
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="hero hero-home" style="background-image: linear-gradient(rgba(30, 58, 58, 0.52), rgba(30, 58, 58, 0.52)), url('<?= e(app_url('assets/images/banners/hero-chair.jpg')); ?>');">
    <div class="container hero-content">
        <p class="hero-kicker">Type of eShop: <strong>Pre-Loved Fashion Marketplace</strong></p>
        <h1>Pre-Loved.<br>Perfectly Yours.</h1>
        <p>Discover curated second-hand branded clothing. Give pre-loved fashion a new home.</p>
        <div class="hero-actions">
            <a class="button button-sand" href="<?= e(app_url('shop.php')); ?>">Shop Now</a>
            <a class="button button-outline" href="<?= e(app_url('request-seller.php')); ?>">Sell With Us</a>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container section-heading">
        <h2>Goals</h2>
        <p>Our marketplace is built around sustainable, affordable, and local fashion</p>
    </div>
    <div class="container feature-grid">
        <article class="feature-card">
            <div class="feature-icon feature-icon-mint">S</div>
            <h3>Sustainable Fashion</h3>
            <p>Keep clothing in circulation for longer and reduce waste through reuse.</p>
        </article>
        <article class="feature-card">
            <div class="feature-icon feature-icon-sand">A</div>
            <h3>Affordable Clothing</h3>
            <p>Find quality branded pieces at friendlier prices than buying new.</p>
        </article>
        <article class="feature-card">
            <div class="feature-icon feature-icon-peach">L</div>
            <h3>Support Local Sellers</h3>
            <p>Help South African sellers earn from items they no longer need.</p>
        </article>
    </div>
</section>

<section class="section">
    <div class="container section-heading">
        <h2>Featured Items</h2>
        <p>Hand-picked pieces from our collection</p>
    </div>
    <div class="container card-grid card-grid-four">
        <?php foreach ($featuredProducts as $product): ?>
            <article class="product-card">
                <img src="<?= e(app_url(product_image_path($product))); ?>" alt="<?= e($product['title']); ?>">
                <div class="product-card-body">
                    <span class="product-brand"><?= e($product['brand']); ?></span>
                    <h3><?= e($product['title']); ?></h3>
                    <div class="product-meta-row">
                        <span>Size: <?= e($product['size_label']); ?></span>
                        <span class="rating"><?= e(rating_stars((int) $product['condition_rating'])); ?></span>
                    </div>
                    <strong class="price"><?= e(format_price((float) $product['sell_price'])); ?></strong>
                    <a class="button button-light button-block" href="<?= e(app_url('product.php?id=' . $product['clothes_id'])); ?>">View</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="centered-action">
        <a class="button button-light" href="<?= e(app_url('shop.php')); ?>">View All Items</a>
    </div>
</section>

<section class="section section-soft">
    <div class="container section-heading">
        <h2>How It Works</h2>
        <p>Simple steps to buy or sell pre-loved fashion</p>
    </div>
    <div class="container steps-grid">
        <article class="step-card">
            <div class="step-icon">ID</div>
            <span class="step-number">1</span>
            <h3>Register</h3>
            <p>Create your free account to start buying or apply to become a verified seller.</p>
        </article>
        <article class="step-card">
            <div class="step-icon">GO</div>
            <span class="step-number">2</span>
            <h3>Browse &amp; Buy</h3>
            <p>Explore curated branded second-hand clothing and find your perfect piece.</p>
        </article>
        <article class="step-card">
            <div class="step-icon">R</div>
            <span class="step-number">3</span>
            <h3>Sell With Us</h3>
            <p>List your pre-loved items and reach buyers who appreciate quality fashion.</p>
        </article>
    </div>
</section>

<section class="section">
    <div class="container section-heading">
        <h2>Why Choose Pastimes</h2>
        <p>Shop with confidence on our trusted platform</p>
    </div>
    <div class="container feature-grid">
        <article class="feature-card">
            <div class="feature-icon feature-icon-mint">OK</div>
            <h3>Verified Sellers</h3>
            <p>Seller applications and live listings are reviewed before they reach customers.</p>
        </article>
        <article class="feature-card">
            <div class="feature-icon feature-icon-sand">24</div>
            <h3>Clear Workflows</h3>
            <p>Browse, cart, checkout, message sellers, and track orders in one clean marketplace.</p>
        </article>
        <article class="feature-card">
            <div class="feature-icon feature-icon-peach">ECO</div>
            <h3>Sustainable Fashion</h3>
            <p>Reduce textile waste by giving pre-loved clothes a second life.</p>
        </article>
    </div>
</section>

<section class="section section-soft">
    <div class="container section-heading">
        <h2>Browse by Category</h2>
        <p>Find exactly what you're looking for</p>
    </div>
    <div class="container category-grid">
        <?php
        $categoryCards = [
            ['name' => 'Tops', 'image' => 'assets/images/categories/tops.jpg'],
            ['name' => 'Bottoms', 'image' => 'assets/images/categories/bottoms.jpg'],
            ['name' => 'Dresses', 'image' => 'assets/images/categories/dresses.jpg'],
            ['name' => 'Outerwear', 'image' => 'assets/images/categories/outerwear.jpg'],
            ['name' => 'Shoes', 'image' => 'assets/images/categories/shoes.jpg'],
            ['name' => 'Accessories', 'image' => 'assets/images/categories/accessories.jpg'],
        ];
        ?>
        <?php foreach ($categoryCards as $category): ?>
            <a class="category-card" href="<?= e(app_url('shop.php?category=' . urlencode($category['name']))); ?>">
                <img src="<?= e(app_url($category['image'])); ?>" alt="<?= e($category['name']); ?>">
                <span><?= e($category['name']); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
