<?php
session_start();
include 'partials/sidebar.php';
require '../includes/dbh.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php?error=Access Denied");
  exit();
}

$limit = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$searchQuery = "";
$params = [$_SESSION['user_id']];


if (!empty($_GET['search'])) {
  $search = '%' . $_GET['search'] . '%';
  $searchQuery = " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
  array_push($params, $search, $search, $search);
}


$query = "
    SELECT u.id_user, u.first_name, u.last_name, u.email, u.role, u.created_at,
           COUNT(e.id_emprunt) AS borrowed_count,
           GROUP_CONCAT(DISTINCT b.title SEPARATOR ', ') AS borrowed_books,
           SUM(CASE WHEN e.loan_status = 'OVERDUE' THEN 1 ELSE 0 END) AS overdue_count
    FROM users u
    LEFT JOIN emprunts e ON u.id_user = e.id_user AND e.loan_status = 'BORROWED'
    LEFT JOIN books b ON e.id_book = b.id_book
    WHERE u.id_user != ? $searchQuery
    GROUP BY u.id_user
    LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Count total users for pagination
$countQuery = "SELECT COUNT(*) FROM users u WHERE u.id_user != ? $searchQuery";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute(array_slice($params, 0, count($params)));
$totalUsers = $countStmt->fetchColumn();
$totalPages = ($totalUsers > 0) ? ceil($totalUsers / $limit) : 1; //avoid division by zero
?>
<main class="content">
  <h2>👤 Manage Users</h2>

  <?php if (isset($_GET['message'])): ?>
    <div class="popup <?= ($_GET['type'] == 'error') ? 'error' : 'success' ?>">
      <?= htmlspecialchars($_GET['message']) ?>
      <button onclick="closePopup()">✖</button>
    </div>
  <?php endif; ?>

  <form action="users.php" method="GET" class="search-bar">
    <input type="text" name="search" placeholder="Search users..."
      value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button type="submit">🔍 Search</button>
  </form>


  <table class="users-table">
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Created At</th>
      <th>Borrowed Books</th>
      <th>Overdue Books</th>
      <th>Action</th>
    </tr>
    <?php if (empty($users)): ?>
      <tr>
        <td colspan="7"><strong>⚠️ No users found.</strong></td>
      </tr>
    <?php else: ?>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= htmlspecialchars($user['role']) ?></td>
          <td><?= htmlspecialchars($user['created_at']) ?></td>
          <td>
            <?php if ($user['borrowed_count'] > 0): ?>
              <span class="borrowed-books" onclick="alert('Borrowed Books:\n<?= htmlspecialchars($user['borrowed_books']) ?>')">
                <?= $user['borrowed_count'] ?> book(s)
              </span>
            <?php else: ?>
              No borrowed books
            <?php endif; ?>
          </td>
          <td>
            <?= ($user['overdue_count'] > 0) ? "<span class='overdue-count'>" . $user['overdue_count'] . " overdue book(s)</span>" : "No overdue books"; ?>
          </td>
          <td>
            <button class="delete-btn" onclick="confirmDelete(<?= $user['id_user'] ?>)">
              ❌ Delete
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>
</main>

<!-- javaScript to handle click on borrowed books count -->
<script>
  function confirmDelete(userId) {
      if (confirm("Are you sure you want to delete this user?")) {
          window.location.href = "../controllers/UserController.php?delete=" + userId;
      }
  }
</script>
<?php include 'partials/footer.php'; ?>
