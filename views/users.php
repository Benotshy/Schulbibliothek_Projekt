<?php
session_start();
include 'partials/sidebar.php'; // ✅ Sidebar already contains <html>, <head>, <body>
require '../includes/dbh.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php?error=Access Denied");
  exit();
}

$limit = 8; // Users per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$searchQuery = "";
$params = [$_SESSION['user_id']];

// ✅ Handling Search Query Correctly
if (!empty($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $searchQuery = " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
    array_push($params, $search, $search, $search);
}

// ✅ Fetch users with search filter and pagination
$query = "
    SELECT u.id_user, u.first_name, u.last_name, u.email, u.role, u.created_at,
           COUNT(e.id_emprunt) AS borrowed_count,
           GROUP_CONCAT(b.title SEPARATOR ', ') AS borrowed_books
    FROM users u
    LEFT JOIN emprunts e ON u.id_user = e.id_user AND e.loan_status = 'BORROWED'
    LEFT JOIN books b ON e.id_book = b.id_book
    WHERE u.id_user != ? $searchQuery
    GROUP BY u.id_user
    LIMIT $limit OFFSET $offset";  // ✅ FIXED: Directly inserting LIMIT & OFFSET

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Count total users for pagination (Without LIMIT & OFFSET)
$countQuery = "SELECT COUNT(*) FROM users WHERE id_user != ? $searchQuery";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute(array_slice($params, 0, count($params))); // ✅ Only using required params
$totalUsers = $countStmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);
?>

<main class="content">
  <h2>👤 Manage Users</h2>

  <!-- Search Bar -->
  <form action="users.php" method="GET" class="search-bar">
    <input type="text" name="search" placeholder="Search users..."
      value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button type="submit">🔍 Search</button>
  </form>

  <!-- Users Table -->
  <table class="users-table">
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Created At</th>
      <th>Borrowed Books</th>
      <th>Action</th>
    </tr>
    <?php if (empty($users)): ?>
      <tr><td colspan="6">No users found.</td></tr>
    <?php else: ?>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= htmlspecialchars($user['role']) ?></td>
          <td><?= htmlspecialchars($user['created_at']) ?></td>
          <td>
            <?php if ($user['borrowed_count'] > 0): ?>
              <span class="borrowed-books" data-books="<?= htmlspecialchars($user['borrowed_books']) ?>">
                <?= $user['borrowed_count'] ?> book(s)
              </span>
            <?php else: ?>
              No borrowed books
            <?php endif; ?>
          </td>
          <td>
            <a href="../controllers/UserController.php?delete=<?= $user['id_user'] ?>"
              onclick="return confirm('Are you sure you want to delete this user?');" class="delete-btn">
              ❌ Delete
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>

  <!-- Pagination -->
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

<script src="../assets/js/main.js"></script>
<?php include 'partials/footer.php'; ?>
