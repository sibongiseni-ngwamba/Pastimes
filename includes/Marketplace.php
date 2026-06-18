<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

declare(strict_types=1);

final class Auth
{
    public static function registerUser(array $data): int
    {
        $fullName = trim((string) ($data['full_name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $username = trim((string) ($data['username'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $phoneNumber = trim((string) ($data['phone_number'] ?? ''));

        if ($fullName === '' || $email === '' || $username === '' || $password === '') {
            throw new RuntimeException('All required fields must be completed.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Enter a valid email address.');
        }

        if (strlen($password) < 8) {
            throw new RuntimeException('Password must contain at least 8 characters.');
        }

        $duplicate = db_one('SELECT user_id FROM tblUser WHERE username = ? OR email = ?', 'ss', [$username, $email]);
        if ($duplicate) {
            throw new RuntimeException('A user with that username or email already exists.');
        }

        $nameParts = preg_split('/\s+/', $fullName, 2);
        $firstName = $nameParts[0] ?? $fullName;
        $lastName = $nameParts[1] ?? ($nameParts[0] ?? '');
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        execute_sql(
            'INSERT INTO tblUser (full_name, first_name, last_name, username, email, password_hash, phone_number, role, customer_status, seller_status, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, "customer", "pending", "none", 1)',
            'sssssss',
            [$fullName, $firstName, $lastName, $username, $email, $passwordHash, $phoneNumber]
        );

        return (int) db()->insert_id;
    }

    public static function loginAdmin(string $identity, string $password): array
    {
        $admin = db_one('SELECT * FROM tblAdmin WHERE email = ? OR username = ? LIMIT 1', 'ss', [$identity, $identity]);

        if (!$admin) {
            throw new RuntimeException('Invalid administrator credentials.');
        }

        if (!verify_password_value($password, (string) $admin['password_hash'])) {
            throw new RuntimeException('Invalid administrator credentials.');
        }

        $_SESSION['admin_id'] = (int) $admin['admin_id'];
        $_SESSION['username'] = (string) $admin['username'];
        $_SESSION['role'] = 'admin';
        $_SESSION['is_seller'] = 0;
        $shopperUser = self::ensureAdminShopperUser($admin);
        $_SESSION['user_id'] = (int) $shopperUser['user_id'];

        return $admin;
    }

    public static function loginUser(string $identity, string $password): array
    {
        $user = db_one('SELECT * FROM tblUser WHERE username = ? OR email = ? LIMIT 1', 'ss', [$identity, $identity]);

        if (!$user) {
            throw new RuntimeException('User does not exist. Please register first.');
        }

        if (($user['customer_status'] ?? 'pending') !== 'verified') {
            throw new RuntimeException('This account is still pending customer verification by the admin.');
        }

        if (!(int) ($user['is_active'] ?? 0)) {
            throw new RuntimeException('This account has been deactivated by an administrator.');
        }

        if (!verify_password_value($password, (string) $user['password_hash'])) {
            throw new RuntimeException('The password is incorrect. Please try again.');
        }

        $_SESSION['user_id'] = (int) $user['user_id'];
        $_SESSION['username'] = (string) $user['username'];
        $_SESSION['role'] = (string) ($user['role'] ?? 'customer');
        $_SESSION['is_seller'] = (int) (($user['seller_status'] ?? 'none') === 'approved' ? 1 : 0);
        unset($_SESSION['admin_id']);
        Cart::mergeGuestCartIntoActiveUser();

        return $user;
    }

    public static function logoutUser(): void
    {
        unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], $_SESSION['is_seller']);
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0;
    }

    private static function ensureAdminShopperUser(array $admin): array
    {
        $adminEmail = (string) ($admin['email'] ?? '');
        $shopper = $adminEmail !== '' ? db_one('SELECT * FROM tblUser WHERE email = ? LIMIT 1', 's', [$adminEmail]) : null;

        if ($shopper) {
            return $shopper;
        }

        $fullName = trim((string) ($admin['full_name'] ?? 'Administrator'));
        $nameParts = preg_split('/\s+/', $fullName, 2);
        $firstName = $nameParts[0] ?? 'Administrator';
        $lastName = $nameParts[1] ?? ($nameParts[0] ?? '');
        $username = trim((string) ($admin['username'] ?? 'admin'));
        if ($username === '') {
            $username = 'admin_' . (int) ($admin['admin_id'] ?? 0);
        }

        $passwordHash = (string) ($admin['password_hash'] ?? '');
        execute_sql(
            'INSERT INTO tblUser (full_name, first_name, last_name, username, email, password_hash, phone_number, role, customer_status, seller_status, is_active)
             VALUES (?, ?, ?, ?, ?, ?, NULL, "admin", "verified", "none", 1)',
            'ssssss',
            [$fullName, $firstName, $lastName, $username, $adminEmail, $passwordHash]
        );

        return db_one('SELECT * FROM tblUser WHERE email = ? LIMIT 1', 's', [$adminEmail]) ?? [];
    }
}

final class Cart
{
    public static function Login(): ?array
    {
        return current_user();
    }

    public static function mergeGuestCartIntoActiveUser(): void
    {
        $user = current_user();
        if (!$user) {
            return;
        }

        $guestCart = self::getSessionCartForScope('guest', false);
        if ($guestCart === []) {
            return;
        }

        $userCart = self::getSessionCart();
        foreach ($guestCart as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;

            if ($productId > 0 && $quantity > 0) {
                $userCart[$productId] = ($userCart[$productId] ?? 0) + $quantity;
            }
        }

        self::saveSessionCart($userCart);
        self::clearSessionCart('guest');
        self::syncDatabaseCart();
    }

    public static function replaceSessionCart(array $cart): void
    {
        self::saveSessionCart($cart);
        self::syncDatabaseCart();
    }

    public static function ProcessInput(array $input = []): bool
    {
        $action = (string) ($input['action'] ?? '');
        $productId = (int) ($input['clothes_id'] ?? $input['product_id'] ?? 0);

        if ($action === 'add' || ($action === '' && $productId > 0 && isset($input['clothes_id']))) {
            self::AddItem($productId, (int) ($input['quantity'] ?? 1));
            return true;
        }

        if ($action === 'remove') {
            self::RemoveItem($productId);
            return true;
        }

        if ($action === 'update') {
            self::UpdateQuantity($productId, (int) ($input['quantity'] ?? 1));
            return true;
        }

        if ($action === 'empty') {
            self::EmptyCart();
            return true;
        }

        return false;
    }

    public static function AddItem(int $productId, int $quantity = 1): void
    {
        if ($productId <= 0) {
            return;
        }

        $quantity = max(1, $quantity);
        $cart = self::getSessionCart();
        $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
        self::saveSessionCart($cart);
        self::syncDatabaseCart();
    }

    public static function RemoveItem(int $productId): void
    {
        $cart = self::getSessionCart();
        unset($cart[$productId]);
        self::saveSessionCart($cart);
        self::syncDatabaseCart();
    }

    public static function UpdateQuantity(int $productId, int $quantity): void
    {
        $cart = self::getSessionCart();

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        self::saveSessionCart($cart);
        self::syncDatabaseCart();
    }

    public static function EmptyCart(): void
    {
        self::saveSessionCart([]);
        self::clearDatabaseCart();
    }

    public static function Checkout(int $addressId): array
    {
        $user = current_user();
        if (!$user) {
            throw new RuntimeException('Please log in before checking out.');
        }

        $cart = cart_details();
        if ($cart['items'] === []) {
            throw new RuntimeException('Add an item to the cart before checking out.');
        }

        $address = db_one('SELECT * FROM tblAddress WHERE address_id = ? AND user_id = ?', 'ii', [$addressId, (int) $user['user_id']]);
        if (!$address) {
            throw new RuntimeException('Choose a valid saved delivery address.');
        }

        db()->begin_transaction();
        try {
            execute_sql(
                'INSERT INTO tblOrder (user_id, address_id, order_total, order_reference, session_reference, status)
                 VALUES (?, ?, ?, "", "", "pending")',
                'iid',
                [(int) $user['user_id'], $addressId, (float) $cart['total']]
            );

            $orderId = (int) db()->insert_id;
            $orderReference = self::generateOrderReference($orderId);
            $sessionReference = self::generateSessionReference();

            execute_sql(
                'UPDATE tblOrder SET order_reference = ?, session_reference = ? WHERE order_id = ?',
                'ssi',
                [$orderReference, $sessionReference, $orderId]
            );

            foreach ($cart['items'] as $item) {
                $product = $item['product'];
                $quantity = (int) $item['quantity'];
                $priceEach = (float) $product['sell_price'];

                execute_sql(
                    'INSERT INTO tblOrderItem (order_id, clothes_id, quantity, price_each) VALUES (?, ?, ?, ?)',
                    'iiid',
                    [$orderId, (int) $product['clothes_id'], $quantity, $priceEach]
                );

                $available = (int) ($product['inventory_quantity'] ?? 1);
                $remaining = max(0, $available - $quantity);

                execute_sql(
                    'UPDATE tblClothes SET inventory_quantity = ?, status = ? WHERE clothes_id = ?',
                    'isi',
                    [$remaining, $remaining > 0 ? 'approved' : 'sold', (int) $product['clothes_id']]
                );
            }

            db()->commit();
            self::EmptyCart();

            return [
                'order_id' => $orderId,
                'order_reference' => $orderReference,
                'session_reference' => $sessionReference,
                'order_total' => (float) $cart['total'],
                'address' => $address,
            ];
        } catch (Throwable $exception) {
            db()->rollback();
            throw $exception;
        }
    }

    public static function getSessionCart(): array
    {
        self::ensureCartStore();

        $scopeKey = self::cartScopeKey();
        if (!isset($_SESSION['cart_buckets'][$scopeKey]) || !is_array($_SESSION['cart_buckets'][$scopeKey])) {
            $_SESSION['cart_buckets'][$scopeKey] = [];
        }

        if ($_SESSION['cart_buckets'][$scopeKey] === []) {
            self::loadDatabaseCartIfNeeded();
        }

        return $_SESSION['cart_buckets'][$scopeKey];
    }

    public static function getCartCount(): int
    {
        return array_sum(self::getSessionCart());
    }

    public static function getCartDetails(): array
    {
        self::loadDatabaseCartIfNeeded();

        $cart = self::getSessionCart();
        if ($cart === []) {
            return ['items' => [], 'total' => 0.0];
        }

        $ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $rows = db_all(
            "SELECT clothes_id, seller_id, title, brand, category, gender, size_label, condition_rating, sell_price, description, image_path, status, inventory_quantity
             FROM tblClothes WHERE clothes_id IN ($placeholders)",
            $types,
            $ids
        );

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[(int) $row['clothes_id']] = $row;
        }

        $items = [];
        $total = 0.0;
        foreach ($cart as $clothesId => $quantity) {
            if (!isset($indexed[(int) $clothesId])) {
                continue;
            }

            $product = $indexed[(int) $clothesId];
            $subtotal = (float) $product['sell_price'] * (int) $quantity;
            $total += $subtotal;
            $items[] = [
                'product' => $product,
                'quantity' => (int) $quantity,
                'subtotal' => $subtotal,
            ];
        }

        return ['items' => $items, 'total' => $total];
    }

    private static function loadDatabaseCartIfNeeded(): void
    {
        $user = current_user();
        if (!$user) {
            return;
        }

        $scopeKey = self::cartScopeKey((int) $user['user_id']);
        if (isset($_SESSION['cart_buckets'][$scopeKey]) && $_SESSION['cart_buckets'][$scopeKey] !== []) {
            return;
        }

        try {
            $rows = db_all('SELECT clothes_id, quantity FROM tblCartItem WHERE user_id = ? ORDER BY cart_id ASC', 'i', [(int) $user['user_id']]);
            if ($rows === []) {
                return;
            }

            $cart = [];
            foreach ($rows as $row) {
                $cart[(int) $row['clothes_id']] = (int) $row['quantity'];
            }

            self::saveSessionCart($cart, $scopeKey);
        } catch (Throwable $exception) {
            // If the cart table is missing or the query fails, fall back to the session cart only.
        }
    }

    private static function saveSessionCart(array $cart, ?string $scopeKey = null): void
    {
        self::ensureCartStore();
        $scopeKey = $scopeKey ?? self::cartScopeKey();
        $_SESSION['cart_buckets'][$scopeKey] = [];

        foreach ($cart as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;
            if ($productId > 0 && $quantity > 0) {
                $_SESSION['cart_buckets'][$scopeKey][$productId] = $quantity;
            }
        }
    }

    private static function clearSessionCart(?string $scopeKey = null): void
    {
        self::ensureCartStore();
        $scopeKey = $scopeKey ?? self::cartScopeKey();
        $_SESSION['cart_buckets'][$scopeKey] = [];
    }

    private static function getSessionCartForScope(string $scope, bool $loadDatabase = true): array
    {
        self::ensureCartStore();

        if (!isset($_SESSION['cart_buckets'][$scope]) || !is_array($_SESSION['cart_buckets'][$scope])) {
            $_SESSION['cart_buckets'][$scope] = [];
        }

        if ($loadDatabase && $scope !== 'guest' && $_SESSION['cart_buckets'][$scope] === []) {
            self::loadDatabaseCartIfNeeded();
        }

        return $_SESSION['cart_buckets'][$scope];
    }

    private static function cartScopeKey(?int $userId = null): string
    {
        if ($userId === null) {
            $currentUser = current_user();
            $userId = (int) ($currentUser['user_id'] ?? 0);
        }

        return $userId > 0 ? 'user_' . $userId : 'guest';
    }

    private static function ensureCartStore(): void
    {
        if (!isset($_SESSION['cart_buckets']) || !is_array($_SESSION['cart_buckets'])) {
            $_SESSION['cart_buckets'] = [];
        }

        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            $legacyCart = $_SESSION['cart'];
            if ($legacyCart !== []) {
                $legacyScope = self::cartScopeKey();
                if (!isset($_SESSION['cart_buckets'][$legacyScope]) || $_SESSION['cart_buckets'][$legacyScope] === []) {
                    self::saveSessionCart($legacyCart, $legacyScope);
                }
            }

            unset($_SESSION['cart']);
        }
    }

    private static function syncDatabaseCart(): void
    {
        $user = current_user();
        if (!$user) {
            return;
        }

        self::clearDatabaseCart();
        $cart = self::getSessionCart();
        if ($cart === []) {
            return;
        }

        $statement = db()->prepare('INSERT INTO tblCartItem (user_id, clothes_id, quantity) VALUES (?, ?, ?)');
        if (!$statement) {
            return;
        }

        foreach ($cart as $clothesId => $quantity) {
            $userId = (int) $user['user_id'];
            $clothesId = (int) $clothesId;
            $quantity = (int) $quantity;
            $statement->bind_param('iii', $userId, $clothesId, $quantity);
            $statement->execute();
        }

        $statement->close();
    }

    private static function clearDatabaseCart(): void
    {
        $user = current_user();
        if (!$user) {
            return;
        }

        execute_sql('DELETE FROM tblCartItem WHERE user_id = ?', 'i', [(int) $user['user_id']]);
    }

    private static function generateOrderReference(int $orderId): string
    {
        return sprintf('ORD%s%05d', date('Y'), $orderId);
    }

    private static function generateSessionReference(): string
    {
        $randomBytes = random_bytes(6);
        return strtoupper(substr(bin2hex($randomBytes), 0, 9));
    }

}
