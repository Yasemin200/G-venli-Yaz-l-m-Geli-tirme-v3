<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $upload_dir = 'uploads/';
    $uploaded_file = $upload_dir . basename($_FILES['profile_pic']['name']);
    
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploaded_file)) {
        $stmt = $db->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bindValue(1, $uploaded_file, SQLITE3_TEXT);
        $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);
        $stmt->execute();
    }
}

$stmt = $db->prepare("SELECT username, profile_pic FROM users WHERE id = ?");
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
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
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="site-name"><a href="index.php">Ayakkabılarım</a></div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($user['username']); ?></span>
            <a href="index.php?action=logout" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <h2 class="text-center">Profil</h2>
        <?php if (!empty($user['profile_pic'])): ?>
            <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profil Resmi" class="profile-pic">
        <?php endif; ?>
        <form action="profile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile_pic">Profil Resmi Yükle</label>
                <input type="file" class="form-control-file" id="profile_pic" name="profile_pic">
            </div>
            <button type="submit" class="btn btn-primary">Güncelle</button>
        </form>
    </div>
</body>
</html>
