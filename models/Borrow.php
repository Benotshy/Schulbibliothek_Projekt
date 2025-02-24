<?php
require_once '../includes/dbh.inc.php';

class Borrow {
    private $conn;

    public function __construct($pdo) {
        $this->conn = $pdo;
    }

    public function isOverdue($user_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM emprunts WHERE id_user = ? AND loan_status = 'LATE'");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function hasReachedBorrowLimit($user_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM emprunts WHERE id_user = ? AND loan_status = 'BORROWED'");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() >= 3;
    }

    public function isBookAvailable($book_id) {
        $stmt = $this->conn->prepare("SELECT book_status FROM books WHERE id_book = ?");
        $stmt->execute([$book_id]);
        return $stmt->fetchColumn() === 'available';
    }

    public function alreadyBorrowedBook($user_id, $book_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM emprunts WHERE id_user = ? AND id_book = ? AND loan_status = 'BORROWED'");
        $stmt->execute([$user_id, $book_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function borrowBook($user_id, $book_id) {
        $date_emprunt = date('Y-m-d');
        $return_date = date('Y-m-d', strtotime('+4 weeks'));

        $stmt = $this->conn->prepare("INSERT INTO emprunts (id_user, id_book, date_emprunt, return_date, loan_status) VALUES (?, ?, ?, ?, 'BORROWED')");
        $stmt->execute([$user_id, $book_id, $date_emprunt, $return_date]);

        $stmt = $this->conn->prepare("UPDATE books SET book_status = 'borrowed' WHERE id_book = ?");
        return $stmt->execute([$book_id]);
    }

    public function returnBook($user_id, $book_id) {
        $stmt = $this->conn->prepare("SELECT id_emprunt FROM emprunts WHERE id_user = ? AND id_book = ? AND loan_status IN ('BORROWED', 'OVERDUE')");
        $stmt->execute([$user_id, $book_id]);
        $borrow_record = $stmt->fetch();

        if (!$borrow_record) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE emprunts SET return_date = CURRENT_DATE, loan_status = 'RETURNED' WHERE id_emprunt = ?");
        $stmt->execute([$borrow_record['id_emprunt']]);

        $stmt = $this->conn->prepare("UPDATE books SET book_status = 'available' WHERE id_book = ?");
        return $stmt->execute([$book_id]);
    }
}
