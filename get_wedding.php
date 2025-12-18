<?php
header('Content-Type: application/json');

$weddingId = $_GET['weddingId'] ?? '';

if (!$weddingId) {
    echo json_encode(['success' => false, 'message' => 'معرف الكارت مفقود']);
    exit;
}

try {
    $db = new PDO("sqlite:wedding.db");
    $stmt = $db->prepare("SELECT * FROM weddings WHERE id = ?");
    $stmt->execute([$weddingId]);
    $wedding = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($wedding) {
        echo json_encode(['success' => true, 'wedding' => $wedding]);
    } else {
        echo json_encode(['success' => false, 'message' => 'الكارت غير موجود']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>