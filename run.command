#!/bin/bash
cd "$(dirname "$0")" || exit 1

if ! command -v php >/dev/null 2>&1; then
    echo "未偵測到 PHP。請先安裝 PHP（例如：brew install php）"
    echo "安裝完成後再重新執行。按任意鍵離開..."
    read -n 1 -s
    echo
    exit 1
fi

echo "提示：第一次執行可能需要在終端機執行 chmod +x run.command"
open "http://localhost:8000/"
echo "伺服器正在執行，按 Ctrl+C 可結束"
php -S localhost:8000 -t .
