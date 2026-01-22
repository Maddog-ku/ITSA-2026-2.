<?php
require_once __DIR__ . '/db.php';
$pageTitle = '首頁';
$bodyClass = 'home-page';
include __DIR__ . '/header.php';
?>

<div class="home-frame">
    <div class="home-banner">學生、課程與成績管理系統</div>
    <div class="home-actions">
        <a class="home-action" href="<?php echo h(url_path('students/list.php')); ?>">學生管理</a>
        <a class="home-action" href="<?php echo h(url_path('courses/list.php')); ?>">課程管理</a>
        <a class="home-action" href="<?php echo h(url_path('grades/list.php')); ?>">課程成績管理</a>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
