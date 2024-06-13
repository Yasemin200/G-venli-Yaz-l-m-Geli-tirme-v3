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
    $target_dir = "uploads/";
    $copy_dir = "uploads/uploads/";

    // Hedef dizinleri kontrol et ve oluştur
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    if (!file_exists($copy_dir)) {
        mkdir($copy_dir, 0777, true);
    }

    // Dosya türü ve boyutu kontrol et
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_file_size = 2 * 1024 * 1024; // 2MB

    if (!in_array($_FILES['image']['type'], $allowed_types)) {
        die('Sadece JPEG, PNG ve GIF dosyalarına izin verilmektedir.');
    }
    if ($_FILES['image']['size'] > $max_file_size) {
        die('Dosya boyutu 2MB\'ı aşmamalıdır.');
    }

    // Dosya adını sanitize et
    $image = basename(preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['image']['name']));
    $target_file = $target_dir . $image;
    $copy_file = $copy_dir . $image;

    // Dosyayı yükle ve kopyasını oluştur
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
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
