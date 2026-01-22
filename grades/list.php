<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
$pageTitle = '學生選修課程與成績列表';
$bodyClass = 'list-page';

$nameFilter = trim($_GET['student_name'] ?? '');
$teacherFilter = $_GET['teacher'] ?? '';
$courseFilter = $_GET['course'] ?? '';

$teacherStmt = $pdo->query('SELECT 老師編號, 老師姓名 FROM Teacher ORDER BY 老師編號');
$teachers = $teacherStmt->fetchAll();

$courseStmt = $pdo->query('SELECT 課程代號, 課程名稱 FROM Course ORDER BY 課程代號');
$courses = $courseStmt->fetchAll();

$sql = 'SELECT e.學號, s.姓名, c.課程代號, c.課程名稱, t.老師姓名, e.成績 '
    . 'FROM Enrollment e '
    . 'JOIN Student s ON e.學號 = s.學號 '
    . 'JOIN Course c ON e.課程代號 = c.課程代號 '
    . 'JOIN Teacher t ON c.老師編號 = t.老師編號';

$where = [];
$params = [];
if ($nameFilter !== '') {
    $where[] = 's.姓名 LIKE :name';
    $params[':name'] = '%' . $nameFilter . '%';
}
if ($teacherFilter !== '') {
    $where[] = 't.老師編號 = :teacher';
    $params[':teacher'] = $teacherFilter;
}
if ($courseFilter !== '') {
    $where[] = 'c.課程代號 = :course';
    $params[':course'] = $courseFilter;
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY s.學號, c.課程代號';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

include __DIR__ . '/../header.php';
render_message();
?>

<div class="page-card">
    <div class="card-header">
        <div class="section-title">學生選修課程與成績列表</div>
        <form method="get" class="filter-row">
            <label for="student_name">學生姓名：</label>
            <input type="text" id="student_name" name="student_name" value="<?php echo h($nameFilter); ?>" placeholder="模糊搜尋">
            <div class="filter-actions">
                <button class="btn primary" type="submit">查詢</button>
                <a class="link-button" href="list.php">清除條件</a>
            </div>
            <label for="teacher">授課老師：</label>
            <select id="teacher" name="teacher">
                <option value="">- 全部老師 -</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?php echo h($teacher['老師編號']); ?>" <?php echo $teacherFilter === $teacher['老師編號'] ? 'selected' : ''; ?>>
                        <?php echo h($teacher['老師姓名']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="course">課程名稱：</label>
            <select id="course" name="course">
                <option value="">- 全部課程 -</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo h($course['課程代號']); ?>" <?php echo $courseFilter === $course['課程代號'] ? 'selected' : ''; ?>>
                        <?php echo h($course['課程名稱']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <table class="table table-blue table-striped">
        <thead>
            <tr>
                <th>學號</th>
                <th>學生姓名</th>
                <th>課程代號</th>
                <th>課程名稱</th>
                <th>授課老師</th>
                <th>成績</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$rows): ?>
                <tr>
                    <td colspan="7">目前沒有成績資料</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo h($row['學號']); ?></td>
                        <td><?php echo h($row['姓名']); ?></td>
                        <td><?php echo h($row['課程代號']); ?></td>
                        <td><?php echo h($row['課程名稱']); ?></td>
                        <td><?php echo h($row['老師姓名']); ?></td>
                        <td><?php echo h($row['成績']); ?></td>
                        <td>
                            <div class="actions-inline">
                                <a class="action-link" href="edit.php?sid=<?php echo h($row['學號']); ?>&cid=<?php echo h($row['課程代號']); ?>">編輯</a>
                                <span class="action-divider">|</span>
                                <form method="post" action="delete.php" class="js-delete" data-confirm="確認要刪除這筆成績嗎？">
                                    <input type="hidden" name="sid" value="<?php echo h($row['學號']); ?>">
                                    <input type="hidden" name="cid" value="<?php echo h($row['課程代號']); ?>">
                                    <button class="action-link" type="submit">刪除</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="footer-actions">
    <a class="btn success" href="create.php">新增學生選修課程資料</a>
    <a class="btn secondary" href="<?php echo h(url_path('index.php')); ?>">返回首頁</a>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
