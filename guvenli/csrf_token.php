<?php
function generateToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function validateToken($token) {
    return $token === $_SESSION['csrf_token'];
}
?>
