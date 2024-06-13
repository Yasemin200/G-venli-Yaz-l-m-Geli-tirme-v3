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

// Favorilere eklenen ürünleri çek
$favorites_query = "SELECT s.id, s.name, s.description, s.price, s.image FROM favorites f JOIN shoes s ON f.shoe_id = s.id WHERE f.user_id = ?";
$stmt = $db->prepare($favorites_query);
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Favorilerim</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style_index.css">
    <link rel="stylesheet" href="navbar.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .header {
            width: 100%;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
        }

        .nav-link {
            color: #343a40 !important;
        }

        .nav-link:hover {
            color: #007bff !important;
        }

        .form-inline .form-control {
            width: auto;
        }

        .container {
            margin-top: 100px;
        }

        .shoe-card {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .shoe-card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
        }

        .card-text {
            font-size: 14px;
            color: #6c757d;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .col-md-3 {
            flex: 0 0 23%;
            max-width: 23%;
            margin-bottom: 20px;
        }

        .footer {
            width: 100%;
            text-align: center;
        }

        .footer .container {
            max-width: 960px;
            margin: auto;
        }

        .footer span {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <ul class="nav-type">
                <li><a href="index.php" class="active">Ana Sayfa</a></li>
                <li><a href="favorilerim.php" class="active1">Favorilerim</a></li>
                <li><a href="sepetim.php" class="active2">Sepetim</a></li>
                <li>
                    <form class="search-form" action="search.php" method="get">
                        <input type="text" name="query" placeholder="Ayakkabı Ara">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </li>
                <li class="nav-item dropdown ml-auto">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo htmlspecialchars($username); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="profile.php">Profilimi Düzenle</a>
                        <a class="dropdown-item" href="logout.php">Çıkış Yap</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <div class="container mt-5">
        <h1>Favorilerim</h1>
        <div class="row">
            <?php while ($shoe = $result->fetchArray(SQLITE3_ASSOC)): ?>
                <div class="col-md-3">
                    <div class="shoe-card">
                        <img src="<?php echo htmlspecialchars($shoe['image']); ?>" alt="Ayakkabı Resmi">
                        <div>
                            <h3 class="card-title"><a href="shoe.php?id=<?php echo $shoe['id']; ?>"><?php echo htmlspecialchars($shoe['name']); ?></a></h3>
                            <p class="card-text"><?php echo htmlspecialchars($shoe['description']); ?></p>
                            <p class="card-text"><strong>Fiyat:</strong> <?php echo htmlspecialchars($shoe['price']); ?> TL</p>
                            <form action="favorilerimden_cikar.php" method="post">
                                <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                                <button type="submit" class="btn btn-danger">Favorilerden Çıkar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
