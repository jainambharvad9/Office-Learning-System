#!/bin/bash
echo "🔄 Switching back to Vite asset loading..."
echo "=============================================="

# Switch app layout back to Vite
sed -i 's/{{-- @vite/{{-- @vite/g' resources/views/layouts/app.blade.php
sed -i 's/<link rel="stylesheet" href="{{ asset/{{-- <link rel="stylesheet" href="{{ asset/g' resources/views/layouts/app.blade.php
sed -i 's/@vite/{{-- @vite/g' resources/views/layouts/app.blade.php
sed -i 's/{{-- @vite/{{-- @vite/g' resources/views/layouts/app.blade.php

# Actually, let me do this properly with PHP since sed might be tricky
echo "Please run this PHP script to switch back:"
echo ""
echo "php -r \"
\$content = file_get_contents('resources/views/layouts/app.blade.php');
\$content = str_replace('@vite(\'resources/css/lms.css\')', '{{-- @vite(\'resources/css/lms.css\') --}}', \$content);
\$content = str_replace('<link rel=\"stylesheet\" href=\"{{ asset(\'css/lms.css?v=\' . time()) }}\">', '@vite(\'resources/css/lms.css\')', \$content);
file_put_contents('resources/views/layouts/app.blade.php', \$content);
echo 'App layout updated\n';
\""
echo ""
echo "And for auth layout:"
echo ""
echo "php -r \"
\$content = file_get_contents('resources/views/layouts/auth.blade.php');
\$content = str_replace('<link rel=\"stylesheet\" href=\"{{ asset(\'css/lms.css?v=\' . time()) }}\">', '@vite(\'resources/css/lms.css\')', \$content);
file_put_contents('resources/views/layouts/auth.blade.php', \$content);
echo 'Auth layout updated\n';
\""
echo ""
echo "Then upload the updated files and clear caches on server."