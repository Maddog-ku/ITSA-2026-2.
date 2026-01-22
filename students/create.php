<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
$pageTitle = '新增學生';
$bodyClass = 'form-page';

$errors = [];
$studentId = '';
$name = '';
$grade = '';
$deptCode = '';

$deptStmt = $pdo->query('SELECT 科系代碼, 科系名稱 FROM Department ORDER BY 科系代碼');
$departments = $deptStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = trim($_POST['student_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $grade = trim($_POST['grade'] ?? '');
    $deptCode = trim($_POST['dept'] ?? '');

    if ($studentId === '' || $name === '' || $grade === '' || $deptCode === '') {
        $errors[] = '請填寫完整資料。';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO Student (學號, 姓名, 年級, 科系代碼) VALUES (:id, :name, :grade, :dept)');
            $stmt->execute([
                ':id' => $studentId,
                ':name' => $name,
                ':grade' => $grade,
                ':dept' => $deptCode,
            ]);
            header('Location: list.php?msg=' . urlencode('新增學生成功'));
            exit;
        } catch (PDOException $e) {
            $errors[] = '新增失敗，請確認學號是否重複。';
        }
    }
}

include __DIR__ . '/../header.php';
?>

<?php if ($errors): ?>
    <div class="alert error"><?php echo h(implode(' ', $errors)); ?></div>
<?php endif; ?>

<div class="form-card">
    <div class="form-title">新增學生</div>
    <form method="post" class="form-grid">
        <div class="form-row inline">
            <label for="student_id">學號：</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo h($studentId); ?>" required>
        </div>
        <div class="form-row inline">
            <label for="name">姓名：</label>
            <input type="text" id="name" name="name" value="<?php echo h($name); ?>" required>
        </div>
        <div class="form-row inline">
            <label for="grade">年級：</label>
            <input type="text" id="grade" name="grade" value="<?php echo h($grade); ?>" required>
        </div>
        <div class="form-row inline">
            <label for="dept">科系：</label>
            <select id="dept" name="dept" required>
                <option value="">- 請選擇 -</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo h($dept['科系代碼']); ?>" <?php echo $deptCode === $dept['科系代碼'] ? 'selected' : ''; ?>>
                        <?php echo h($dept['科系名稱']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions">
            <button class="btn primary" type="submit">新增學生</button>
            <a class="btn secondary" href="list.php">返回學生列表</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
