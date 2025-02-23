<?php
session_start();
require '../includes/dbh.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../views/index.php?error=Access Denied");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['return_book_id'];

    // ✅ Step 1: Check if the book is borrowed or overdue by the user
    $stmt = $pdo->prepare("SELECT id_emprunt FROM emprunts WHERE id_user = ? AND id_book = ? AND loan_status IN ('BORROWED', 'OVERDUE')");
    $stmt->execute([$user_id, $book_id]);
    $borrow_record = $stmt->fetch();

    if (!$borrow_record) {
        header("Location: ../views/index.php?error=Book not found or already returned.");
        exit();
    }

    // ✅ Step 2: Mark book as returned in emprunts table
    $stmt = $pdo->prepare("UPDATE emprunts SET return_date = CURRENT_DATE, loan_status = 'RETURNED' WHERE id_emprunt = ?");
    if (!$stmt->execute([$borrow_record['id_emprunt']])) {
        die("Error updating emprunts: " . implode(", ", $stmt->errorInfo())); // Converts the array into a comma-separated string.
    }

    // ✅ Step 3: Update book status to available in books table
    $stmt = $pdo->prepare("UPDATE books SET book_status = 'available' WHERE id_book = ?");
    if (!$stmt->execute([$book_id])) {
        die("Error updating books: " . implode(", ", $stmt->errorInfo()));
    }

    // ✅ Step 4: Redirect user with success message
    header("Location: ../views/index.php?success=Book returned successfully.");
    exit();
} else {
    header("Location: ../views/index.php?error=Invalid request.");
    exit();
}
