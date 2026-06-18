<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/DBConn.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/Marketplace.php';

try {
    ensure_clothes_inventory_column();
    ensure_order_tracking_columns();
    ensure_message_related_order_column();
    ensure_message_receiver_admin_column();
    ensure_product_image_table();
    ensure_user_profile_image_column();
} catch (Throwable $exception) {
    // Keep bootstrap resilient; individual pages can still surface a clearer database error if needed.
}

define('APP_ROOT', dirname(__DIR__));

$documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : null;
$normalizedAppRoot = str_replace('\\', '/', APP_ROOT);
$normalizedDocumentRoot = $documentRoot ? str_replace('\\', '/', $documentRoot) : null;

if (!defined('APP_URL_BASE')) {
    if ($normalizedDocumentRoot && strpos($normalizedAppRoot, $normalizedDocumentRoot) === 0) {
        $basePath = str_replace('\\', '/', substr($normalizedAppRoot, strlen($normalizedDocumentRoot)));
        define('APP_URL_BASE', rtrim($basePath, '/'));
    } else {
        define('APP_URL_BASE', '');
    }
}
