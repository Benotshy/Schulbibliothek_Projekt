<?php
session_start();
require '../includes/dbh.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php?error=Access Denied");
  exit();
}

// ✅ Fetch all users except the current admin
$stmt = $pdo->prepare("
    SELECT u.id_user, u.first_name, u.last_name, u.email, u.role, u.created_at,
           COUNT(e.id_emprunt) AS borrowed_count,
           GROUP_CONCAT(b.title SEPARATOR ', ') AS borrowed_books
    FROM users u
    LEFT JOIN emprunts e ON u.id_user = e.id_user AND e.loan_status = 'BORROWED'
    LEFT JOIN books b ON e.id_book = b.id_book
    WHERE u.id_user != ?
    GROUP BY u.id_user
");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
  <h2>👤 Manage Users</h2>
  <a href="index.php">🔙 Back to Home</a>

  <table border="1">
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Created At</th>
      <th>Borrowed Books</th>
      <th>Action</th>
    </tr>
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
            onclick="return confirm('Are you sure you want to delete this user?');">
            ❌ Delete
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <script src="../assets/js/main.js"></script>
</body>

</html>
