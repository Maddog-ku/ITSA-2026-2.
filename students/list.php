<?php
require_once __DIR__ . '/../db.php';
$pdo = get_pdo();
$pageTitle = '學生列表';
$bodyClass = 'list-page';

$deptFilter = $_GET['dept'] ?? '';

$deptStmt = $pdo->query('SELECT 科系代碼, 科系名稱 FROM Department ORDER BY 科系代碼');
$departments = $deptStmt->fetchAll();

$sql = 'SELECT s.學號, s.姓名, s.年級, s.科系代碼, d.科系名稱 '
    . 'FROM Student s LEFT JOIN Department d ON s.科系代碼 = d.科系代碼';
$params = [];
if ($deptFilter !== '') {
    $sql .= ' WHERE s.科系代碼 = :dept';
    $params[':dept'] = $deptFilter;
}
$sql .= ' ORDER BY s.學號';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

include __DIR__ . '/../header.php';
render_message();
?>

<div class="page-card">
    <div class="card-header">
        <div class="section-title">學生列表</div>
        <form method="get" class="filter-row">
            <label for="dept">依科系篩選：</label>
            <select id="dept" name="dept" class="js-auto-submit">
                <option value="">- 全部科系 -</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo h($dept['科系代碼']); ?>" <?php echo $deptFilter === $dept['科系代碼'] ? 'selected' : ''; ?>>
                        <?php echo h($dept['科系名稱']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <table class="table table-blue table-striped">
        <thead>
            <tr>
                <th>學號</th>
                <th>姓名</th>
                <th>年級</th>
                <th>科系</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$students): ?>
                <tr>
                    <td colspan="5">目前沒有學生資料</td>
                </tr>
            <?php else: ?>
                <?php foreach ($students as $row): ?>
                    <tr>
                        <td><?php echo h($row['學號']); ?></td>
                        <td><?php echo h($row['姓名']); ?></td>
                        <td><?php echo h($row['年級']); ?></td>
                        <td><?php echo h($row['科系名稱'] ?? $row['科系代碼']); ?></td>
                        <td>
                            <div class="actions-inline">
                                <a class="action-link" href="edit.php?id=<?php echo h($row['學號']); ?>">編輯</a>
                                <span class="action-divider">|</span>
                                <form method="post" action="delete.php" class="js-delete" data-confirm="確認要刪除 <?php echo h($row['姓名']); ?> 嗎？">
                                    <input type="hidden" name="id" value="<?php echo h($row['學號']); ?>">
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
    <a class="btn success" href="create.php">新增學生</a>
    <a class="btn secondary" href="<?php echo h(url_path('index.php')); ?>">返回首頁</a>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
