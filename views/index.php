<?php
session_start();


if (!isset($_SESSION['user_id'])) {
  header("Location: login.php?error=Please login first");
  exit();
}
require_once '../includes/dbh.inc.php'; // Database connection
require '../includes/statusUpdate.php';

// Fetch all books
$stmt = $pdo->prepare("SELECT * FROM books");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <b>YAAY your are in the home page :D !!</b>
  <a href="../controllers/logout.php">Logout</a>
  <h2>Library Books</h2>
  <a href="borrowed.php">📚 My Borrowed Books</a>
  <?php if ($_SESSION['role'] === "admin"): ?>
        <li><a href="manage_books.php">📖 Manage Books</a></li>
        <li><a href="users.php">👤 Manage Users</a></li>
    <?php endif; ?>
  <table border="1">
    <tr>
      <th>Title</th>
      <th>Author</th>
      <th>Status</th>
    </tr>
    <?php foreach ($books as $book): ?>
      <tr>
        <td><?= htmlspecialchars($book['title']) ?></td>
        <td><?= htmlspecialchars($book['author']) ?></td>
        <td><?= htmlspecialchars($book['book_status']) ?></td>
        <td>
          <?php if ($book['book_status'] == 'available'): ?>
            <form action="../controllers/BorrowController.php" method="POST">
              <input type="hidden" name="book_id" value="<?= $book['id_book'] ?>">
              <button type="submit">Borrow</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>

</html>
