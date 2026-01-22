<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
$pageTitle = '課程列表';
$bodyClass = 'list-page';

$deptFilter = $_GET['dept'] ?? '';
$teacherFilter = $_GET['teacher'] ?? '';
$keyword = trim($_GET['keyword'] ?? '');

$teacherStmt = $pdo->query('SELECT 老師編號, 老師姓名 FROM Teacher ORDER BY 老師編號');
$teachers = $teacherStmt->fetchAll();

$sql = 'SELECT c.課程代號, c.課程名稱, c.學分數, t.老師姓名, '
    . 'COUNT(e.學號) AS 選課人數, AVG(e.成績) AS 平均成績 '
    . 'FROM Course c '
    . 'JOIN Teacher t ON c.老師編號 = t.老師編號 '
    . 'LEFT JOIN Enrollment e ON c.課程代號 = e.課程代號 '
    . 'LEFT JOIN Student s ON e.學號 = s.學號';

$params = [];
$where = [];
if ($deptFilter !== '') {
    $where[] = 's.科系代碼 = :dept';
    $params[':dept'] = $deptFilter;
}
if ($teacherFilter !== '') {
    $where[] = 'c.老師編號 = :teacher';
    $params[':teacher'] = $teacherFilter;
}
if ($keyword !== '') {
    $where[] = 'c.課程名稱 LIKE :kw';
    $params[':kw'] = '%' . $keyword . '%';
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' GROUP BY c.課程代號, c.課程名稱, c.學分數, t.老師姓名 '
    . 'ORDER BY c.課程代號';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

include __DIR__ . '/../header.php';
render_message();
?>

<div class="page-card">
    <div class="card-header">
        <div class="section-title">課程列表</div>
        <form method="get" class="filter-row">
            <label for="teacher">授課老師：</label>
            <select id="teacher" name="teacher">
                <option value="">- 全部老師 -</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?php echo h($teacher['老師編號']); ?>" <?php echo $teacherFilter === $teacher['老師編號'] ? 'selected' : ''; ?>>
                        <?php echo h($teacher['老師姓名']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="keyword">課程關鍵字：</label>
            <input type="text" id="keyword" name="keyword" value="<?php echo h($keyword); ?>" placeholder="例如：資料庫">
            <?php if ($deptFilter !== ''): ?>
                <input type="hidden" name="dept" value="<?php echo h($deptFilter); ?>">
            <?php endif; ?>
            <div class="filter-actions">
                <button class="btn primary" type="submit">查詢</button>
                <a class="link-button" href="list.php">清除</a>
            </div>
        </form>
    </div>

    <table class="table table-blue table-striped">
        <thead>
            <tr>
                <th>課程代號</th>
                <th>課程名稱</th>
                <th>學分</th>
                <th>授課老師</th>
                <th>選課人數</th>
                <th>平均成績</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$courses): ?>
                <tr>
                    <td colspan="7">目前沒有課程資料</td>
                </tr>
            <?php else: ?>
                <?php foreach ($courses as $row): ?>
                    <tr>
                        <td><?php echo h($row['課程代號']); ?></td>
                        <td><?php echo h($row['課程名稱']); ?></td>
                        <td><?php echo h($row['學分數']); ?></td>
                        <td><?php echo h($row['老師姓名']); ?></td>
                        <td>
                            <a class="action-link" href="enrollments.php?course=<?php echo h($row['課程代號']); ?>">
                                <?php echo h((int)$row['選課人數']); ?>
                            </a>
                        </td>
                        <td><?php echo $row['平均成績'] !== null ? h(number_format((float)$row['平均成績'], 1)) : '-'; ?></td>
                        <td>
                            <div class="actions-inline">
                                <a class="action-link" href="edit.php?id=<?php echo h($row['課程代號']); ?>">編輯</a>
                                <span class="action-divider">|</span>
                                <form method="post" action="delete.php" class="js-delete" data-confirm="確認要刪除課程 <?php echo h($row['課程名稱']); ?> 嗎？">
                                    <input type="hidden" name="id" value="<?php echo h($row['課程代號']); ?>">
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
    <a class="btn success" href="create.php">新增課程</a>
    <a class="btn secondary" href="<?php echo h(url_path('index.php')); ?>">返回首頁</a>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
