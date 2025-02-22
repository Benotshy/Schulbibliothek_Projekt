<?php
session_start();
require '../includes/dbh.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../views/index.php?error=Access Denied");
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'];

// ✅ Step 1: Check if the user has any overdue books (LATE status)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_user = ? AND loan_status = 'LATE'");
$stmt->execute([$user_id]);
$late_count = $stmt->fetchColumn();

if ($late_count > 0) {
    echo "<script>alert('Please contact your admin.'); window.location.href = '../views/index.php';</script>";
    exit();
}

// ✅ Step 2: Check if student already borrowed 3 books
$stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_user = ? AND loan_status = 'BORROWED'");
$stmt->execute([$user_id]);
$borrowed_count = $stmt->fetchColumn();

if ($borrowed_count >= 3) {
    header("Location: ../views/index.php?error=You can only borrow up to 3 books.");
    exit();
}

// ✅ Step 3: Check if the book is available
$stmt = $pdo->prepare("SELECT book_status FROM books WHERE id_book = ?");
$stmt->execute([$book_id]);
$book_status = $stmt->fetchColumn();

if ($book_status !== 'available') {
    header("Location: ../views/index.php?error=Book is currently unavailable.");
    exit();
}

// ✅ Step 4: Check if the user already borrowed the same book
$stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_user = ? AND id_book = ? AND loan_status = 'BORROWED'");
$stmt->execute([$user_id, $book_id]);
$already_borrowed = $stmt->fetchColumn();

if ($already_borrowed > 0) {
    header("Location: ../views/index.php?error=You already borrowed this book.");
    exit();
}

// ✅ Step 5: Insert borrowing record
$date_emprunt = date('Y-m-d');
$return_date = date('Y-m-d', strtotime('+4 weeks'));

$stmt = $pdo->prepare("INSERT INTO emprunts (id_user, id_book, date_emprunt, return_date, loan_status) VALUES (?, ?, ?, ?, 'BORROWED')");
$stmt->execute([$user_id, $book_id, $date_emprunt, $return_date]);

// ✅ Step 6: Mark book as borrowed
$stmt = $pdo->prepare("UPDATE books SET book_status = 'borrowed' WHERE id_book = ?");
$stmt->execute([$book_id]);

header("Location: ../views/index.php?success=Book borrowed successfully.");
exit();
