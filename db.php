<?php
// TODO: Rename the folder to match your team number, e.g., team021-2.

$DB_HOST = 'localhost';
$DB_NAME = '選課系統';
$DB_USER = 'root';
$DB_PASS = '';
$DB_CHARSET = 'utf8mb4';

/*
關鍵 SQL 註解
1) 學生列表 + 科系名稱:
   SELECT s.學號, s.姓名, s.年級, d.科系名稱
   FROM Student s LEFT JOIN Department d ON s.科系代碼 = d.科系代碼

2) 學生依科系篩選:
   ... WHERE s.科系代碼 = :dept

3) 課程列表 (含選課人數/平均成績):
   SELECT c.課程代號, c.課程名稱, c.學分數, t.老師姓名,
          COUNT(e.學號) AS 選課人數, AVG(e.成績) AS 平均成績
   FROM Course c
   JOIN Teacher t ON c.老師編號 = t.老師編號
   LEFT JOIN Enrollment e ON c.課程代號 = e.課程代號
   ... GROUP BY c.課程代號

4) 課程名稱關鍵字搜尋:
   ... WHERE c.課程名稱 LIKE :kw

5) 課程刪除前檢查是否有學生選修:
   SELECT COUNT(*) FROM Enrollment WHERE 課程代號 = :course

6) 點選課人數顯示修課學生:
   SELECT s.學號, s.姓名, e.成績
   FROM Enrollment e JOIN Student s ON e.學號 = s.學號
   WHERE e.課程代號 = :course

7) 成績列表/搜尋:
   SELECT e.學號, s.姓名, c.課程名稱, t.老師姓名, e.成績
   FROM Enrollment e
   JOIN Student s ON e.學號 = s.學號
   JOIN Course c ON e.課程代號 = c.課程代號
   JOIN Teacher t ON c.老師編號 = t.老師編號
   WHERE s.姓名 LIKE :name
*/

function get_pdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET;
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function base_prefix(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = trim(dirname($script), '/');
    if ($dir === '') {
        return '';
    }

    $parts = explode('/', $dir);
    $project = basename(__DIR__);
    if ($parts && $parts[0] === $project) {
        array_shift($parts);
    }
    if (!$parts) {
        return '';
    }

    return str_repeat('../', count($parts));
}

function asset_path(string $path): string
{
    return base_prefix() . ltrim($path, '/');
}

function url_path(string $path): string
{
    return base_prefix() . ltrim($path, '/');
}

function render_message(): void
{
    if (!empty($_GET['msg'])) {
        echo '<div class="alert success">' . h($_GET['msg']) . '</div>';
    }
    if (!empty($_GET['err'])) {
        echo '<div class="alert error">' . h($_GET['err']) . '</div>';
    }
}
