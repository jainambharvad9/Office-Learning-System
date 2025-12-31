# Office Learning LMS Server Startup Script
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Office Learning LMS Server Startup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Checking PHP configuration..." -ForegroundColor Yellow
$upload = & php -c php.ini -r "echo ini_get('upload_max_filesize');"
$post = & php -c php.ini -r "echo ini_get('post_max_size');"
$memory = & php -c php.ini -r "echo ini_get('memory_limit');"

Write-Host "upload_max_filesize: $upload" -ForegroundColor Green
Write-Host "post_max_size: $post" -ForegroundColor Green
Write-Host "memory_limit: $memory" -ForegroundColor Green
Write-Host ""

Write-Host "Starting PHP development server..." -ForegroundColor Yellow
Write-Host "Server will be available at: http://127.0.0.1:8000" -ForegroundColor Green
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Red
Write-Host ""

# Start the server
& php -c php.ini -S 127.0.0.1:8000 -t public