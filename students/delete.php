<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$studentId = trim($_POST['id'] ?? '');
if ($studentId === '') {
    header('Location: list.php?err=' . urlencode('缺少學生學號'));
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM Student WHERE 學號 = :id');
    $stmt->execute([':id' => $studentId]);
    header('Location: list.php?msg=' . urlencode('刪除學生成功'));
    exit;
} catch (PDOException $e) {
    header('Location: list.php?err=' . urlencode('刪除失敗，可能已有選課資料'));
    exit;
}
