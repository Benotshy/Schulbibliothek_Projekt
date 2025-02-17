<?php
session_start();
require_once '../includes/dbh.inc.php';
require_once '../models/User.php';

// ✅ Ensure Only Admins Can Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../views/index.php?error=Access Denied");
    exit();
}

// ✅ Handle User Registration
if (isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['pwd'], PASSWORD_DEFAULT); // Secure password storage

    try {
        $user = new User($pdo);
        $user->addUser($first_name, $last_name, $password, $email);
        header("Location: ../views/login.php?success=User created successfully.");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// ✅ Handle User Deletion
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
