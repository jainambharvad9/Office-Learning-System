# Office Learning LMS - Large File Upload Setup

## 🚀 Quick Start
To run the server with large file upload support:

1. **Windows**: Double-click `start_server.bat`
2. **Manual**: Run `php -c php.ini artisan serve --host=127.0.0.1 --port=8000`

## 📁 File Upload Limits
- **Maximum file size**: 150MB
- **Supported formats**: MP4 videos only
- **Memory limit**: 256MB
- **Execution time**: 5 minutes max

## 🔧 Configuration Files Modified

### 1. `php.ini` (Project root)
```ini
upload_max_filesize = 150M
post_max_size = 200M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
```

### 2. `public/.htaccess`
```apache
php_value upload_max_filesize 150M
php_value post_max_size 200M
php_value memory_limit 256M
php_value max_execution_time 300
php_value max_input_time 300
```

### 3. Laravel Controller (`app/Http/Controllers/AdminController.php`)
- Updated validation to accept 150MB files
- Added error handling and user feedback
- File size validation on both client and server side

## 🎯 Upload Process
1. **Admin Login**: Use `admin@example.com` / `password`
2. **Navigate**: Go to "Upload Video" section
3. **Guidelines**: Check upload requirements before selecting file
4. **Select File**: Choose MP4 file (max 150MB)
5. **Upload**: Click upload and wait for completion
6. **Feedback**: Success/error messages will be displayed

## 🐛 Troubleshooting

### "File too large" error
- Ensure you're using the custom `php.ini` file
- Check that Apache/.htaccess limits are applied
- Try the `start_server.bat` script

### Memory exhausted error
- Increase `memory_limit` in `php.ini` if needed
- Ensure sufficient system RAM (4GB+ recommended)

### Upload timeout
- Increase `max_execution_time` and `max_input_time`
- Check network connection stability

## 📊 Performance Tips
- Large files may take 2-5 minutes to upload
- Ensure stable internet connection
- Do not close browser during upload
- Monitor upload progress in the interface

## 🔒 Security Notes
- Only MP4 files are accepted
- Server-side validation prevents malicious uploads
- Files are stored in `storage/app/public/videos/`
- Progress tracking prevents duplicate uploads