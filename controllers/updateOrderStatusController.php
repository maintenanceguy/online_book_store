<?php

require_once '../config/database.php';
require_once '../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$order_id = (int) ($_POST['order_id'] ?? 0);
$status   = sanitize($_POST['status'] ?? '');

$allowedStatus = ['pending', 'confirmed', 'shipped', 'delivered'];

if (!in_array($status, $allowedStatus)) {
    redirect('../views/admin/view_orders.php');
}

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();

redirect('../views/admin/view_orders.php');
