@echo off
echo ========================================
echo   Office Learning LMS Server Startup
echo ========================================
echo.
echo Checking PHP configuration...
php -c php.ini -r "echo 'upload_max_filesize: ' . ini_get('upload_max_filesize');"
php -c php.ini -r "echo 'post_max_size: ' . ini_get('post_max_size');"
php -c php.ini -r "echo 'memory_limit: ' . ini_get('memory_limit');"
echo.
echo Starting PHP development server...
echo Server will be available at: http://127.0.0.1:8080
echo.
echo Press Ctrl+C to stop the server
echo.

REM Use PHP built-in server directly for better config control
php -c php.ini -S 127.0.0.1:8080 -t public