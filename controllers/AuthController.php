<?php
// ob_start();
session_start();

require_once '../includes/dbh.inc.php';
require_once '../models/User.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    $userModel = new User($pdo);
    $user = $userModel->getUserByEmail($email);
    if ($user) {
        if (password_verify($pwd, $user['pwd'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_name'] = $user['first_name'] . " " . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header("Location: ../views/index.php");
            exit();
        } else {
            header("Location: ../views/login.php?error=Invalid credentials");
            exit();
        }
    } else {
        header("Location: ../views/login.php?error=User not found");
        exit();
    }
}

// ob_end_flush();
