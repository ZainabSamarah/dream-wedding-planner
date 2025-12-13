<?php
// logout.php - تسجيل الخروج

session_start();
session_unset();
session_destroy();

header('Location: login.html');
exit;
?>