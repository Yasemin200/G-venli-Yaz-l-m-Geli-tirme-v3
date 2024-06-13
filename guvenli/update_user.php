<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

// Handle the user update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['update_user_id'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $stmt = $db->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->bindValue(1, $username, SQLITE3_TEXT);
    $stmt->bindValue(2, $role, SQLITE3_TEXT);
    $stmt->bindValue(3, $id, SQLITE3_INTEGER);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}
?>
