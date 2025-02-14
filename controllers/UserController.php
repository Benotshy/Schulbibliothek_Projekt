<?php
require_once '../includes/dbh.inc.php';
require_once '../models/User.php';

if(isset($_POST['submit'])){
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$_pwd = password_hash($_POST['pwd'], PASSWORD_DEFAULT);

try{
  $user = new User($pdo);
  $user->addUser($first_name, $last_name, $_pwd, $email);
  header("Location: ../views/login.php");
  exit();
} catch (PDOException $e){
  echo "Error: " . $e->getMessage();
  exit();
}
}
