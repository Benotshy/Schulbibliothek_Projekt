<?php
require_once '../includes/dbh.inc.php';

class User {
  private $db;

  public function __construct($pdo){
    $this->db = $pdo;
  }

  public function addUser($first_name, $last_name, $pwd, $email) {
    $stmt = $this->db->prepare("INSERT INTO users (first_name, last_name, pwd, email, role) VALUES (:first_name, :last_name, :pwd, :email, 'student')");
    $stmt->bindValue(':first_name', $first_name);
    $stmt->bindValue(':last_name', $last_name);
    $stmt->bindValue(':pwd', $pwd);
    $stmt->bindValue(':email', $email);
    return $stmt->execute();
  }

  public function getUserByEmail($email) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // return the result as an associative array
  }
}

