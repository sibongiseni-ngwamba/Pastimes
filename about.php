<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Pastimes | About';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero hero-slim hero-teal">
    <div class="container hero-content hero-content-narrow">
        <h1>About Pastimes</h1>
        <p>Giving pre-loved fashion a second life</p>
    </div>
</section>

<section class="section">
    <div class="container about-layout">
        <div>
            <h2>Our Story</h2>
            <p>Pastimes connects South African fashion lovers to buy and sell quality second-hand branded clothing.</p>
            <p>We believe sustainable fashion should be accessible, trusted, and easy to shop, with verified users, approved listings, and clear admin oversight.</p>
        </div>
        <div class="about-image-placeholder">
            <img src="<?= e(app_url('assets/images/banners/about-story.jpg')); ?>" alt="Sustainable fashion display">
        </div>
    </div>
</section>

<section class="section section-soft" id="mission">
    <div class="container quote-card">
        <h2>Our Mission</h2>
        <blockquote>To create a trusted community where pre-owned fashion finds new homes, reducing textile waste one garment at a time.</blockquote>
    </div>
</section>

<section class="section">
    <div class="container section-heading">
        <h2>Our Values</h2>
    </div>
    <div class="container feature-grid">
        <article class="feature-card">
            <div class="feature-icon feature-icon-mint">ECO</div>
            <h3>Sustainability</h3>
            <p>Every purchase extends a garment's life cycle, reducing fashion waste and environmental impact.</p>
        </article>
        <article class="feature-card">
            <div class="feature-icon feature-icon-sand">YOU</div>
            <h3>Community</h3>
            <p>We support buyers, verified sellers, and admins with straightforward marketplace workflows.</p>
        </article>
        <article class="feature-card">
            <div class="feature-icon feature-icon-peach">TOP</div>
            <h3>Quality</h3>
            <p>Seller verification and listing approval help keep the shopping experience focused and trusted.</p>
        </article>
        </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
