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
$pageTitle = 'Pastimes | Addresses';
$errors = [];

if (isset($_GET['delete'])) {
    execute_sql('DELETE FROM tblAddress WHERE address_id = ? AND user_id = ?', 'ii', [(int) $_GET['delete'], (int) $user['user_id']]);
    set_flash('Address deleted.', 'success');
    redirect_to('addresses.php');
}

if (isset($_GET['default'])) {
    execute_sql('UPDATE tblAddress SET is_default = 0 WHERE user_id = ?', 'i', [(int) $user['user_id']]);
    execute_sql('UPDATE tblAddress SET is_default = 1 WHERE address_id = ? AND user_id = ?', 'ii', [(int) $_GET['default'], (int) $user['user_id']]);
    set_flash('Default address updated.', 'success');
    redirect_to('addresses.php');
}

if (is_post()) {
    $label = trim($_POST['address_label'] ?? '');
    $street = trim($_POST['street_address'] ?? '');
    $suburb = trim($_POST['suburb'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $postalCode = trim($_POST['postal_code'] ?? '');
    $isDefault = isset($_POST['is_default']) ? 1 : 0;

    if ($label === '' || $street === '' || $suburb === '' || $city === '' || $province === '' || $postalCode === '') {
        $errors[] = 'Please complete every address field.';
    } else {
        if ($isDefault) {
            execute_sql('UPDATE tblAddress SET is_default = 0 WHERE user_id = ?', 'i', [(int) $user['user_id']]);
        }

        execute_sql(
            'INSERT INTO tblAddress (user_id, address_label, street_address, suburb, city, province, postal_code, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            'issssssi',
            [(int) $user['user_id'], $label, $street, $suburb, $city, $province, $postalCode, $isDefault]
        );
        set_flash('Address saved.', 'success');
        redirect_to('addresses.php');
    }
}

$addresses = db_all('SELECT * FROM tblAddress WHERE user_id = ? ORDER BY is_default DESC, created_at DESC', 'i', [(int) $user['user_id']]);
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container split-layout">
        <div class="content-main">
            <h1>Saved Addresses</h1>
            <?php if ($addresses): ?>
                <table>
                    <thead><tr><th>Label</th><th>Address</th><th>Default</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($addresses as $address): ?>
                        <tr>
                            <td><?= e($address['address_label']); ?></td>
                            <td><?= e($address['street_address'] . ', ' . $address['suburb'] . ', ' . $address['city'] . ', ' . $address['province'] . ' ' . $address['postal_code']); ?></td>
                            <td><?= (int) $address['is_default'] === 1 ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="<?= e(app_url('addresses.php?default=' . $address['address_id'])); ?>">Set Default</a> |
                                <a href="<?= e(app_url('addresses.php?delete=' . $address['address_id'])); ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">No addresses saved yet.</div>
            <?php endif; ?>
        </div>
        <aside class="content-side">
            <div class="form-card">
                <h3>Add Address</h3>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-error"><?= e($error); ?></div>
                <?php endforeach; ?>
                <form method="post">
                    <label>Label</label>
                    <select name="address_label">
                        <option value="Home">Home</option>
                        <option value="Work">Work</option>
                        <option value="Other">Other</option>
                    </select>
                    <label>Street Address</label>
                    <input name="street_address" required>
                    <label>Suburb</label>
                    <input name="suburb" required>
                    <label>City</label>
                    <input name="city" required>
                    <label>Province</label>
                    <input name="province" required>
                    <label>Postal Code</label>
                    <input name="postal_code" required>
                    <label><input type="checkbox" name="is_default" value="1" style="width:auto;"> Set as default</label>
                    <input type="submit" value="Save Address">
                </form>
            </div>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
