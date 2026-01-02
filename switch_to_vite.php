<?php
echo "🔄 Switching back to Vite asset loading...\n";
echo "==============================================\n\n";

// Update app layout
$appLayout = 'resources/views/layouts/app.blade.php';
if (file_exists($appLayout)) {
    $content = file_get_contents($appLayout);
    $content = str_replace(
        '    {{-- @vite(\'resources/css/lms.css\') --}}
    <link rel="stylesheet" href="{{ asset(\'css/lms.css?v=\' . time()) }}">',
        '    @vite(\'resources/css/lms.css\')

    {{-- <link rel="stylesheet" href="{{ asset(\'css/lms.css\') }}"> --}}
    @stack(\'styles\')',
        $content
    );
    file_put_contents($appLayout, $content);
    echo "✅ App layout switched to Vite\n";
}

// Update auth layout
$authLayout = 'resources/views/layouts/auth.blade.php';
if (file_exists($authLayout)) {
    $content = file_get_contents($authLayout);
    $content = str_replace(
        '    {{-- @vite(\'resources/css/lms.css\') --}}
    <link rel="stylesheet" href="{{ asset(\'css/lms.css?v=\' . time()) }}">
    @stack(\'styles\')',
        '    @vite(\'resources/css/lms.css\')
    @stack(\'styles\')',
        $content
    );
    file_put_contents($authLayout, $content);
    echo "✅ Auth layout switched to Vite\n";
}

echo "\n📤 Now upload these files to your server:\n";
echo "   - resources/views/layouts/app.blade.php\n";
echo "   - resources/views/layouts/auth.blade.php\n";
echo "\n🔧 Then run on server:\n";
echo "   php artisan cache:clear\n";
echo "   php artisan view:clear\n";
echo "\n🎉 CSS will load properly with Vite!\n";
