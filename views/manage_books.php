<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php?error=Access Denied");
  exit();
}

require_once '../includes/dbh.inc.php';

// Fetch all books
$stmt = $pdo->prepare("SELECT * FROM books ORDER BY id_book DESC");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Books</title>
  <script>
    // Function to open the edit modal and fill in book details
    function openEditModal(id, title, author, status) {
      document.getElementById("edit_id").value = id;
      document.getElementById("edit_title").value = title;
      document.getElementById("edit_author").value = author;
      document.getElementById("edit_status").value = status;
      document.getElementById("editModal").style.display = "block";
    }

    // Function to close the modal
    function closeEditModal() {
      document.getElementById("editModal").style.display = "none";
    }
  </script>
</head>

<body>
  <h2>📖 Manage Books</h2>
  <a href="index.php">🔙 Back to Dashboard</a>

  <h3>Add a New Book</h3>
  <form action="../controllers/BookController.php" method="POST">
    <label>Title:</label>
    <input type="text" name="title" required>

    <label>Author:</label>
    <input type="text" name="author" required>

    <label>Status:</label>
    <select name="book_status">
      <option value="available">Available</option>
      <option value="borrowed">Borrowed</option>
    </select>

    <button type="submit">Add Book</button>
  </form>

  <h3>📚 Existing Books</h3>
  <table border="1">
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Author</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($books as $book): ?>
      <tr>
        <td><?= htmlspecialchars($book['id_book']) ?></td>
        <td><?= htmlspecialchars($book['title']) ?></td>
        <td><?= htmlspecialchars($book['author']) ?></td>
        <td><?= htmlspecialchars($book['book_status']) ?></td>
        <td>
          <button
            onclick="openEditModal('<?= $book['id_book'] ?>', '<?= htmlspecialchars($book['title']) ?>', '<?= htmlspecialchars($book['author']) ?>', '<?= $book['book_status'] ?>')">✏️
            Edit</button>
          <a href="../controllers/BookController.php?delete=<?= $book['id_book'] ?>"
            onclick="return confirm('Are you sure you want to delete this book?');">🗑 Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <!-- Edit Book Modal (Hidden by Default) -->
  <div id="editModal"
    style="display: none; position: fixed; top: 20%; left: 50%; transform: translate(-50%, -20%); background: white; padding: 20px; border: 1px solid black;">
    <h3>Edit Book</h3>
    <form action="../controllers/BookController.php" method="POST">
      <input type="hidden" name="update_book" value="1">
      <input type="hidden" id="edit_id" name="id_book">

      <label>Title:</label>
      <input type="text" id="edit_title" name="title" required>

      <label>Author:</label>
      <input type="text" id="edit_author" name="author" required>

      <label>Status:</label>
      <select id="edit_status" name="book_status">
        <option value="available">Available</option>
        <option value="borrowed">Borrowed</option>
      </select>

      <button type="submit" name="update_book">Update</button>
      <button type="button" onclick="closeEditModal()">Cancel</button>
    </form>
  </div>
</body>

</html>
