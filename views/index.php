<?php
session_start();
include 'partials/sidebar.php'; // ✅ Sidebar already contains <html>, <head>, <body>

require_once '../includes/dbh.inc.php';
require '../includes/statusUpdate.php';

// ✅ Handle pagination
$limit = 8; // Number of books per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ✅ Handle search & filter functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterAvailable = isset($_GET['available']) && $_GET['available'] === 'true';

// ✅ Fix: Ensuring WHERE clause is correctly structured before ORDER BY
$query = "SELECT books.*, emprunts.loan_status
          FROM books
          LEFT JOIN emprunts ON books.id_book = emprunts.id_book";
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(books.title LIKE ? OR books.author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filterAvailable) {
    $conditions[] = "books.book_status = 'available'";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// ✅ Fix: ORDER BY comes after WHERE clause, ensuring proper SQL execution
$query .= " ORDER BY FIELD(emprunts.loan_status, 'OVERDUE', 'BORROWED') DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Count total books for pagination
$countQuery = "SELECT COUNT(*) FROM books";
if (!empty($conditions)) {
    $countQuery .= " WHERE " . implode(" AND ", $conditions);
}
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalBooks = $countStmt->fetchColumn();
$totalPages = ceil($totalBooks / $limit);
?>

<main class="content">
  <form action="index.php" method="GET" class="search-bar">
    <input type="text" name="search" placeholder="Search for books..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">🔍 Search</button>
  </form>

  <div class="book-container">
    <?php if (empty($books)): ?>
      <p>No books found.</p>
    <?php else: ?>
      <?php foreach ($books as $book): ?>
        <div class="book-card">
          <img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="Book Cover">
          <h3><?= htmlspecialchars($book['title']) ?></h3>
          <p><?= htmlspecialchars($book['author']) ?></p>

          <!-- ✅ Fix: Correctly displaying OVERDUE status with styled button -->
          <?php if ($book['loan_status'] == 'OVERDUE'): ?>
            <!-- <span class="status overdue">Overdue</span> -->
            <button class="overdue-btn" disabled>Overdue</button>
          <?php elseif ($book['loan_status'] == 'BORROWED'): ?>
            <button class="borrowed-btn" disabled>Borrowed</button>
          <?php else: ?>
            <span class="status available">Available</span>
            <form action="../controllers/BorrowController.php" method="POST">
              <input type="hidden" name="book_id" value="<?= $book['id_book'] ?>">
              <button type="submit" class="borrow-btn available-btn"></button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&available=<?= $filterAvailable ? 'true' : '' ?>" class="prev">← Previous</a>
    <?php endif; ?>

    <span>Page <?= $page ?> of <?= $totalPages ?></span>

    <?php if ($page < $totalPages): ?>
      <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&available=<?= $filterAvailable ? 'true' : '' ?>" class="next">Next →</a>
    <?php endif; ?>
  </div>
</main>

<script src="../assets/js/filter.js"></script>
<script src="../assets/js/main.js"></script>

<?php include 'partials/footer.php'; ?>
