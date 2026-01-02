@echo off
echo 🚀 Office Learning - Production Deployment Script
echo =================================================

REM Check if we're in the right directory
if not exist "package.json" (
    echo ❌ Error: Run this script from the Laravel project root directory
    pause
    exit /b 1
)

if not exist "artisan" (
    echo ❌ Error: Run this script from the Laravel project root directory
    pause
    exit /b 1
)

echo 📦 Installing Node dependencies...
call npm install

if %errorlevel% neq 0 (
    echo ❌ npm install failed!
    pause
    exit /b 1
)

echo 🔨 Building production assets...
call npm run build

if %errorlevel% neq 0 (
    echo ❌ Build failed! Check for errors above.
    pause
    exit /b 1
)

echo 🧹 Clearing Laravel caches...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

echo ✅ Build complete!
echo.
echo 📤 Upload these files to your server:
echo    - public\build\ (entire directory)
echo    - public\css\auth.css
echo    - All files in resources\views\
echo.
echo 🔧 On server, run:
echo    php artisan cache:clear
echo    php artisan view:clear
echo.
echo 🎉 Ready for deployment!
pause