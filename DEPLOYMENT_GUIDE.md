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

### Nginx Server (nginx.conf)
Add to your server block:
```
server {
    # ... other config ...

    client_max_body_size 200M;

    location ~ \.php$ {
        # ... other php config ...
        fastcgi_param PHP_VALUE "upload_max_filesize=150M \n post_max_size=200M \n memory_limit=256M \n max_execution_time=300 \n max_input_time=300";
    }
}
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

### If uploads still fail:
1. Check PHP limits: `php -r "echo ini_get('upload_max_filesize');"`
2. Verify server config: `phpinfo()` in a test file
3. Check file permissions on upload directory
4. Ensure storage directory is writable: `chmod 755 storage/`

### Common Issues:
- **PostTooLargeException**: post_max_size too small
- **Memory exhausted**: memory_limit too small
- **Upload fails silently**: upload_max_filesize too small

## Easy Deployment Checklist

- [ ] Set PHP limits (150M upload, 200M post, 256M memory)
- [ ] Configure web server (Apache/Nginx)
- [ ] Set proper file permissions
- [ ] Test with large file (100MB+)
- [ ] Monitor server resources during upload