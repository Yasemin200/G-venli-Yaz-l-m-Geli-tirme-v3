<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'login') {
        // Giriş işlemi
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Güvenli SQL sorgusu
        $stmt = $db->prepare("SELECT id, role FROM users WHERE username = ? AND password = ?");
        $stmt->bindValue(1, $username, SQLITE3_TEXT);
        $stmt->bindValue(2, $password, SQLITE3_TEXT);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Geçersiz kullanıcı adı veya şifre!";
        }
    } elseif ($action == 'register') {
        // Kayıt işlemi
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Şifreyi hash'lemek daha güvenli olur
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bindValue(1, $username, SQLITE3_TEXT);
        $stmt->bindValue(2, $hashed_password, SQLITE3_TEXT);  // Şifre hash'lenmiş olarak saklanır
        $stmt->bindValue(3, 'user', SQLITE3_TEXT);

        if ($stmt->execute()) {
            $success = "Kayıt başarılı! Giriş yapabilirsiniz.";
        } else {
            $error = "Kayıt başarısız!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login & Signup Forms</title>
    <link rel="stylesheet" href="style_login.css">
</head>
<body>
<section class="forms-section">
  <h1 class="section-title">Login & Signup Forms</h1>
  <div class="forms">
    <div class="form-wrapper is-active">
      <button type="button" class="switcher switcher-login">
        Login
        <span class="underline"></span>
      </button>
      <form class="form form-login" action="giris_kayit.php" method="post">
        <input type="hidden" name="action" value="login">
        <fieldset>
          <legend>Please, enter your username and password for login.</legend>
          <div class="input-block">
            <label for="login-username">Username</label>
            <input id="login-username" type="text" name="username" required>
          </div>
          <div class="input-block">
            <label for="login-password">Password</label>
            <input id="login-password" type="password" name="password" required>
          </div>
        </fieldset>
        <?php if (isset($error) && $action == 'login'): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <button type="submit" class="btn-login">Login</button>
      </form>
    </div>
    <div class="form-wrapper">
      <button type="button" class="switcher switcher-signup">
        Sign Up
        <span class="underline"></span>
      </button>
      <form class="form form-signup" action="giris_kayit.php" method="post">
        <input type="hidden" name="action" value="register">
        <fieldset>
          <legend>Please, enter your username, email, password and password confirmation for sign up.</legend>
          <div class="input-block">
            <label for="signup-username">Username</label>
            <input id="signup-username" type="text" name="username" required>
          </div>
          <div class="input-block">
            <label for="signup-password">Password</label>
            <input id="signup-password" type="password" name="password" required>
          </div>
          <div class="input-block">
            <label for="signup-confirm-password">Confirm Password</label>
            <input id="signup-confirm-password" type="password" name="confirm_password" required>
          </div>
        </fieldset>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error) && $action == 'register'): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <button type="submit" class="btn-signup">Sign Up</button>
      </form>
    </div>
  </div>
</section>
<script src="script_login.js"></script>
</body>
</html>
