<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();

$courseId = $_GET['course'] ?? '';
if ($courseId === '') {
    header('Location: list.php?err=' . urlencode('缺少課程代號'));
    exit;
}

$courseStmt = $pdo->prepare('SELECT c.課程代號, c.課程名稱, t.老師姓名 FROM Course c JOIN Teacher t ON c.老師編號 = t.老師編號 WHERE c.課程代號 = :id');
$courseStmt->execute([':id' => $courseId]);
$course = $courseStmt->fetch();

if (!$course) {
    header('Location: list.php?err=' . urlencode('找不到課程資料'));
    exit;
}

$listStmt = $pdo->prepare('SELECT s.學號, s.姓名, e.成績 FROM Enrollment e JOIN Student s ON e.學號 = s.學號 WHERE e.課程代號 = :id ORDER BY s.學號');
$listStmt->execute([':id' => $courseId]);
$enrollments = $listStmt->fetchAll();

$pageTitle = '選修學生';
$bodyClass = 'list-page';
include __DIR__ . '/../header.php';
?>

<div class="page-card">
    <div class="card-header">
        <div class="section-title">「<?php echo h($course['課程名稱']); ?>」選修學生</div>
        <div class="subtle-text">授課老師：<?php echo h($course['老師姓名']); ?></div>
    </div>

    <table class="table table-blue table-striped">
        <thead>
            <tr>
                <th>學號</th>
                <th>學生姓名</th>
                <th>成績</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$enrollments): ?>
                <tr>
                    <td colspan="3">目前沒有學生選修此課程</td>
                </tr>
            <?php else: ?>
                <?php foreach ($enrollments as $row): ?>
                    <tr>
                        <td><?php echo h($row['學號']); ?></td>
                        <td><?php echo h($row['姓名']); ?></td>
                        <td><?php echo h($row['成績']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="footer-actions">
    <a class="btn secondary" href="list.php">返回課程列表</a>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
