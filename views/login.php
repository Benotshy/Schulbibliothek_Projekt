<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/style.css">
  <title>Document</title>
</head>
<body>
  <form action="../controllers/AuthController.php" method="POST">
    <label for="email">Enter your email:</label>
    <input type="text" id="email" name="email"><br>
    <label for="pwd">Enter your password:</label>
    <input type="password" id="pwd" name="pwd">
    <button type="submit">Login</button><br>
  </form>
</body>
</html>
