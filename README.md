# 學生、課程、成績管理系統

## A. 專案介紹
- 本專案為「學生、課程、成績管理系統」（PHP + MySQL + jQuery）
- 後端採 PDO prepared statements，避免 SQL injection
- 資料庫必須由題目提供的 `init.sql` 建立（不可自行建表）

## B. 資料庫建立（含指令）

### 方法1：phpMyAdmin
1. 建立資料庫 `選課系統`
2. 匯入 `init.sql`

### 方法2：命令列（macOS / Linux / Windows）
以下指令可直接複製貼上（預設使用 `root`，若有密碼請加 `-p`）：

```bash
mysql -u root -e "DROP DATABASE IF EXISTS \`選課系統\`;"
mysql -u root -e "CREATE DATABASE \`選課系統\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root "選課系統" < init.sql
mysql -u root -e "USE \`選課系統\`; SHOW TABLES;"
```

> 備註：以上指令假設 `init.sql` 與本 README 同一層；若 `init.sql` 在其他位置，請自行調整路徑。

## C. 設定連線（db.php）
請打開 `db.php`，確認以下設定：
- `DB_HOST`：資料庫主機（預設 `localhost`）
- `DB_USER`：帳號（預設 `root`）
- `DB_PASS`：密碼（預設空字串）
- `DB_NAME`：資料庫名稱（預設 `選課系統`）

## D. 啟動方式（重點）
- macOS：雙擊 `run.command`
- Windows：雙擊 `run.bat`
- 手動啟動：在專案根目錄執行 `php -S localhost:8000 -t .`，再用瀏覽器開 `http://localhost:8000/`

注意：必須透過 Web Server 執行 PHP，不能用 Finder 直接打開 `.php` 或 `.html` 檔案。
若 macOS 無法直接雙擊執行，可先在終端機執行 `chmod +x run.command`。

## E. 系統操作流程（助教驗收順序）
1. 首頁：三個入口（學生管理 / 課程管理 / 成績管理）
2. 學生管理：列表、CRUD、依科系篩選
3. 課程管理：列表（選課人數/平均成績）、點選人數查看學生名單、CRUD、刪除限制（有選修不可刪）、依科系篩選、課程關鍵字搜尋
4. 成績管理：列表、CRUD、學生姓名模糊搜尋、授課老師/課程名稱篩選
5. 所有刪除皆有二次確認（confirm）

## F. 常見問題排查
- **php command not found**：請安裝 PHP（macOS 可用 `brew install php`），或在 Windows 安裝 XAMPP/獨立 PHP 並加入 PATH
- **mysql command not found**：請安裝 MySQL 或 XAMPP，並確保 `mysql` 已加入 PATH
- **DB 連線失敗**：檢查 `db.php` 設定、資料庫是否存在、MySQL 服務是否啟動
- **亂碼 / 編碼錯誤**：請確認資料庫為 utf8mb4，`db.php` 的 charset 設為 `utf8mb4`
- **連不到 localhost:8000**：確認 PHP 伺服器是否已啟動、埠號是否被占用（可改 8000 以外埠號）、防火牆是否阻擋
