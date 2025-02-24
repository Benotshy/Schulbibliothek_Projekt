<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']); // Clear errors after displaying them
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/auth.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Schulbibliothek</title>
</head>

<body>


  <div class="container">
    <div class="form-box login">
      <form action="../controllers/AuthController.php" method="POST">
        <h1>Login</h1>
        <div class="input-box">
          <!-- <label for="email">Enter your email:</label> -->
          <input type="text" id="email" name="email" placeholder="youremail@gmail.com" required>
          <i class='bx bxs-user'></i>
        </div>
        <div class="input-box">
          <!-- <label for="pwd">Enter your password:</label> -->
          <input type="password" id="pwd" name="pwd" placeholder="password" required>
          <i class='bx bxs-lock'></i>
        </div>
        <div class="forgot-link">
          <a href="#">Forgot password?</a>
        </div>
        <button type="submit" class="btn">Login</button>
        <p>Nothing better than the feeling of finishing a book :D !</p>
      </form>
    </div>
    <div class="form-box register">
      <form action="../controllers/UserController.php" method="POST">
        <h1>Registration</h1>

        <div class="input-box">
          <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
          <i class='bx bx-user-circle'></i>
        </div>

        <div class="input-box">
          <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
          <i class='bx bx-user-circle'></i>
        </div>

        <div class="input-box">
          <input type="text" name="email" id="email" placeholder="youremail@gmail.com" required
            class="<?= isset($_SESSION['errors']['email']) ? 'error-input' : '' ?>">
          <i class='bx bx-envelope'></i>

          <!-- show error message below input -->
          <?php if (isset($_SESSION['errors']['email'])): ?>
            <p class="error-text"><?= $_SESSION['errors']['email']; ?></p>
          <?php endif; ?>
        </div>

        <div class="input-box">
          <input type="password" name="pwd" id="pwd" placeholder="Password" required>
          <i class='bx bxs-lock'></i>
        </div>

        <button type="submit" name="submit" class="btn">Register</button>
      </form>

    </div>
    <div class="toggle-box">
      <div class="toggle-panel toggle-left">
        <h1>Hello, Welcome!</h1>
        <p>Don't have an account ?</p>
        <button class="btn register-btn">Register</button>
      </div>
      <div class="toggle-panel toggle-right">
        <h1>Welcome Back !</h1>
        <p>Already have an account ?</p>
        <button class="btn login-btn">Login</button>
      </div>
    </div>
  </div>
  <script src="../assets/js/auth.js"></script>
</body>

</html>
