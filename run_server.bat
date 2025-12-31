@echo off
echo ========================================
echo   Office Learning LMS - Easy Start
echo ========================================
echo.
echo This script ensures your server starts with the correct
echo PHP configuration for large video uploads.
echo.
echo Current PHP limits in your config:
php -c php.ini -r "echo 'upload_max_filesize: ' . ini_get('upload_max_filesize');"
php -c php.ini -r "echo 'post_max_size: ' . ini_get('post_max_size');"
php -c php.ini -r "echo 'memory_limit: ' . ini_get('memory_limit');"
echo.
echo Starting server...
echo.
echo Server will be available at: http://127.0.0.1:8000
echo Close this window to stop the server
echo.
php -c php.ini -S 127.0.0.1:8000 -t public