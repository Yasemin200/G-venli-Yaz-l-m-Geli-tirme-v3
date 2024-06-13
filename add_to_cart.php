<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$shoe_id = $_POST['shoe_id'] ?? 0;

if ($shoe_id > 0) {
    $stmt = $db->prepare("INSERT INTO carts (user_id, shoe_id, quantity) VALUES (?, ?, ?)");
    $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $shoe_id, SQLITE3_INTEGER);
    $stmt->bindValue(3, 1, SQLITE3_INTEGER); // Miktar 1 olarak ekleniyor
    $stmt->execute();
}

header("Location: index.php");
exit();
?>
