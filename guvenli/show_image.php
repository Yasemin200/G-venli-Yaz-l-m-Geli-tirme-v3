<?php
require_once 'db.php';

$shoe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($shoe_id > 0) {
    $stmt = $db->prepare("SELECT image FROM shoes WHERE id = ?");
    $stmt->bindValue(1, $shoe_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $shoe = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($shoe && file_exists($shoe['image'])) {
        header('Content-Type: image/jpeg');
        readfile($shoe['image']);
        exit();
    }
}

echo "Resim bulunamadı.";
?>
