<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$courseId = trim($_POST['id'] ?? '');
if ($courseId === '') {
    header('Location: list.php?err=' . urlencode('缺少課程代號'));
    exit;
}

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM Enrollment WHERE 課程代號 = :id');
$countStmt->execute([':id' => $courseId]);
$enrolledCount = (int)$countStmt->fetchColumn();

if ($enrolledCount > 0) {
    header('Location: list.php?err=' . urlencode('此課程已有學生選修，無法刪除'));
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM Course WHERE 課程代號 = :id');
    $stmt->execute([':id' => $courseId]);
    header('Location: list.php?msg=' . urlencode('刪除課程成功'));
    exit;
} catch (PDOException $e) {
    header('Location: list.php?err=' . urlencode('刪除失敗'));
    exit;
}
