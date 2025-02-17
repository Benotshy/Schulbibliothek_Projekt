<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php?error=Please login first");
  exit();
}

require_once '../includes/dbh.inc.php'; // Database connection
require '../includes/statusUpdate.php';

// ✅ Handle the search functionality
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Library Home</title>
</head>

<body>
  <b>YAAY you are in the home page :D !!</b>
  <a href="../controllers/logout.php">Logout</a>
  <h2>📚 Library Books</h2>

  <form action="index.php" method="GET">
    <input type="text" name="search" placeholder="Search for books..." value="<?= htmlspecialchars($search) ?>" required>
    <button type="submit">🔍 Search</button>
  </form>

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
      <th>Action</th>
    </tr>
    <?php if (empty($books)): ?>
      <tr><td colspan="4">❌ No books found.</td></tr>
    <?php else: ?>
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
            <?php else: ?>
              Not Available
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>
</body>

</html>
