<?php
require_once '../includes/dbh.inc.php';

class Book {
    private $conn;

    public function __construct($pdo) {
        $this->conn = $pdo;
    }

    public function getAllBooks($search = "") {
        if (!empty($search)) {
            $stmt = $this->conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
            $stmt->execute(["%$search%", "%$search%"]);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM books");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBook($id, $title, $author, $status) {
        $stmt = $this->conn->prepare("UPDATE books SET title = ?, author = ?, book_status = ? WHERE id_book = ?");
        return $stmt->execute([$title, $author, $status, $id]);
    }

    public function addBook($title, $author, $status) {
        $stmt = $this->conn->prepare("INSERT INTO books (title, author, book_status) VALUES (?, ?, ?)");
        return $stmt->execute([$title, $author, $status]);
    }

    public function deleteBook($id) {
        $stmt = $this->conn->prepare("DELETE FROM books WHERE id_book = ?");
        return $stmt->execute([$id]);
    }

    public function isBorrowed($id) {
        $stmt = $this->conn->prepare("SELECT book_status FROM books WHERE id_book = ?");
        $stmt->execute([$id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book && $book['book_status'] == 'borrowed';
    }
}
