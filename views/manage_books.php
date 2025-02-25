<?php
session_start();
include 'partials/sidebar.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php?error=Access Denied");
  exit();
}

require_once '../includes/dbh.inc.php';

// Fetch all books with pagination and search functionality
$limit = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$searchQuery = "";
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
  $searchQuery = "WHERE title LIKE :search OR author LIKE :search";
  $params['search'] = '%' . $_GET['search'] . '%';
}

$query = "SELECT * FROM books $searchQuery ORDER BY id_book DESC LIMIT :offset, :limit";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
if (!empty($params)) {
  foreach ($params as $key => &$value) {
    $stmt->bindParam(':' . $key, $value, PDO::PARAM_STR);
  }
}
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total book count for pagination
$countQuery = "SELECT COUNT(*) FROM books $searchQuery";
$countStmt = $pdo->prepare($countQuery);
if (!empty($params)) {
  foreach ($params as $key => &$value) {
    $countStmt->bindParam(':' . $key, $value, PDO::PARAM_STR);
  }
}
$countStmt->execute();
$totalBooks = $countStmt->fetchColumn();
$totalPages = ceil($totalBooks / $limit);
?>

<main class="content">
  <h2>📖 Manage Books</h2>

  <form action="manage_books.php" method="GET" class="search-bar">
    <input type="text" name="search" placeholder="Search for books..."
      value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button type="submit">🔍 Search</button>
  </form>

  <button class="add-book-btn" onclick="openAddModal()"><i class='bx bxs-book-add'></i> Add a New Book</button>

  <table class="books-table">
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
          <button type="button"
            onclick="openEditModal('<?= $book['id_book'] ?>', '<?= htmlspecialchars($book['title']) ?>', '<?= htmlspecialchars($book['author']) ?>', '<?= $book['book_status'] ?>')"><i class='bx bxs-edit-alt'></i>
            Edit</button>
          <a href="../controllers/BookController.php?delete=<?= $book['id_book'] ?>"
            onclick="return confirm('Are you sure you want to delete this book?');"><i class='bx bxs-trash'></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>" class="prev">← Previous</a>
    <?php endif; ?>

    <span>Page <?= $page ?> of <?= $totalPages ?></span>

    <?php if ($page < $totalPages): ?>
      <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>" class="next">Next →</a>
    <?php endif; ?>
  </div>
</main>

<div id="addModal" class="modal hidden">
  <div class="modal-content">
    <span class="close" onclick="closeAddModal()">&times;</span>
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
  </div>
</div>


<div id="editModal" class="modal hidden">
  <div class="modal-content">
    <span class="close" onclick="closeEditModal()">&times;</span>
    <h3>Edit Book</h3>
    <form action="../controllers/BookController.php" method="POST">
      <input type="hidden" id="edit_id" name="id_book">
      <input type="hidden" name="update_book" value="1">

      <label>Title:</label>
      <input type="text" id="edit_title" name="title" required>
      <label>Author:</label>
      <input type="text" id="edit_author" name="author" required>
      <label>Status:</label>
      <select id="edit_status" name="book_status">
        <option value="available">Available</option>
        <option value="borrowed">Borrowed</option>
      </select>
      <button type="submit">Update Book</button>
    </form>
  </div>
</div>



<script>
  document.addEventListener("DOMContentLoaded", function () {
    function showModal(modalId) {
      let modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove("hidden");
        modal.classList.add("active");
      }
    }

    function hideModal(modalId) {
      let modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove("active");
        modal.classList.add("hidden");
      }
    }

    window.openAddModal = function () {
      showModal('addModal');
    };

    window.closeAddModal = function () {
      hideModal('addModal');
    };

    //open Edit Book Modal
    window.openEditModal = function (id, title, author, status) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_title').value = title;
      document.getElementById('edit_author').value = author;
      document.getElementById('edit_status').value = status;
      showModal('editModal');
    };

    //close Edit Book Modal
    window.closeEditModal = function () {
      hideModal('editModal');
    };
  });
</script>
<?php include 'partials/footer.php'; ?>
