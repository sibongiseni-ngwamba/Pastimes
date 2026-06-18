<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

declare(strict_types=1);

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

function app_url(string $path = ''): string
{
    $suffix = ltrim($path, '/');
    $base = defined('APP_URL_BASE') ? APP_URL_BASE : '';

    return $suffix === '' ? ($base ?: '/') : ($base . '/' . $suffix);
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $path): void
{
    header('Location: ' . app_url($path));
    exit;
}

function set_flash(string $message, string $type = 'info'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function old(string $key, string $default = ''): string
{
    return $_POST[$key] ?? $default;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function db(): mysqli
{
    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    $connection = getDBConnection(true);

    return $connection;
}

function bind_params(mysqli_stmt $statement, string $types, array &$params): void
{
    $bindParams = [$types];
    foreach ($params as $key => &$value) {
        $bindParams[] = &$value;
    }
    unset($value);
    call_user_func_array([$statement, 'bind_param'], $bindParams);
}

function db_one(string $sql, string $types = '', array $params = []): ?array
{
    $statement = db()->prepare($sql);

    if (!$statement) {
        throw new RuntimeException('Prepare failed: ' . db()->error);
    }

    if ($types !== '' && $params !== []) {
        bind_params($statement, $types, $params);
    }

    $statement->execute();
    $result = $statement->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $statement->close();

    return $row ?: null;
}

function db_all(string $sql, string $types = '', array $params = []): array
{
    $statement = db()->prepare($sql);

    if (!$statement) {
        throw new RuntimeException('Prepare failed: ' . db()->error);
    }

    if ($types !== '' && $params !== []) {
        bind_params($statement, $types, $params);
    }

    $statement->execute();
    $result = $statement->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $statement->close();

    return $rows;
}

function execute_sql(string $sql, string $types = '', array $params = []): bool
{
    $statement = db()->prepare($sql);

    if (!$statement) {
        throw new RuntimeException('Prepare failed: ' . db()->error);
    }

    if ($types !== '' && $params !== []) {
        bind_params($statement, $types, $params);
    }

    $success = $statement->execute();
    $statement->close();

    return $success;
}

function verify_password_value(string $password, string $storedHash): bool
{
    if (password_verify($password, $storedHash)) {
        return true;
    }

    // Legacy seed data in the classroom dump still uses MD5 hashes, so we keep a compatibility fallback.
    return hash('md5', $password) === $storedHash;
}

function current_user(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return db_one('SELECT * FROM tblUser WHERE user_id = ?', 'i', [(int) $_SESSION['user_id']]);
}

function current_admin(): ?array
{
    if (!isset($_SESSION['admin_id'])) {
        return null;
    }

    return db_one('SELECT * FROM tblAdmin WHERE admin_id = ?', 'i', [(int) $_SESSION['admin_id']]);
}

function require_user_login(): array
{
    $user = current_user();
    if (!$user) {
        set_flash('Please log in to continue.', 'error');
        redirect_to('login.php');
    }

    return $user;
}

function require_admin_login(): array
{
    $admin = current_admin();
    if (!$admin) {
        set_flash('Administrator access is required. Please sign in as an administrator.', 'error');
        redirect_to('login.php?role=admin');
    }

    return $admin;
}

function require_seller_login(): array
{
    $user = require_user_login();
    if (($user['seller_status'] ?? 'none') !== 'approved') {
        set_flash('Only approved sellers can access that page.', 'error');
        redirect_to('request-seller.php');
    }

    return $user;
}

function read_seed_rows(string $filePath): array
{
    $rows = [];
    if (!is_file($filePath)) {
        return $rows;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $rows[] = array_map('trim', explode('|', $trimmed));
    }

    return $rows;
}

function get_cart(): array
{
    return Cart::getSessionCart();
}

function save_cart(array $cart): void
{
    Cart::replaceSessionCart($cart);
}

function cart_count(): int
{
    return Cart::getCartCount();
}

function cart_details(): array
{
    return Cart::getCartDetails();
}

function status_badge(string $status): string
{
    $safe = strtolower($status);
    return '<span class="badge badge-' . e($safe) . '">' . e(ucfirst($status)) . '</span>';
}

function format_price(float $value): string
{
    return 'R' . number_format($value, 2, '.', ',');
}

function rating_stars(int $rating): string
{
    $stars = '';

    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';
    }

    return $stars;
}

function product_image_path(array $product): string
{
    $imagePath = str_replace('\\', '/', (string) ($product['image_path'] ?? ''));

    if (str_starts_with($imagePath, 'assets/images/')) {
        return $imagePath;
    }

    $title = strtolower((string) ($product['title'] ?? ''));
    $category = strtolower((string) ($product['category'] ?? ''));

    if (str_contains($title, 'dress') || $category === 'dresses') {
        return 'assets/images/products/floral-dress.jpg';
    }

    if (str_contains($title, 'denim') || str_contains($title, 'jacket')) {
        return 'assets/images/products/denim-jacket.jpg';
    }

    if (str_contains($title, 'coat') || $category === 'outerwear') {
        return 'assets/images/products/wool-coat.jpg';
    }

    if (str_contains($title, 'shoe') || str_contains($title, 'boot') || $category === 'shoes') {
        return 'assets/images/products/running-shoes.jpg';
    }

    if (str_contains($title, 'bag') || $category === 'accessories') {
        return 'assets/images/products/leather-bag.jpg';
    }

    if (str_contains($title, 'blouse') || str_contains($title, 'shirt') || $category === 'tops') {
        return 'assets/images/products/silk-blouse.jpg';
    }

    return 'assets/images/products/fallback-product.jpg';
}

function profile_upload_directory(): string
{
    return dirname(__DIR__) . '/assets/images/uploads';
}

function user_avatar_path(array $user): string
{
    $imagePath = str_replace('\\', '/', trim((string) ($user['profile_image_path'] ?? '')));

    if ($imagePath !== '' && str_starts_with($imagePath, 'assets/images/')) {
        return $imagePath;
    }

    return 'assets/images/uploads/default-avatar.svg';
}

function ensure_user_profile_image_column(): bool
{
    $column = db_one("SHOW COLUMNS FROM tblUser LIKE 'profile_image_path'");
    if ($column) {
        return true;
    }

    return execute_sql('ALTER TABLE tblUser ADD COLUMN profile_image_path VARCHAR(255) DEFAULT NULL AFTER phone_number');
}

function ensure_clothes_inventory_column(): bool
{
    $column = db_one("SHOW COLUMNS FROM tblClothes LIKE 'inventory_quantity'");
    if ($column) {
        return true;
    }

    return execute_sql('ALTER TABLE tblClothes ADD COLUMN inventory_quantity INT NOT NULL DEFAULT 1 AFTER image_path');
}

function ensure_order_tracking_columns(): bool
{
    $orderReferenceColumn = db_one("SHOW COLUMNS FROM tblOrder LIKE 'order_reference'");
    $sessionReferenceColumn = db_one("SHOW COLUMNS FROM tblOrder LIKE 'session_reference'");

    if ($orderReferenceColumn && $sessionReferenceColumn) {
        return true;
    }

    $statements = [];
    if (!$orderReferenceColumn) {
        $statements[] = 'ADD COLUMN order_reference VARCHAR(20) DEFAULT NULL AFTER order_total';
    }

    if (!$sessionReferenceColumn) {
        $statements[] = 'ADD COLUMN session_reference VARCHAR(20) DEFAULT NULL AFTER order_reference';
    }

    return execute_sql('ALTER TABLE tblOrder ' . implode(', ', $statements));
}

function ensure_message_related_order_column(): bool
{
    $column = db_one("SHOW COLUMNS FROM tblMessage LIKE 'related_order_id'");
    if ($column) {
        return true;
    }

    return execute_sql('ALTER TABLE tblMessage ADD COLUMN related_order_id INT DEFAULT NULL AFTER receiver_user_id');
}

function ensure_message_receiver_admin_column(): bool
{
    $column = db_one("SHOW COLUMNS FROM tblMessage LIKE 'receiver_admin_id'");
    if ($column) {
        return true;
    }

    return execute_sql('ALTER TABLE tblMessage ADD COLUMN receiver_admin_id INT DEFAULT NULL AFTER receiver_user_id');
}

function ensure_product_image_table(): bool
{
    $table = db_one("SHOW TABLES LIKE 'tblProductImage'");
    if (!$table) {
        execute_sql(
            'CREATE TABLE tblProductImage (
                image_id INT NOT NULL AUTO_INCREMENT,
                clothes_id INT NOT NULL,
                image_path VARCHAR(255) NOT NULL,
                sort_order INT NOT NULL DEFAULT 1,
                alt_text VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (image_id),
                UNIQUE KEY ux_product_image_sort (clothes_id, sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }

    execute_sql(
        'INSERT IGNORE INTO tblProductImage (clothes_id, image_path, sort_order, alt_text)
         SELECT clothes_id, image_path, 1, title
         FROM tblClothes'
    );

    return true;
}

function delete_profile_image_file(?string $imagePath): void
{
    $relativePath = str_replace('\\', '/', trim((string) $imagePath));

    if ($relativePath === '' || $relativePath === 'assets/images/uploads/default-avatar.svg') {
        return;
    }

    if (!str_starts_with($relativePath, 'assets/images/uploads/')) {
        return;
    }

    $absolutePath = dirname(__DIR__) . '/' . $relativePath;
    if (is_file($absolutePath)) {
        @unlink($absolutePath);
    }
}

function store_profile_image_upload(array $file): string
{
    $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($uploadError === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    if ($uploadError !== UPLOAD_ERR_OK) {
        throw new RuntimeException('We could not upload that profile picture. Please try again.');
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        throw new RuntimeException('The selected profile picture is not a valid upload.');
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0) {
        throw new RuntimeException('The selected profile picture is empty.');
    }

    if ($size > 2 * 1024 * 1024) {
        throw new RuntimeException('Profile pictures must be 2 MB or smaller.');
    }

    $mimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpName) ?: '';
    } elseif (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($tmpName) ?: '';
    } else {
        $mimeType = (string) ($file['type'] ?? '');
    }

    if (!isset($mimeTypes[$mimeType])) {
        throw new RuntimeException('Only JPG, PNG, GIF, or WEBP profile pictures are supported.');
    }

    $uploadDirectory = profile_upload_directory();
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
        throw new RuntimeException('The profile picture upload folder could not be created.');
    }

    $filename = sprintf('profile-%s.%s', bin2hex(random_bytes(8)), $mimeTypes[$mimeType]);
    $targetPath = $uploadDirectory . '/' . $filename;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        throw new RuntimeException('We could not save your profile picture.');
    }

    return 'assets/images/uploads/' . $filename;
}

function condition_label(int $rating): string
{
    switch ($rating) {
        case 1:
            return 'Fair';
        case 2:
            return 'Good';
        case 3:
            return 'Very Good';
        case 4:
            return 'Excellent';
        case 5:
            return 'Like New';
        default:
            return 'Unknown';
    }
}

function generate_order_reference_number(int $orderId): string
{
    return sprintf('ORD%s%05d', date('Y'), $orderId);
}

function generate_session_reference_code(): string
{
    return strtoupper(substr(bin2hex(random_bytes(6)), 0, 9));
}
