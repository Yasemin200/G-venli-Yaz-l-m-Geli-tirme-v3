<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$shoe_id = $_POST['shoe_id'] ?? 0;

if ($shoe_id > 0) {
    $stmt = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND shoe_id = ?");
    $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $shoe_id, SQLITE3_INTEGER);
    $stmt->execute();
}

header("Location: favorilerim.php");
exit();
?>
