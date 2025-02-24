<?php
session_start();
require '../includes/dbh.inc.php';
require '../models/Borrow.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../views/index.php?error=Access Denied");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['return_book_id'];

    $borrowModel = new Borrow($pdo);

    // ✅ Attempt to return the book
    if ($borrowModel->returnBook($user_id, $book_id)) {
        header("Location: ../views/index.php?success=Book returned successfully.");
        exit();
    } else {
        header("Location: ../views/index.php?error=Book not found or already returned.");
        exit();
    }
} else {
    header("Location: ../views/index.php?error=Invalid request.");
    exit();
}
