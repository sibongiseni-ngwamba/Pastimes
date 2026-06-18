<?php
/**
 * Student Number: ST10454956
 * Name and Surname: Sibongiseni Collel Ngwamba
 * Student Number Group Member: ST10449382
 * Name and Surname Group Member: Thokozani Masondo
 * Declaration: This code is my own original work, except where reference is made to the work of others.
 */
require_once __DIR__ . '/includes/bootstrap.php';

$scope = $_GET['scope'] ?? 'user';

if ($scope === 'admin') {
    Auth::logoutUser();
    unset($_SESSION['admin_id'], $_SESSION['username'], $_SESSION['role'], $_SESSION['is_seller']);
    set_flash('Administrator logged out successfully.', 'success');
} else {
    Auth::logoutUser();
    set_flash('User logged out successfully.', 'success');
}

redirect_to('index.php');
