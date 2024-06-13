<?php
session_start();
require_once 'db.php';

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

// Kullanıcı bilgilerini alın
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
$username = $user['username'];
$role = $user['role'];

// Ayakkabı ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $user_id = $_SESSION['user_id'];

    $image_path = '';
    if (!empty($_FILES['image']['name'])) {
        $image_path = 'uploads/' . basename($_FILES['image']['name']);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            echo "Resim yükleme hatası!";
            exit();
        }
    }

    $stmt = $db->prepare("INSERT INTO shoes (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bindValue(1, $name, SQLITE3_TEXT);
    $stmt->bindValue(2, $description, SQLITE3_TEXT);
    $stmt->bindValue(3, $price, SQLITE3_FLOAT);
    $stmt->bindValue(4, $image_path, SQLITE3_TEXT);

    if ($stmt->execute()) {
        echo "Yeni ayakkabı başarıyla eklendi.";
    } else {
        echo "Hata: " . $db->lastErrorMsg();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ayakkabı Ekle</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css">
    <style>
        .container {
            margin-top: 100px;
            background-color: #44475a;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
            color: #fff;
        }
        h2 {
            color: #50fa7b;
        }
        .btn-back {
            margin-bottom: 20px;
        }
        label {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="site-name">Ayakkabılarım</div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="index.php?action=logout" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <h2 class="text-center">Ayakkabı Ekle</h2>
        <a href="admin.php" class="btn btn-secondary btn-back">Geri Dön</a>
        <form action="shoe_add.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">İsim</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Fiyat</label>
                <input type="text" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="image">Resim</label>
                <input type="file" class="form-control-file" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-primary">Ayakkabı Ekle</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrap.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
