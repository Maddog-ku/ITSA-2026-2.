<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$studentId = trim($_POST['sid'] ?? '');
$courseId = trim($_POST['cid'] ?? '');
if ($studentId === '' || $courseId === '') {
    header('Location: list.php?err=' . urlencode('缺少成績識別資訊'));
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM Enrollment WHERE 學號 = :sid AND 課程代號 = :cid');
    $stmt->execute([
        ':sid' => $studentId,
        ':cid' => $courseId,
    ]);
    header('Location: list.php?msg=' . urlencode('刪除成績成功'));
    exit;
} catch (PDOException $e) {
    header('Location: list.php?err=' . urlencode('刪除失敗'));
    exit;
}
