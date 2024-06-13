<?php
session_start();
session_destroy();
header("Location: giris_kayit.php");
exit();
?>
