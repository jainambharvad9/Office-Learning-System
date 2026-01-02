#!/bin/bash
echo "🚀 Office Learning - Production Deployment Script"
echo "================================================="

# Check if we're in the right directory
if [ ! -f "package.json" ] || [ ! -f "artisan" ]; then
    echo "❌ Error: Run this script from the Laravel project root directory"
    exit 1
fi

echo "📦 Installing Node dependencies..."
npm install

echo "🔨 Building production assets..."
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Build failed! Check for errors above."
    exit 1
fi

echo "🧹 Clearing Laravel caches..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

echo "✅ Build complete!"
echo ""
echo "📤 Upload these files to your server:"
echo "   - public/build/ (entire directory)"
echo "   - public/css/auth.css"
echo "   - All files in resources/views/"
echo ""
echo "🔧 On server, run:"
echo "   php artisan cache:clear"
echo "   php artisan view:clear"
echo ""
echo "🎉 Ready for deployment!"