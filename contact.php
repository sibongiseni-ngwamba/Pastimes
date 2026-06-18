<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Pastimes | Contact';
$submitted = false;

if (is_post()) {
    $submitted = true;
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="section section-tight">
    <div class="container contact-layout">
        <div class="contact-copy">
            <h1>Contact Pastimes</h1>
            <p>Need help with an order, a seller application, or a listing approval? Our admin team is ready to assist.</p>
            <div class="contact-notes">
                <p><strong>Email:</strong> support@pastimes.co.za</p>
                <p><strong>Phone:</strong> +27 11 555 0101</p>
                <p><strong>Office Hours:</strong> Monday to Friday, 08:00 to 17:00</p>
            </div>
        </div>
        <div class="dashboard-card">
            <h2>Send Us A Message</h2>
            <?php if ($submitted): ?>
                <div class="inline-alert inline-alert-success">Thanks. Your prototype enquiry has been captured successfully.</div>
            <?php endif; ?>
            <form method="post">
                <label>Name
                    <input type="text" name="name" required>
                </label>
                <label>Email
                    <input type="email" name="email" required>
                </label>
                <label>Topic
                    <select name="topic">
                        <option>General Support</option>
                        <option>Seller Verification</option>
                        <option>Pending Listing Approval</option>
                        <option>Order Tracking</option>
                    </select>
                </label>
                <label>Message
                    <textarea name="message" rows="6" required></textarea>
                </label>
                <button class="button button-dark button-block" type="submit">Send Message</button>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
