<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Library System</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

  <aside class="sidebar">
    <div class="user-profile">
      <a href="index.php">
        <img src="../assets/img/user_img.jpg" alt="User">
      </a>
      <p>
        <?php
        echo isset($_SESSION['user_name'])
          ? htmlspecialchars($_SESSION['user_name'])
          : 'Guest';
        ?>
      </p>
    </div>

    <nav>
      <ul>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
          <li><a href="manage_books.php">Manage Books</a></li>
          <li><a href="manage_users.php">Manage Users</a></li>
        <?php } else { ?>
          <div>
          <a href="borrowed.php"><i class='bx bxs-book'></i>My Borrowed Books</a>
          </div>
        <?php } ?>
      </ul>
      <!-- Filter Section -->
      <div class="filter-section">
        <label>
          <input type="checkbox" id="availableFilter"> Show Available Books
        </label>
      </div>
      <a href="../controllers/Logout.php">Logout</a>

    </nav>
  </aside>
