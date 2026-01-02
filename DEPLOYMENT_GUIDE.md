# Office Learning LMS - Deployment Guide
# How to configure large video uploads on any server

## For Local Development (Windows)

### 1. Use the Custom PHP Configuration
- Always start server with: `php -c php.ini artisan serve --host=127.0.0.1 --port=8000`
- Or use: `run_server.bat` (double-click to start)

### 2. PHP Limits Required
```
upload_max_filesize = 150M  # For video files
post_max_size = 200M        # For form data + file
memory_limit = 256M         # For processing
max_execution_time = 300    # For long uploads
max_input_time = 300        # For request processing
```

## For Production Server (Linux/Apache/Nginx)

### Apache Server (.htaccess method)
Add to your `public/.htaccess`:
```
php_value upload_max_filesize 150M
php_value post_max_size 200M
php_value memory_limit 256M
php_value max_execution_time 300
php_value max_input_time 300
```

### Nginx Server (nginx.conf) - UPDATED FOR VIDEO UPLOADS
Add to your server block:
```
server {
    # ... other config ...

    # Increase timeouts for video uploads
    client_max_body_size 200M;
    client_body_timeout 300s;
    client_header_timeout 60s;

    location ~ \.php$ {
        # ... other php config ...
        fastcgi_read_timeout 300s;
        fastcgi_send_timeout 300s;
        fastcgi_connect_timeout 60s;
        fastcgi_param PHP_VALUE "upload_max_filesize=150M \n post_max_size=200M \n memory_limit=256M \n max_execution_time=300 \n max_input_time=300";
    }
}
```

### Alternative: .htaccess for Nginx (if using nginx with htaccess support)
```
php_value upload_max_filesize 150M
php_value post_max_size 200M
php_value memory_limit 256M
php_value max_execution_time 300
php_value max_input_time 300
```

### PHP-FPM (php.ini method)
Edit `/etc/php/8.1/fpm/php.ini` (adjust version):
```
upload_max_filesize = 150M
post_max_size = 200M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
```

## Troubleshooting

### 504 Gateway Timeout (Nginx)
If you get 504 errors during video uploads:
1. **Increase Nginx timeouts** (see nginx.conf section above)
2. **Check PHP-FPM timeouts** in `/etc/php/8.1/fpm/pool.d/www.conf`:
   ```
   request_terminate_timeout = 300s
   ```
3. **Restart services**:
   ```bash
   sudo systemctl restart nginx
   sudo systemctl restart php8.1-fpm
   ```

### If uploads still fail:
1. Check PHP limits: `php -r "echo ini_get('upload_max_filesize');"`
2. Verify server config: `phpinfo()` in a test file
3. Check file permissions on upload directory
4. Ensure storage directory is writable: `chmod 755 storage/`

### Process Video Durations
After deployment, run this command to process existing videos:
```bash
php artisan videos:update-durations
```

For specific video:
```bash
php artisan videos:update-durations --video_id=123
```

## Easy Deployment Checklist

- [ ] Set PHP limits (150M upload, 200M post, 256M memory)
- [ ] Configure web server (Apache/Nginx)
- [ ] Set proper file permissions
- [ ] Test with large file (100MB+)
- [ ] Monitor server resources during upload

---

# 🚨 CSS FIXES FOR LIVE SERVER

## If CSS is Broken on Production

### Step 1: Build Assets Before Deployment
```bash
# Always run this before uploading to live server
npm run build
```

### Step 2: Upload These Files
Make sure these are uploaded to your live server:
- `public/build/` (entire directory with manifest.json and assets/)
- `public/css/auth.css` (for login/register pages)
- All files in `resources/views/`

### Step 3: Clear Caches on Server
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Step 4: Verify Configuration
Check `resources/views/layouts/app.blade.php` contains:
```blade
@vite('resources/css/lms.css')
```

### Step 5: Check Live Server
1. Visit your site
2. Open DevTools (F12) → Network tab
3. CSS should load from `/build/assets/lms-[hash].css`
4. No 404 errors for CSS files

### Emergency Fix (If Still Broken)
Temporarily edit `resources/views/layouts/app.blade.php`:
```blade
{{-- @vite('resources/css/lms.css') --}}
<link rel="stylesheet" href="{{ asset('css/lms.css?v=' . time()) }}">
```

Then rebuild and redeploy.

**Remember**: `npm run build` is required before every production deployment!