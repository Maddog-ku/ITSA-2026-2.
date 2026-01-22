<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
$pageTitle = '新增選課成績';
$bodyClass = 'form-page';

$errors = [];
$studentId = '';
$courseId = '';
$score = '';

$studentStmt = $pdo->query('SELECT 學號, 姓名 FROM Student ORDER BY 學號');
$students = $studentStmt->fetchAll();

$courseStmt = $pdo->query('SELECT 課程代號, 課程名稱 FROM Course ORDER BY 課程代號');
$courses = $courseStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = trim($_POST['student'] ?? '');
    $courseId = trim($_POST['course'] ?? '');
    $score = trim($_POST['score'] ?? '');

    if ($studentId === '' || $courseId === '' || $score === '') {
        $errors[] = '請填寫完整資料。';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO Enrollment (學號, 課程代號, 成績) VALUES (:sid, :cid, :score)');
            $stmt->execute([
                ':sid' => $studentId,
                ':cid' => $courseId,
                ':score' => (int)$score,
            ]);
            header('Location: list.php?msg=' . urlencode('新增成績成功'));
            exit;
        } catch (PDOException $e) {
            $errors[] = '新增失敗，可能該學生已選修此課程。';
        }
    }
}

include __DIR__ . '/../header.php';
?>

<?php if ($errors): ?>
    <div class="alert error"><?php echo h(implode(' ', $errors)); ?></div>
<?php endif; ?>

<div class="form-card">
    <div class="form-title">新增選課成績</div>
    <form method="post" class="form-grid">
        <div class="form-row inline">
            <label for="student">學生：</label>
            <select id="student" name="student" required>
                <option value="">- 請選擇學生 -</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo h($student['學號']); ?>" <?php echo $studentId === $student['學號'] ? 'selected' : ''; ?>>
                        <?php echo h($student['姓名']); ?> (<?php echo h($student['學號']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row inline">
            <label for="course">課程：</label>
            <select id="course" name="course" required>
                <option value="">- 請選擇課程 -</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo h($course['課程代號']); ?>" <?php echo $courseId === $course['課程代號'] ? 'selected' : ''; ?>>
                        <?php echo h($course['課程名稱']); ?> (<?php echo h($course['課程代號']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row inline">
            <label for="score">成績：</label>
            <input type="number" id="score" name="score" value="<?php echo h($score); ?>" min="0" max="100" required>
        </div>
        <div class="form-actions">
            <button class="btn primary" type="submit">新增選課成績</button>
            <a class="btn secondary" href="list.php">返回成績列表</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
