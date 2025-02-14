<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <b>YAAY your are in the home page :D !!</b>
  <a href="../controllers/logout.php">Logout</a>
</body>
</html>
