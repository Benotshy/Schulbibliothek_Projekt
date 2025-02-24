<?php
require 'dbh.inc.php';

// update overdue books (if return_date < today and not returned)
$stmt = $pdo->prepare("UPDATE emprunts SET loan_status = 'OVERDUE' WHERE return_date < CURRENT_DATE AND loan_status = 'BORROWED'");
$stmt->execute();
