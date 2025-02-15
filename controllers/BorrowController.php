<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: ../views/login.php?error=Please login first");
  exit();
}

require_once '../includes/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $user_id = $_SESSION['user_id'];
  $book_id = $_POST['book_id'];

  // Check if user already has 3 borrowed books
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_user = ?");
  $stmt->execute([$user_id]);
  $borrowed_count = $stmt->fetchColumn();

  if ($borrowed_count >= 3) {
    header("Location: ../views/index.php?error=You can only borrow up to 3 books.");
    exit();
  }

  // Insert into `emprunts` table
  $date_emprunt = date('Y-m-d');
  $return_date = date('Y-m-d', strtotime('+4 weeks'));

  $stmt = $pdo->prepare("INSERT INTO emprunts (id_user, id_book, date_emprunt, return_date) VALUES (?, ?, ?, ?)");
  $stmt->execute([$user_id, $book_id, $date_emprunt, $return_date]);


  // Update book status
  $stmt = $pdo->prepare("UPDATE books SET book_status = 'borrowed' WHERE id_book = ?");
  $stmt->execute([$book_id]);

  header("Location: ../views/index.php?success=Book borrowed successfully.");
  exit();
}
