<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

declare(strict_types=1);

require_once __DIR__ . '/DBConn.php';
require_once __DIR__ . '/includes/functions.php';

$messages = [];

try {
    $serverConnection = getDBConnection(false);
    $serverConnection->query('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $serverConnection->close();

    $connection = getDBConnection(true);
    // Reuse the exported DDL so the lecturer can recreate the same structure manually if needed.
    $schema = file_get_contents(__DIR__ . '/database/myClothingStore.sql');

    if ($schema === false) {
        throw new RuntimeException('Unable to read myClothingStore.sql');
    }

    if (!$connection->multi_query($schema)) {
        throw new RuntimeException('Schema execution failed: ' . $connection->error);
    }

    do {
        if ($result = $connection->store_result()) {
            $result->free();
        }
    } while ($connection->more_results() && $connection->next_result());

    $messages[] = 'Database schema created successfully.';

    $loaders = [
        'tblUser' => ['database/seeds/tblUser.txt', 'INSERT INTO tblUser (full_name, first_name, last_name, username, email, password_hash, phone_number, role, customer_status, seller_status, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'],
        'tblAdmin' => ['database/seeds/tblAdmin.txt', 'INSERT INTO tblAdmin (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)'],
        'tblClothes' => ['database/seeds/tblClothes.txt', 'INSERT INTO tblClothes (seller_id, title, brand, category, gender, size_label, condition_rating, sell_price, description, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'],
        'tblAddress' => ['database/seeds/tblAddress.txt', 'INSERT INTO tblAddress (user_id, address_label, street_address, suburb, city, province, postal_code, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'],
        'tblSellerApplication' => ['database/seeds/tblSellerApplication.txt', 'INSERT INTO tblSellerApplication (user_id, id_number, motivation, status) VALUES (?, ?, ?, ?)'],
        'tblCartItem' => ['database/seeds/tblCartItem.txt', 'INSERT INTO tblCartItem (user_id, clothes_id, quantity) VALUES (?, ?, ?)'],
        'tblOrder' => ['database/seeds/tblOrder.txt', 'INSERT INTO tblOrder (user_id, address_id, order_total, status) VALUES (?, ?, ?, ?)'],
        'tblOrderItem' => ['database/seeds/tblOrderItem.txt', 'INSERT INTO tblOrderItem (order_id, clothes_id, quantity, price_each) VALUES (?, ?, ?, ?)'],
        'tblMessage' => ['database/seeds/tblMessage.txt', 'INSERT INTO tblMessage (sender_user_id, sender_admin_id, receiver_user_id, title, message_body, is_broadcast, is_read) VALUES (?, ?, ?, ?, ?, ?, ?)'],
    ];

    foreach ($loaders as $tableName => [$filePath, $insertSql]) {
        $rows = read_seed_rows(__DIR__ . '/' . $filePath);
        $statement = $connection->prepare($insertSql);
        if (!$statement) {
            throw new RuntimeException("Prepare failed for $tableName: " . $connection->error);
        }

        foreach ($rows as $row) {
            // Each seed file uses a fixed column order that mirrors the destination table definition.
            switch ($tableName) {
                case 'tblUser':
                    [$fullName, $firstName, $lastName, $username, $email, $passwordHash, $phoneNumber, $role, $customerStatus, $sellerStatus, $isActive] = $row;
                    $active = (int) $isActive;
                    $statement->bind_param('ssssssssssi', $fullName, $firstName, $lastName, $username, $email, $passwordHash, $phoneNumber, $role, $customerStatus, $sellerStatus, $active);
                    break;
                case 'tblAdmin':
                    [$fullName, $username, $email, $passwordHash] = $row;
                    $username = $email ?: $username;
                    $statement->bind_param('ssss', $fullName, $username, $email, $passwordHash);
                    break;
                case 'tblClothes':
                    [$sellerId, $title, $brand, $category, $gender, $sizeLabel, $conditionRating, $sellPrice, $description, $imagePath, $status] = $row;
                    $sellerId = (int) $sellerId;
                    $conditionRating = (int) $conditionRating;
                    $sellPrice = (float) $sellPrice;
                    $statement->bind_param('isssssidsss', $sellerId, $title, $brand, $category, $gender, $sizeLabel, $conditionRating, $sellPrice, $description, $imagePath, $status);
                    break;
                case 'tblAddress':
                    [$userId, $addressLabel, $streetAddress, $suburb, $city, $province, $postalCode, $isDefault] = $row;
                    $userId = (int) $userId;
                    $isDefault = (int) $isDefault;
                    $statement->bind_param('issssssi', $userId, $addressLabel, $streetAddress, $suburb, $city, $province, $postalCode, $isDefault);
                    break;
                case 'tblSellerApplication':
                    [$userId, $idNumber, $motivation, $status] = $row;
                    $userId = (int) $userId;
                    $statement->bind_param('isss', $userId, $idNumber, $motivation, $status);
                    break;
                case 'tblCartItem':
                    [$userId, $clothesId, $quantity] = $row;
                    $userId = (int) $userId;
                    $clothesId = (int) $clothesId;
                    $quantity = (int) $quantity;
                    $statement->bind_param('iii', $userId, $clothesId, $quantity);
                    break;
                case 'tblOrder':
                    [$userId, $addressId, $orderTotal, $status] = $row;
                    $userId = (int) $userId;
                    $addressId = (int) $addressId;
                    $orderTotal = (float) $orderTotal;
                    $statement->bind_param('iids', $userId, $addressId, $orderTotal, $status);
                    break;
                case 'tblOrderItem':
                    [$orderId, $clothesId, $quantity, $priceEach] = $row;
                    $orderId = (int) $orderId;
                    $clothesId = (int) $clothesId;
                    $quantity = (int) $quantity;
                    $priceEach = (float) $priceEach;
                    $statement->bind_param('iiid', $orderId, $clothesId, $quantity, $priceEach);
                    break;
                case 'tblMessage':
                    [$senderUserId, $senderAdminId, $receiverUserId, $title, $messageBody, $isBroadcast, $isRead] = $row;
                    $senderUserId = $senderUserId === '' ? null : (int) $senderUserId;
                    $senderAdminId = $senderAdminId === '' ? null : (int) $senderAdminId;
                    $receiverUserId = $receiverUserId === '' ? null : (int) $receiverUserId;
                    $isBroadcast = (int) $isBroadcast;
                    $isRead = (int) $isRead;
                    $statement->bind_param('iiissii', $senderUserId, $senderAdminId, $receiverUserId, $title, $messageBody, $isBroadcast, $isRead);
                    break;
            }

            $statement->execute();
        }

        $statement->close();
        $messages[] = "$tableName seeded successfully.";
    }

    $migrationSql = file_get_contents(__DIR__ . '/database/pastimes_schema_updates.sql');
    if ($migrationSql === false) {
        throw new RuntimeException('Unable to read pastimes_schema_updates.sql');
    }

    if (!$connection->multi_query($migrationSql)) {
        throw new RuntimeException('Migration execution failed: ' . $connection->error);
    }

    do {
        if ($result = $connection->store_result()) {
            $result->free();
        }
    } while ($connection->more_results() && $connection->next_result());

    $messages[] = 'Schema updates applied successfully.';

    $imageResult = $connection->query('SELECT clothes_id, title, image_path FROM tblClothes ORDER BY clothes_id');
    $missingImages = [];
    $imageCount = 0;

    if ($imageResult) {
        while ($imageRow = $imageResult->fetch_assoc()) {
            $imageCount++;
            $relativePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $imageRow['image_path']);
            $absolutePath = __DIR__ . DIRECTORY_SEPARATOR . $relativePath;

            if (!is_file($absolutePath)) {
                $missingImages[] = '#' . $imageRow['clothes_id'] . ' ' . $imageRow['title'] . ' -> ' . $imageRow['image_path'];
            }
        }

        $imageResult->free();
    }

    if ($missingImages === []) {
        $messages[] = "$imageCount product image paths verified in tblClothes.";
    } else {
        $messages[] = 'Missing product image files: ' . implode('; ', $missingImages);
    }
} catch (Throwable $exception) {
    $messages[] = 'Error: ' . $exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>loadClothingStore.php</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container section">
    <h1>loadClothingStore.php</h1>
    <?php foreach ($messages as $message): ?>
        <div class="alert alert-info"><?= e($message); ?></div>
    <?php endforeach; ?>
    <p><a class="button" href="index.php">Open Pastimes</a></p>
</div>
</body>
</html>
