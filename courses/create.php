<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
$pageTitle = '新增課程';
$bodyClass = 'form-page';

$errors = [];
$courseId = '';
$courseName = '';
$credits = '';
$teacherId = '';

$teacherStmt = $pdo->query('SELECT 老師編號, 老師姓名 FROM Teacher ORDER BY 老師編號');
$teachers = $teacherStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = trim($_POST['course_id'] ?? '');
    $courseName = trim($_POST['course_name'] ?? '');
    $credits = trim($_POST['credits'] ?? '');
    $teacherId = trim($_POST['teacher'] ?? '');

    if ($courseId === '' || $courseName === '' || $credits === '' || $teacherId === '') {
        $errors[] = '請填寫完整資料。';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO Course (課程代號, 課程名稱, 學分數, 老師編號) VALUES (:id, :name, :credits, :teacher)');
            $stmt->execute([
                ':id' => $courseId,
                ':name' => $courseName,
                ':credits' => (int)$credits,
                ':teacher' => $teacherId,
            ]);
            header('Location: list.php?msg=' . urlencode('新增課程成功'));
            exit;
        } catch (PDOException $e) {
            $errors[] = '新增失敗，請確認課程代號是否重複。';
        }
    }
}

include __DIR__ . '/../header.php';
?>

<?php if ($errors): ?>
    <div class="alert error"><?php echo h(implode(' ', $errors)); ?></div>
<?php endif; ?>

<div class="form-card">
    <div class="form-title">新增課程</div>
    <form method="post" class="form-grid">
        <div class="form-row inline">
            <label for="course_id">課程代號：</label>
            <input type="text" id="course_id" name="course_id" value="<?php echo h($courseId); ?>" required>
        </div>
        <div class="form-row inline">
            <label for="course_name">課程名稱：</label>
            <input type="text" id="course_name" name="course_name" value="<?php echo h($courseName); ?>" required>
        </div>
        <div class="form-row inline">
            <label for="credits">學分數：</label>
            <input type="number" id="credits" name="credits" value="<?php echo h($credits); ?>" min="1" required>
        </div>
        <div class="form-row inline">
            <label for="teacher">授課老師：</label>
            <select id="teacher" name="teacher" required>
                <option value="">- 請選擇 -</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?php echo h($teacher['老師編號']); ?>" <?php echo $teacherId === $teacher['老師編號'] ? 'selected' : ''; ?>>
                        <?php echo h($teacher['老師姓名']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions">
            <button class="btn primary" type="submit">新增課程</button>
            <a class="btn secondary" href="list.php">返回課程列表</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
