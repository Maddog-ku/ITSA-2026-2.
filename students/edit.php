<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
$pageTitle = '編輯學生資料';
$bodyClass = 'form-page';

$studentId = $_GET['id'] ?? '';
if ($studentId === '') {
    header('Location: list.php?err=' . urlencode('缺少學生學號'));
    exit;
}

$stmt = $pdo->prepare('SELECT 學號, 姓名, 年級, 科系代碼 FROM Student WHERE 學號 = :id');
$stmt->execute([':id' => $studentId]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: list.php?err=' . urlencode('找不到學生資料'));
    exit;
}

$deptStmt = $pdo->query('SELECT 科系代碼, 科系名稱 FROM Department ORDER BY 科系代碼');
$departments = $deptStmt->fetchAll();

$errors = [];
$name = $student['姓名'];
$grade = $student['年級'];
$deptCode = $student['科系代碼'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $grade = trim($_POST['grade'] ?? '');
    $deptCode = trim($_POST['dept'] ?? '');

    if ($name === '' || $grade === '' || $deptCode === '') {
        $errors[] = '請填寫完整資料。';
    }

    if (!$errors) {
        $update = $pdo->prepare('UPDATE Student SET 姓名 = :name, 年級 = :grade, 科系代碼 = :dept WHERE 學號 = :id');
        $update->execute([
            ':name' => $name,
            ':grade' => $grade,
            ':dept' => $deptCode,
            ':id' => $studentId,
        ]);
        header('Location: list.php?msg=' . urlencode('更新學生成功'));
        exit;
    }
}

include __DIR__ . '/../header.php';
?>

<?php if ($errors): ?>
    <div class="alert error"><?php echo h(implode(' ', $errors)); ?></div>
<?php endif; ?>

<div class="form-card">
    <div class="form-title">編輯學生資料</div>
    <form method="post" class="form-grid">
        <div class="form-row inline">
            <label>學號：</label>
            <input type="text" value="<?php echo h($studentId); ?>" readonly>
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
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo h($dept['科系代碼']); ?>" <?php echo $deptCode === $dept['科系代碼'] ? 'selected' : ''; ?>>
                        <?php echo h($dept['科系名稱']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions">
            <button class="btn primary" type="submit">更新學生資料</button>
            <a class="btn secondary" href="list.php">返回學生列表</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
