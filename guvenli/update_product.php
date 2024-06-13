<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

// Handle the product update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['update_shoe_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $stmt = $db->prepare("UPDATE shoes SET name = ?, description = ?, price = ? WHERE id = ?");
    $stmt->bindValue(1, $name, SQLITE3_TEXT);
    $stmt->bindValue(2, $description, SQLITE3_TEXT);
    $stmt->bindValue(3, $price, SQLITE3_TEXT);
    $stmt->bindValue(4, $id, SQLITE3_INTEGER);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}
?>
