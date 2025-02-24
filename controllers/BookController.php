<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../views/index.php?error=Access Denied");
  exit();
}

require_once '../models/Book.php';
require_once '../includes/dbh.inc.php';

$bookModel = new Book($pdo);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$books = $bookModel->getAllBooks($search);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_book'])) {
  $id = $_POST['id_book'];
  $title = $_POST['title'];
  $author = $_POST['author'];
  $book_status = $_POST['book_status'];

  if (empty($id) || empty($title) || empty($author)) {
    header("Location: ../views/manage_books.php?error=All fields are required");
    exit();
  }

  if ($bookModel->updateBook($id, $title, $author, $book_status)) {
    header("Location: ../views/manage_books.php?success=Book updated successfully");
  } else {
    die("Error updating book");
  }
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update_book'])) {
  $title = $_POST['title'];
  $author = $_POST['author'];
  $book_status = $_POST['book_status'];

  if (empty($title) || empty($author)) {
    header("Location: ../views/manage_books.php?error=All fields are required");
    exit();
  }

  if ($bookModel->addBook($title, $author, $book_status)) {
    header("Location: ../views/manage_books.php?success=Book added successfully");
  } else {
    die("Error adding book");
  }
  exit();
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
  $book_id = $_GET['delete'];

  if ($bookModel->isBorrowed($book_id)) {
    header("Location: ../views/manage_books.php?message=Cannot delete a borrowed book.&type=error");
    exit();
  }

  if ($bookModel->deleteBook($book_id)) {
    header("Location: ../views/manage_books.php?message=Book deleted successfully.&type=success");
  } else {
    header("Location: ../views/manage_books.php?message=Error deleting book.&type=error");
  }
  exit();
}
