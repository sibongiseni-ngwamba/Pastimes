<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
?>
</main>
<footer class="site-footer">
    <?php if (!current_user() && !current_admin()): ?>
        <section class="footer-cta">
            <div class="container footer-cta-inner">
                <h2>Ready to give your wardrobe a refresh?</h2>
                <p>Join fashion lovers who shop sustainably and sell with confidence.</p>
                <a class="button button-sand" href="<?= e(app_url('register.php')); ?>">Get Started Today</a>
            </div>
        </section>
    <?php endif; ?>
    <div class="container footer-main">
        <div>
            <h3>Pastimes</h3>
            <p class="footer-tagline">Pre-Loved. Perfectly Yours.</p>
            <p>Discover curated second-hand branded clothing. Give pre-loved fashion a new home while shopping sustainably.</p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <a href="<?= e(app_url('shop.php')); ?>">Shop</a>
            <a href="<?= e(app_url('request-seller.php')); ?>">Sell With Us</a>
            <a href="<?= e(app_url('about.php')); ?>">About</a>
            <a href="<?= e(app_url('contact.php')); ?>">Contact</a>
        </div>
        <div>
            <h4>Account</h4>
            <a href="<?= e(app_url('login.php')); ?>">Login</a>
            <a href="<?= e(app_url('register.php')); ?>">Register</a>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>&copy; <?= date('Y'); ?> Pastimes. All rights reserved.</p>
        <div class="social-links">
            <span>Instagram</span>
            <span>Facebook</span>
            <span>X</span>
        </div>
    </div>
</footer>
</body>
</html>
