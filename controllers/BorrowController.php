<?php
session_start();
require '../includes/dbh.inc.php';
require '../models/Borrow.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../views/index.php?error=Access Denied");
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'];

$borrowModel = new Borrow($pdo);

// ✅ Step 1: Check if the user has any overdue books
if ($borrowModel->isOverdue($user_id)) {
    echo "<script>alert('Please contact your admin.'); window.location.href = '../views/index.php';</script>";
    exit();
}

// ✅ Step 2: Check if the user has reached the borrowing limit
if ($borrowModel->hasReachedBorrowLimit($user_id)) {
    header("Location: ../views/index.php?error=You can only borrow up to 3 books.");
    exit();
}

// ✅ Step 3: Check if the book is available
if (!$borrowModel->isBookAvailable($book_id)) {
    header("Location: ../views/index.php?error=Book is currently unavailable.");
    exit();
}

// ✅ Step 4: Check if the user has already borrowed this book
if ($borrowModel->alreadyBorrowedBook($user_id, $book_id)) {
    header("Location: ../views/index.php?error=You already borrowed this book.");
    exit();
}

// ✅ Step 5: Borrow the book
if ($borrowModel->borrowBook($user_id, $book_id)) {
    header("Location: ../views/index.php?success=Book borrowed successfully.");
    exit();
} else {
    header("Location: ../views/index.php?error=Failed to borrow book.");
    exit();
}
