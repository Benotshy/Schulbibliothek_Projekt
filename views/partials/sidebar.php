<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$current_page = basename($_SERVER['PHP_SELF']); // Get the current file name

// ✅ Set profile picture based on role
$profile_picture = "../assets/img/user_img.jpg"; // Default: Student
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
  $profile_picture = "../assets/img/admin_pic.png"; // Admin picture
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Library System</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/manage_B.css">
  <link rel="stylesheet" href="../assets/css/manage_U.css">
  <script src="../assets/js/edit_modal.js"></script>
</head>

<body>

  <aside class="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">📚 Schulbibliothek</div>
    <p class="credits">Welcome back 😃!</p>

    <!-- ✅ User Profile Section -->
    <div class="user-profile">
      <a href="index.php">
        <img src="<?= $profile_picture ?>" alt="User">
      </a>
      <p class="user-name">
        <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?>
      </p>
    </div>

    <!-- Navigation Links (Role-Based) -->
    <nav class="sidebar-nav">
      <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
        <i class='bx bx-home'></i> Dashboard
      </a>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
        <!-- Admin Features -->
        <a href="manage_books.php" class="<?= $current_page == 'manage_books.php' ? 'active' : '' ?>">
          <i class='bx bx-book'></i> Manage Books
        </a>
        <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">
          <i class='bx bx-user'></i> Manage Users
        </a>
        <div class="filter-section">
          <label>
            <input type="checkbox" id="availableFilter"> Show Available Books
          </label>
        </div>
      <?php } else { ?>
        <!-- User Features -->
        <a href="borrowed.php" class="<?= $current_page == 'borrowed.php' ? 'active' : '' ?>">
          <i class='bx bxs-book'></i> My Borrowed Books
        </a>
      <?php } ?>

      <!-- Filter Section (For Users) -->
      <?php if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { ?>
        <div class="filter-section">
          <label>
            <input type="checkbox" id="availableFilter"> Show Available Books
          </label>
        </div>
      <?php } ?>

      <!-- Logout Button -->
      <a href="../controllers/Logout.php" class="logout">
        <i class='bx bx-log-out'></i> Logout
      </a>
    </nav>
  </aside>
