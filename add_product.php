<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: giris_kayit.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];

    // Güvenlik zafiyeti: Dosya türü ve boyutu kontrol edilmiyor
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Dosyanın kopyasını oluşturma
        $copy_dir = "uploads/uploads/";
        $copy_file = $copy_dir . basename($image);
        
        // Hedef dizin var mı kontrol et, yoksa oluştur
        if (!file_exists($copy_dir)) {
            mkdir($copy_dir, 0777, true);
        }

        copy($target_file, $copy_file);

        // Veritabanına kayıt
        $stmt = $db->prepare("INSERT INTO shoes (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bindValue(1, $name, SQLITE3_TEXT);
        $stmt->bindValue(2, $description, SQLITE3_TEXT);
        $stmt->bindValue(3, $price, SQLITE3_FLOAT);
        $stmt->bindValue(4, $image, SQLITE3_TEXT);
        $stmt->execute();

        header("Location: admin.php");
        exit();
    } else {
        echo "Dosya yüklenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Ekle</title>
    <link rel="stylesheet" href="style_index.css">
</head>
<body>
    <div class="container">
        <h1>Ürün Ekle</h1>
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Ürün Adı</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea id="description" name="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Fiyat</label>
                <input type="number" id="price" name="price" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="image">Ürün Fotoğrafı</label>
                <input type="file" id="image" name="image" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Ürünü Ekle</button>
        </form>
    </div>
</body>
</html>
