<?php
if (!isset($pageTitle)) {
    $pageTitle = '管理系統';
}
$base = base_prefix();
$bodyClass = $bodyClass ?? '';
?>
<!doctype html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo h($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo h(asset_path('assets/style.css')); ?>">
</head>
<body class="<?php echo h(trim($bodyClass)); ?>">
<header class="site-header">
    <div class="container">
        <div class="brand">
            <div class="brand-title">學生、課程與成績管理系統</div>
            <div class="brand-sub">PHP + MySQL + jQuery</div>
        </div>
        <nav class="main-nav">
            <a href="<?php echo h(url_path('index.php')); ?>">首頁</a>
            <a href="<?php echo h(url_path('students/list.php')); ?>">學生管理</a>
            <a href="<?php echo h(url_path('courses/list.php')); ?>">課程管理</a>
            <a href="<?php echo h(url_path('grades/list.php')); ?>">成績管理</a>
        </nav>
    </div>
</header>
<main class="container">
    <h2><?php echo h($pageTitle); ?></h2>
