<?php
require_once '../config/database.php';
require_once '../config/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_POST['book_id']) ? (int) $_POST['book_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

if ($book_id <= 0 || $quantity <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid book or quantity."
    ]);
    exit();
}

$bookStmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$bookStmt->bind_param("i", $book_id);
$bookStmt->execute();
$book = $bookStmt->get_result()->fetch_assoc();

if (!$book) {
    echo json_encode([
        "success" => false,
        "message" => "Book not found."
    ]);
    exit();
}

if ($quantity > $book['stock']) {
    echo json_encode([
        "success" => false,
        "message" => "Quantity exceeds stock."
    ]);
    exit();
}

$cartStmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND book_id = ?");
$cartStmt->bind_param("ii", $user_id, $book_id);
$cartStmt->execute();
$existing = $cartStmt->get_result()->fetch_assoc();

if ($existing) {
    $newQty = $existing['quantity'] + $quantity;

    if ($newQty > $book['stock']) {
        echo json_encode([
            "success" => false,
            "message" => "Cart quantity exceeds available stock."
        ]);
        exit();
    }

    $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $updateStmt->bind_param("ii", $newQty, $existing['id']);
    $updateStmt->execute();
} else {
    $insertStmt = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
    $insertStmt->bind_param("iii", $user_id, $book_id, $quantity);
    $insertStmt->execute();
}

$countStmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
$countStmt->bind_param("i", $user_id);
$countStmt->execute();
$count = $countStmt->get_result()->fetch_assoc();

echo json_encode([
    "success" => true,
    "message" => "Book added to cart successfully.",
    "cart_count" => $count['total'] ?? 0
]);
?>
