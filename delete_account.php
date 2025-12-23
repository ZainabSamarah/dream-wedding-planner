<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Optional: Delete related data manually if CASCADE not set
    // $pdo->prepare("DELETE FROM user_food_selections WHERE user_id = ?")->execute([$user_id]);
    // $pdo->prepare("DELETE FROM user_cake_selections WHERE user_id = ?")->execute([$user_id]);
    // $pdo->prepare("DELETE FROM user_card_customizations WHERE user_id = ?")->execute([$user_id]);

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    session_unset();
    session_destroy();

    echo "<script>alert('Account deleted successfully.'); window.location.href = 'index.html';</script>";
} catch (PDOException $e) {
    echo "Error deleting account: " . $e->getMessage();
}
?>