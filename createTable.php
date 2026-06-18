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

function dropUserTable(mysqli $connection, array &$messages): void
{
    $connection->query('DROP TABLE IF EXISTS tblUser');
    $messages[] = 'Step 1 complete: tblUser deleted.';
}

function createUserTable(mysqli $connection, array &$messages): void
{
    $createTableSql = <<<SQL
    CREATE TABLE tblUser (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(120) NOT NULL,
        first_name VARCHAR(60) NOT NULL,
        last_name VARCHAR(60) NOT NULL,
        username VARCHAR(60) NOT NULL UNIQUE,
        email VARCHAR(120) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) DEFAULT NULL,
        profile_image_path VARCHAR(255) DEFAULT NULL,
        role ENUM('customer', 'seller', 'admin') NOT NULL DEFAULT 'customer',
        customer_status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
        seller_status ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
    SQL;

    $connection->query($createTableSql);
    $messages[] = 'Step 2 complete: tblUser created.';
}

function loadUserData(mysqli $connection, string $seedPath, array &$messages): void
{
    $rows = read_seed_rows($seedPath);
    $statement = $connection->prepare(
        'INSERT INTO tblUser (full_name, first_name, last_name, username, email, password_hash, phone_number, role, customer_status, seller_status, is_active)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    if (!$statement) {
        throw new RuntimeException('Prepare failed: ' . $connection->error);
    }

    foreach ($rows as $row) {
        // tblUser.txt stays simple and human-editable, so each line is parsed into the expected columns here.
        [$fullName, $firstName, $lastName, $username, $email, $passwordHash, $phoneNumber, $role, $customerStatus, $sellerStatus, $isActive] = $row;
        $active = (int) $isActive;
        $statement->bind_param(
            'ssssssssssi',
            $fullName,
            $firstName,
            $lastName,
            $username,
            $email,
            $passwordHash,
            $phoneNumber,
            $role,
            $customerStatus,
            $sellerStatus,
            $active
        );
        $statement->execute();
    }

    $statement->close();
    $messages[] = 'Step 3 complete: tblUser data loaded from tblUser.txt.';
}

try {
    $serverConnection = getDBConnection(false);
    $serverConnection->query('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $serverConnection->close();

    $connection = getDBConnection(true);
    dropUserTable($connection, $messages);
    createUserTable($connection, $messages);
    loadUserData($connection, __DIR__ . '/database/seeds/tblUser.txt', $messages);
    $connection->close();
} catch (Throwable $exception) {
    $messages[] = 'Error: ' . $exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>createTable.php</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container section">
    <h1>createTable.php</h1>
    <p>This page performs the required delete, create, and load steps for tblUser.</p>
    <?php foreach ($messages as $message): ?>
        <div class="alert alert-info"><?= e($message); ?></div>
    <?php endforeach; ?>
    <script>
        <?php foreach ($messages as $message): ?>
        console.log(<?= json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>);
        <?php endforeach; ?>
    </script>
    <p><a class="button" href="index.php">Open Pastimes</a></p>
</div>
</body>
</html>
