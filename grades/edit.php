<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
$pageTitle = '編輯成績';
$bodyClass = 'form-page';

$studentId = $_GET['sid'] ?? '';
$courseId = $_GET['cid'] ?? '';
if ($studentId === '' || $courseId === '') {
    header('Location: list.php?err=' . urlencode('缺少成績識別資訊'));
    exit;
}

$stmt = $pdo->prepare(
    'SELECT e.學號, s.姓名, e.課程代號, c.課程名稱, e.成績 '
    . 'FROM Enrollment e '
    . 'JOIN Student s ON e.學號 = s.學號 '
    . 'JOIN Course c ON e.課程代號 = c.課程代號 '
    . 'WHERE e.學號 = :sid AND e.課程代號 = :cid'
);
$stmt->execute([':sid' => $studentId, ':cid' => $courseId]);
$enrollment = $stmt->fetch();

if (!$enrollment) {
    header('Location: list.php?err=' . urlencode('找不到成績資料'));
    exit;
}

$errors = [];
$score = $enrollment['成績'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = trim($_POST['score'] ?? '');

    if ($score === '') {
        $errors[] = '請輸入成績。';
    }

    if (!$errors) {
        $update = $pdo->prepare('UPDATE Enrollment SET 成績 = :score WHERE 學號 = :sid AND 課程代號 = :cid');
        $update->execute([
            ':score' => (int)$score,
            ':sid' => $studentId,
            ':cid' => $courseId,
        ]);
        header('Location: list.php?msg=' . urlencode('更新成績成功'));
        exit;
    }
}

include __DIR__ . '/../header.php';
?>

<?php if ($errors): ?>
    <div class="alert error"><?php echo h(implode(' ', $errors)); ?></div>
<?php endif; ?>

<div class="form-card">
    <div class="form-title">編輯成績</div>
    <form method="post" class="form-grid">
        <div class="form-row inline">
            <label>學生：</label>
            <input type="text" value="<?php echo h($enrollment['姓名'] . ' (' . $enrollment['學號'] . ')'); ?>" readonly>
        </div>
        <div class="form-row inline">
            <label>課程：</label>
            <input type="text" value="<?php echo h($enrollment['課程名稱'] . ' (' . $enrollment['課程代號'] . ')'); ?>" readonly>
        </div>
        <div class="form-row inline">
            <label for="score">成績：</label>
            <input type="number" id="score" name="score" value="<?php echo h($score); ?>" min="0" max="100" required>
        </div>
        <div class="form-actions">
            <button class="btn primary" type="submit">更新成績</button>
            <a class="btn secondary" href="list.php">返回成績列表</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
