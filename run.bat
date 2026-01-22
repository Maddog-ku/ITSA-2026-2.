@echo off
cd /d "%~dp0"

where php >nul 2>&1
if errorlevel 1 (
    echo 未偵測到 PHP，請安裝 XAMPP 或 PHP，並將 php 加入 PATH。
    pause
    exit /b 1
)

echo 即將啟動伺服器 http://localhost:8000/
start "" "http://localhost:8000/"
echo 伺服器正在執行，按 Ctrl+C 可結束
php -S localhost:8000 -t .
