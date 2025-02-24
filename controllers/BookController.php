<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../views/index.php?error=Access Denied");
  exit();
}

require_once '../includes/dbh.inc.php';


$search = isset($_GET['search']) ? trim($_GET['search']) : ''; // trim helps prevent errors caused by accidental spaces.
if (!empty($search)) {
  $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
  $stmt->execute(["%$search%", "%$search%"]); //'%search%' allows partial matching, so users don’t have to type the exact title or author name.
} else {
  $stmt = $pdo->prepare("SELECT * FROM books");
  $stmt->execute();
}

$books = $stmt->fetchAll(PDO::FETCH_ASSOC); //returns results as associative arrays

// Editing a Book
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
    // checking if the books exists before modifying it
    $checkStmt = $pdo->prepare("SELECT id_book FROM books WHERE id_book = ?");
    $checkStmt->execute([$id]);

    if ($checkStmt->rowCount() == 0) {
      header("Location: ../views/manage_books.php?error=Book not found");
      exit();
    }

    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, book_status = ? WHERE id_book = ?");
    $stmt->execute([$title, $author, $book_status, $id]);

    header("Location: ../views/manage_books.php?success=Book updated successfully");
    exit();
  } catch (PDOException $e) {
    die("Error updating book: " . $e->getMessage());
  }
}


//adding a book
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


//editing a book
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
  $book_id = $_GET['delete'];

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
