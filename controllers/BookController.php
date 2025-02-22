<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../views/index.php?error=Access Denied");
  exit();
}

require_once '../includes/dbh.inc.php';

// ✅ New Feature: Handle Book Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
  // Search books by title or author
  $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
  $stmt->execute(["%$search%", "%$search%"]);
} else {
  // Fetch all books if no search term is provided
  $stmt = $pdo->prepare("SELECT * FROM books");
  $stmt->execute();
}

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Handle Editing a Book (Prevent Duplicate Issue)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_book'])) {
  $id = $_POST['id_book'];
  $title = $_POST['title'];
  $author = $_POST['author'];
  $book_status = $_POST['book_status'];

  if (empty($id) || empty($title) || empty($author)) {
    header("Location: ../views/manage_books.php?error=All fields are required");
    exit();
  }

  try {
    // 🔍 Check if Book Exists Before Updating
    $checkStmt = $pdo->prepare("SELECT id_book FROM books WHERE id_book = ?");
    $checkStmt->execute([$id]);

    if ($checkStmt->rowCount() == 0) {
      header("Location: ../views/manage_books.php?error=Book not found");
      exit();
    }

    // 🛠 FIX: Properly Update the Book Instead of Inserting a New One
    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, book_status = ? WHERE id_book = ?");
    $stmt->execute([$title, $author, $book_status, $id]);

    header("Location: ../views/manage_books.php?success=Book updated successfully");
    exit();
  } catch (PDOException $e) {
    die("Error updating book: " . $e->getMessage());
  }
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update_book'])) {
  $title = $_POST['title'];
  $author = $_POST['author'];
  $book_status = $_POST['book_status'];

  if (empty($title) || empty($author)) {
    header("Location: ../views/manage_books.php?error=All fields are required");
    exit();
  }

  try {
    $stmt = $pdo->prepare("INSERT INTO books (title, author, book_status) VALUES (?, ?, ?)");
    $stmt->execute([$title, $author, $book_status]);

    header("Location: ../views/manage_books.php?success=Book added successfully");
    exit();
  } catch (PDOException $e) {
    die("Error adding book: " . $e->getMessage());
  }
}



if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
  $book_id = $_GET['delete']; // ✅ Make sure we are using the correct variable

  try {
    // 🔍 Check if the book is currently borrowed
    $stmt = $pdo->prepare("SELECT book_status FROM books WHERE id_book = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book && $book['book_status'] == 'borrowed') {
      // ❌ Cannot delete borrowed books (Redirect with error)
      header("Location: ../views/manage_books.php?message=Cannot delete a borrowed book.&type=error");
      exit();
    } else {
      // ✅ Proceed with book deletion
      $stmt = $pdo->prepare("DELETE FROM books WHERE id_book = ?");
      $stmt->execute([$book_id]);

      header("Location: ../views/manage_books.php?message=Book deleted successfully.&type=success");
      exit();
    }
  } catch (PDOException $e) {
    header("Location: ../views/manage_books.php?message=Error deleting book.&type=error");
    exit();
  }
}
