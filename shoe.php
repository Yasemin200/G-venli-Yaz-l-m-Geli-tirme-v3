<?php
require_once 'db.php';
session_start();

// Ayakkabı ID'sini al
$shoe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($shoe_id > 0) {
    $stmt = $db->prepare("SELECT name, description, price, image FROM shoes WHERE id = ?");
    $stmt->bindValue(1, $shoe_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $shoe = $result->fetchArray(SQLITE3_ASSOC);
} else {
    echo "Geçersiz ayakkabı ID'si.";
    exit();
}

// Yorumları çek
$comments_stmt = $db->prepare("SELECT c.comment, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.shoe_id = ? ORDER BY c.created_at DESC");
$comments_stmt->bindValue(1, $shoe_id, SQLITE3_INTEGER);
$comments_result = $comments_stmt->execute();

// Yorum ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'] ?? 0;

    if ($user_id > 0 && !empty($comment)) {
        $insert_comment_stmt = $db->prepare("INSERT INTO comments (user_id, shoe_id, comment) VALUES (?, ?, ?)");
        $insert_comment_stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
        $insert_comment_stmt->bindValue(2, $shoe_id, SQLITE3_INTEGER);
        $insert_comment_stmt->bindValue(3, $comment, SQLITE3_TEXT);
        $insert_comment_stmt->execute();
        header("Location: shoe.php?id=" . $shoe_id . "&message=Yorum eklendi"); // Reflected XSS zafiyeti burada
        exit();
    } else {
        echo "Yorum eklenemedi. Lütfen giriş yapın ve yorum alanını boş bırakmayın.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $shoe['name']; ?></title>
    <link rel="stylesheet" href="style_index.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding-top: 70px;
        }
        .navbar {
            background-color: #343a40;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .navbar .site-name a {
            color: #fff;
            text-decoration: none;
        }
        .navbar .user-info span {
            margin-right: 15px;
        }
        .navbar .logout-button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }
        .container {
            padding: 20px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
        }
        .shoe-detail img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .comments-section {
            margin-top: 30px;
        }
        .comment {
            background-color: #e9ecef;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .form-group textarea {
            resize: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="site-name"><a href="index.php">Ayakkabılar</a></div>
        <div class="user-info">
            <span><?php echo $_SESSION['username'] ?? ''; ?></span>
            <a href="logout.php" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <div class="shoe-detail">
            <h1><?php echo $shoe['name']; ?></h1>
            <img src="uploads/<?php echo $shoe['image']; ?>" alt="Ayakkabı Resmi">
            <p><strong>Açıklama:</strong> <?php echo nl2br($shoe['description']); ?></p>
            <p><strong>Fiyat:</strong> <span id="product-price"><?php echo $shoe['price']; ?> TL</span></p>
        </div>

        <div class="comments-section">
            <h2>Yorumlar</h2>
            <?php while ($comment = $comments_result->fetchArray(SQLITE3_ASSOC)): ?>
                <div class="comment">
                    <p><strong><?php echo $comment['username']; ?></strong>: <?php echo $comment['comment']; ?></p> <!-- XSS zafiyeti burada -->
                </div>
            <?php endwhile; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="shoe.php?id=<?php echo $shoe_id; ?>" method="post">
                    <div class="form-group">
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Yorumunuzu yazın..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Yorumu Gönder</button>
                </form>
            <?php else: ?>
                <p>Yorum yapmak için <a href="giris_kayit.php">giriş yapın</a>.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    // Reflected XSS zafiyetinin çıktısı
    if (isset($_GET['message'])) {
        echo "<script>alert('" . $_GET['message'] . "');</script>";
    }
    ?>
</body>
</html>
