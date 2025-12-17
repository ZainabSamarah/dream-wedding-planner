<?php
global $pdo;
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit();
}

try {
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    session_destroy();
    header("Location: signup.php");
    exit();
} catch (PDOException $e) {
    echo "Error deleting account: " . $e->getMessage();
}
?>
