<?php

require_once '../config/database.php';
require_once '../config/helpers.php';

if (!isLoggedIn()) {
    redirect('../views/auth/login.php');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $address        = sanitize($_POST['address']);
    $payment_method = sanitize($_POST['payment_method']);
    $transaction_id = sanitize($_POST['transaction_id'] ?? '');

    $errors = [];

    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    $allowedPayments = ['Credit Card', 'bKash', 'Nagad', 'Bank Transfer', 'Cash on Delivery'];
    if (!in_array($payment_method, $allowedPayments)) {
        $errors[] = "Invalid payment method.";
    }

    $cartStmt = $conn->prepare("
        SELECT cart.*, books.price, books.stock
        FROM cart
        JOIN books ON cart.book_id = books.id
        WHERE cart.user_id = ?
    ");
    $cartStmt->bind_param("i", $user_id);
    $cartStmt->execute();
    $cartItems = $cartStmt->get_result();

    if ($cartItems->num_rows == 0) {
        $errors[] = "Cart is empty.";
    }

    $items = [];
    $total = 0;

    while ($item = $cartItems->fetch_assoc()) {
        if ($item['quantity'] > $item['stock']) {
            $errors[] = "One or more items exceed available stock.";
            break;
        }
        $items[] = $item;
        $total  += $item['price'] * $item['quantity'];
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        redirect('../views/customer/checkout.php');
    }

    $conn->begin_transaction();

    try {

        $orderStmt = $conn->prepare("
            INSERT INTO orders (user_id, total_amount, status, payment_method)
            VALUES (?, ?, 'pending', ?)
        ");
        $orderStmt->bind_param("ids", $user_id, $total, $payment_method);
        $orderStmt->execute();
        $order_id = $conn->insert_id;

        foreach ($items as $item) {

            $itemStmt = $conn->prepare("
                INSERT INTO order_items (order_id, book_id, quantity, unit_price)
                VALUES (?, ?, ?, ?)
            ");
            $itemStmt->bind_param("iiid", $order_id, $item['book_id'], $item['quantity'], $item['price']);
            $itemStmt->execute();

            $stockStmt = $conn->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
            $stockStmt->bind_param("ii", $item['quantity'], $item['book_id']);
            $stockStmt->execute();
        }

        $paymentStmt = $conn->prepare("
            INSERT INTO payments (order_id, amount, payment_method, transaction_id)
            VALUES (?, ?, ?, ?)
        ");
        $paymentStmt->bind_param("idss", $order_id, $total, $payment_method, $transaction_id);
        $paymentStmt->execute();

        $clearCart = $conn->prepare("DELETE FROM cart WHERE user_id=?");
        $clearCart->bind_param("i", $user_id);
        $clearCart->execute();

        $conn->commit();

        $_SESSION['success'] = "Order placed successfully. Order ID: #" . $order_id;
        redirect('../views/customer/order_history.php');

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['errors'] = ["Order failed. Please try again."];
        redirect('../views/customer/checkout.php');
    }
}
