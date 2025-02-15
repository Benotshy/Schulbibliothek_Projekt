<?php
require_once '../includes/dbh.inc.php';

class User {
  private $db;
  public function __construct($pdo){
    $this->db = $pdo;
  }
  public function addUser($first_name, $last_name, $pwd, $email){
    $stmt = $this->db->prepare("INSERT INTO users (first_name, last_name, pwd, email) VALUES (:first_name, :last_name, :pwd, :email)");
    $stmt->bindValue(':first_name', $first_name);
    $stmt->bindValue(':last_name', $last_name);
    $stmt->bindValue(':pwd', $pwd);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
  }
  public function getUserByEmail($email) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user as an associative array
  }
}
// The User class has two methods: addUser and getUserByEmail.
// The $pdo variable is passed to the __construct function to initialize the $db property of the User class.
// This allows the User class to use the database connection for its methods.
// By passing the $pdo variable, we ensure that the User class has access to the database connection
// and can perform database operations such as adding a user.
