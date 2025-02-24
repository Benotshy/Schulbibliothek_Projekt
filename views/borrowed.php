<?php
session_start();
include 'partials/sidebar.php';
require '../includes/dbh.inc.php';
require '../includes/statusUpdate.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

$user_id = $_SESSION['user_id'];
$limit = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

//fetch borrowed books (with pagination)
$query = "
    SELECT books.id_book, books.title, books.author, emprunts.return_date, emprunts.loan_status
    FROM emprunts
    JOIN books ON emprunts.id_book = books.id_book
    WHERE emprunts.id_user = ?
    AND emprunts.loan_status IN ('BORROWED', 'OVERDUE')
    GROUP BY books.id_book
    LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$borrowed_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// count total borrowed books
$countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM emprunts
    WHERE id_user = ? AND loan_status IN ('BORROWED', 'OVERDUE')
");
$countStmt->execute([$user_id]);
$totalBooks = $countStmt->fetchColumn();
$totalPages = ceil($totalBooks / $limit);
?>

<main class="content">
    <h2>📚 My Borrowed Books</h2>

    <a href="index.php" class="back-btn">🔙 Back to Library</a>

    <table class="books-table">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Due Date ⏳</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if (empty($borrowed_books)): ?>
            <tr><td colspan="5">No borrowed books found.</td></tr>
        <?php else: ?>
            <?php foreach ($borrowed_books as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['return_date']) ?></td>
                    <td class="<?= $book['loan_status'] === 'OVERDUE' ? 'overdue' : 'borrowed' ?>">
                        <?= htmlspecialchars($book['loan_status']) ?>
                    </td>
                    <td>
                        <?php if ($book['loan_status'] === 'OVERDUE' || $book['loan_status'] === 'BORROWED'): ?>
                            <button class="return-btn" onclick="openReturnModal(<?= $book['id_book'] ?>)">Return</button>
                        <?php else: ?>
                            No action available
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="prev">← Previous</a>
        <?php endif; ?>

        <span>Page <?= $page ?> of <?= $totalPages ?></span>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="next">Next →</a>
        <?php endif; ?>
    </div>
</main>


<div id="returnModal" class="modal hidden">
    <div class="modal-content">
        <span class="close" onclick="closeReturnModal()">&times;</span>
        <h3>📦 Confirm Return</h3>
        <p>Please ensure you have **placed the book in the library** before confirming its return.</p>
        <form id="returnForm" action="../controllers/return.php" method="POST">
            <input type="hidden" name="return_book_id" id="return_book_id">
            <button type="submit" class="confirm-btn">Confirm Return</button>
            <button type="button" class="cancel-btn" onclick="closeReturnModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openReturnModal(bookId) {
        document.getElementById('return_book_id').value = bookId;
        document.getElementById('returnModal').classList.remove("hidden");
        document.getElementById('returnModal').classList.add("active");
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.remove("active");
        document.getElementById('returnModal').classList.add("hidden");
    }
</script>

<script src="../assets/js/main.js"></script>
<?php include 'partials/footer.php'; ?>
