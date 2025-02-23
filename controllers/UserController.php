<?php
session_start();
require_once '../includes/dbh.inc.php';
require_once '../models/User.php';

if (isset($_POST['submit'])) {
    require_once '../models/User.php';

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['pwd'], PASSWORD_DEFAULT);

    try {
        $user = new User($pdo);
        $user->addUser($first_name, $last_name, $password, $email);

        header("Location: ../views/login.php?success=User created successfully.");
        exit();
    } catch (PDOException $e) {
        header("Location: ../views/login.php?error=Error: " . urlencode($e->getMessage())); //encodes the error message so it can be safely passed in a URL
        exit();
    }
}


if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];

    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        header("Location: ../views/users.php?error=You cannot delete yourself.");
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
        $stmt->execute([$user_id]);

        header("Location: ../views/users.php?success=User deleted successfully.");
        exit();
    } catch (PDOException $e) {
        die("Error deleting user: " . $e->getMessage());
    }
}
