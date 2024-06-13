<?php
$db_dir = __DIR__ . '/sqlite';
if (!file_exists($db_dir)) {
    mkdir($db_dir, 0777, true);
}

try {
    $db = new SQLite3($db_dir . '/ayakkabi_magazasi.db');
} catch (Exception $e) {
    die($e->getMessage());
}

// Veritabanı tablolarını oluştur
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL,
    profile_pic TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS shoes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    price REAL NOT NULL,
    image TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    shoe_id INTEGER,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS carts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    shoe_id INTEGER,
    quantity INTEGER,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS favorites (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    shoe_id INTEGER,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Admin kullanıcısını ekle
$admin_username = 'admin';
$admin_password = 'admin123'; // Güçlü bir şifre kullanmanız önerilir
$admin_role = 'admin';

// Kullanıcı tablosunda admin kullanıcı olup olmadığını kontrol edin
$stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
$stmt->bindValue(1, $admin_username, SQLITE3_TEXT);
$result = $stmt->execute();
$count = $result->fetchArray(SQLITE3_ASSOC)['count'];

if ($count == 0) {
    // Admin kullanıcısı yoksa ekleyin
    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bindValue(1, $admin_username, SQLITE3_TEXT);
    $stmt->bindValue(2, $admin_password, SQLITE3_TEXT);
    $stmt->bindValue(3, $admin_role, SQLITE3_TEXT);
    $stmt->execute();


   
}
?>



