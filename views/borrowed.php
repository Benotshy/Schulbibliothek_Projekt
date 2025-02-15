<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

require_once '../includes/dbh.inc.php'; // Database connection

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch books that this user has borrowed
$stmt = $pdo->prepare("SELECT books.title, books.author, books.book_status
                       FROM emprunts
                       JOIN books ON emprunts.id_book = books.id_book
                       WHERE emprunts.id_user = ?");
$stmt->execute([$user_id]);
$borrowed_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowed Books</title>
</head>
<body>
    <h2>📚 My Borrowed Books</h2>
    <a href="index.php">🔙 Back to Library</a>

    <table border="1">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Status</th>
        </tr>
        <?php foreach ($borrowed_books as $book): ?>
            <tr>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= htmlspecialchars($book['book_status']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
