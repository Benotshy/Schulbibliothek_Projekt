<?php
session_start();
require '../includes/dbh.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch books the user has borrowed
$stmt = $pdo->prepare("SELECT books.id_book, books.title, books.author, emprunts.return_date
                       FROM emprunts
                       JOIN books ON emprunts.id_book = books.id_book
                       WHERE emprunts.id_user = ?
                       AND emprunts.loan_status = 'BORROWED'
                       GROUP BY books.id_book;");
$stmt->execute([$user_id]);
$borrowed_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Borrowed Books</title>
</head>
<body>
    <h2>📚 My Borrowed Books</h2>
    <a href="index.php">🔙 Back to Library</a>

    <table border="1">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Return Date</th>
        <th>Action</th>
    </tr>
    <?php foreach ($borrowed_books as $book): ?>
        <tr>
            <td><?= htmlspecialchars($book['title']) ?></td>
            <td><?= htmlspecialchars($book['author']) ?></td>
            <td><?= htmlspecialchars($book['return_date']) ?></td>
            <td>
                <form action="../controllers/return.php" method="POST">
                    <input type="hidden" name="return_book_id" value="<?= $book['id_book'] ?>">
                    <button type="submit">Return</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
