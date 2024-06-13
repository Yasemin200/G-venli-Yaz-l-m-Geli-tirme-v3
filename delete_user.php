<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

// Handle the user deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['delete_user_id'];

    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bindValue(1, $id, SQLITE3_INTEGER);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}
?>
