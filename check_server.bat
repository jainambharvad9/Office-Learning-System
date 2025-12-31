@echo off
echo Checking if Office Learning LMS server is running with correct PHP limits...
echo.

REM Check if server is running on port 8000
netstat -an | find "8000" >nul 2>&1
if %errorlevel% neq 0 (
    echo No server detected on port 8000.
    echo Please start the server using start_server.bat
    goto :end
)

echo Server appears to be running. Testing PHP configuration...
echo.

REM Test the limits by making a request to check PHP info
curl -s http://127.0.0.1:8000/admin/diagnostics >nul 2>&1
if %errorlevel% neq 0 (
    echo Cannot connect to diagnostics page. Server may not be fully started yet.
    goto :end
)

echo If you're still getting upload errors, the server was likely started without custom PHP limits.
echo.
echo SOLUTION: Stop the current server (Ctrl+C) and restart using start_server.bat

:end
echo.
pause