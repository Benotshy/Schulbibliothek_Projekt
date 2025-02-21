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

// ✅ Handle Editing a Book (Fix for Duplicate Issue)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_book'])) {
  $id = $_POST['id_book'];
  $title = $_POST['title'];
  $author = $_POST['author'];
  $book_status = $_POST['book_status'];

  if (empty($title) || empty($author)) {
    header("Location: ../views/manage_books.php?error=All fields are required");
    exit();
  }

  try {
    // 🛠 FIX: Ensure UPDATE instead of INSERT (prevent duplicate issue)
    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, book_status = ? WHERE id_book = ?");
    $stmt->execute([$title, $author, $book_status, $id]);

    header("Location: ../views/manage_books.php?success=Book updated successfully");
    exit();
  } catch (PDOException $e) {
    die("Error updating book: " . $e->getMessage());
  }
}

// ✅ Handle Adding a Book
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

// ✅ Handle Deleting a Book (Fixed)
if (isset($_GET['delete'])) {
  $user_id = $_GET['delete'];

  try {
    // 🔍 Check if the user has borrowed books
    $stmt = $pdo->prepare("SELECT COUNT(*) AS borrowed_count FROM emprunts WHERE id_user = ? AND loan_status = 'BORROWED'");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['borrowed_count'] > 0) {
      // ❌ Cannot delete users with borrowed books (Redirect with error)
      header("Location: ../views/users.php?message=User cannot be deleted! They have borrowed books.&type=error");
      exit();
    } else {
      // ✅ Proceed with user deletion
      $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
      $stmt->execute([$user_id]);

      header("Location: ../views/users.php?message=User deleted successfully.&type=success");
      exit();
    }
  } catch (PDOException $e) {
    header("Location: ../views/users.php?message=Error deleting user.&type=error");
    exit();
  }
}
