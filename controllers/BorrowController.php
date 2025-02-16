<?php
session_start();
require '../includes/dbh.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../views/index.php?error=Access Denied");
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'];

// Check if student already borrowed 3 books
$stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_user = ? AND loan_status = 'BORROWED';");
$stmt->execute([$user_id]);
$borrowed_count = $stmt->fetchColumn();

if ($borrowed_count >= 3) {
    header("Location: ../views/index.php?error=You can only borrow up to 3 books.");
    exit();
}

// Check if book is available
$stmt = $pdo->prepare("SELECT book_status FROM books WHERE id_book = ?");
$stmt->execute([$book_id]);
$book_status = $stmt->fetchColumn();

if ($book_status !== 'available') {
    header("Location: ../views/index.php?error=Book is currently unavailable.");
    exit();
}

// Insert borrowing record
$date_emprunt = date('Y-m-d');
$return_date = date('Y-m-d', strtotime('+4 weeks'));

$stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_user = ? AND id_book = ? AND loan_status = 'BORROWED'");
$stmt->execute([$user_id, $book_id]);
$already_borrowed = $stmt->fetchColumn();

if ($already_borrowed > 0) {
    header("Location: ../views/index.php?error=You already borrowed this book.");
    exit();
}
$stmt = $pdo->prepare("INSERT INTO emprunts (id_user, id_book, date_emprunt, return_date) VALUES (?, ?, ?, ?)");
$stmt->execute([$user_id, $book_id, $date_emprunt, $return_date]);

// Mark book as borrowed
$stmt = $pdo->prepare("UPDATE books SET book_status = 'borrowed' WHERE id_book = ?");
$stmt->execute([$book_id]);

header("Location: ../views/index.php?success=Book borrowed successfully.");
exit();
