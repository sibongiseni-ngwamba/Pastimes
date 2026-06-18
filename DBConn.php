<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

declare(strict_types=1);

if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('PASTIMES_DB_HOST') ?: '127.0.0.1');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('PASTIMES_DB_NAME') ?: 'ClothingStore');
}

if (!defined('DB_USER')) {
    define('DB_USER', getenv('PASTIMES_DB_USER') ?: 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('PASTIMES_DB_PASS') ?: '');
}

if (!defined('DB_PORT')) {
    define('DB_PORT', (int) (getenv('PASTIMES_DB_PORT') ?: 3306));
}

function getDBConnection(bool $withDatabase = true): mysqli
{
    $database = $withDatabase ? DB_NAME : '';
    $connection = @new mysqli(DB_HOST, DB_USER, DB_PASS, $database, DB_PORT);

    if ($connection->connect_error) {
        throw new RuntimeException('Database connection failed: ' . $connection->connect_error);
    }

    $connection->set_charset('utf8mb4');

    return $connection;
}
