<?php
ob_start();

require_once '../config/database.php';

ob_clean();
header('Content-Type: application/json');

if (!$conn || $conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$q        = isset($_GET['q'])        ? trim($_GET['q'])        : '';
$category = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$filter   = isset($_GET['filter'])   ? trim($_GET['filter'])   : 'all';
$search   = "%" . $q . "%";

if ($category > 0 && $q !== '') {

    if ($filter === 'author') {
        $sql = "SELECT books.*, categories.name AS category_name
                FROM books JOIN categories ON books.category_id = categories.id
                WHERE books.author LIKE ? AND books.category_id = ?
                ORDER BY books.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $search, $category);

    } elseif ($filter === 'title') {
        $sql = "SELECT books.*, categories.name AS category_name
                FROM books JOIN categories ON books.category_id = categories.id
                WHERE books.title LIKE ? AND books.category_id = ?
                ORDER BY books.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $search, $category);

    } else {
        $sql = "SELECT books.*, categories.name AS category_name
                FROM books JOIN categories ON books.category_id = categories.id
                WHERE (books.title LIKE ? OR books.author LIKE ?) AND books.category_id = ?
                ORDER BY books.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $search, $search, $category);
    }

} elseif ($category > 0) {

    $sql = "SELECT books.*, categories.name AS category_name
            FROM books JOIN categories ON books.category_id = categories.id
            WHERE books.category_id = ?
            ORDER BY books.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category);

} elseif ($q !== '') {

    if ($filter === 'author') {
        $sql = "SELECT books.*, categories.name AS category_name
                FROM books JOIN categories ON books.category_id = categories.id
                WHERE books.author LIKE ?
                ORDER BY books.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search);

    } elseif ($filter === 'title') {
        $sql = "SELECT books.*, categories.name AS category_name
                FROM books JOIN categories ON books.category_id = categories.id
                WHERE books.title LIKE ?
                ORDER BY books.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search);

    } else {
        $sql = "SELECT books.*, categories.name AS category_name
                FROM books JOIN categories ON books.category_id = categories.id
                WHERE books.title LIKE ? OR books.author LIKE ?
                ORDER BY books.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $search, $search);
    }

} else {

    $sql = "SELECT books.*, categories.name AS category_name
            FROM books JOIN categories ON books.category_id = categories.id
            ORDER BY books.id DESC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

echo json_encode($books);
