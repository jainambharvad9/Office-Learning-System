# Office Learning Management System (LMS) - GST Training Module

A modern, minimal LMS designed for internal GST training with video restrictions to ensure full completion.

## Features

### Admin Features
- **Dashboard**: View total interns, videos, and completion statistics
- **Upload Videos**: Upload GST training videos (MP4 only)
- **Manage Interns**: View all intern accounts
- **Reports**: Detailed progress reports for each intern and video

### Intern Features
- **Dashboard**: View assigned GST modules with progress
- **Video Learning**: Watch training videos with enforced restrictions
- **Progress Tracking**: Automatic progress saving

### Video Restrictions (MOST IMPORTANT)
- ❌ **No Skipping**: Cannot fast-forward or seek ahead
- 🔇 **No Mute**: Cannot mute the video or set volume to 0
- ⏱️ **Progress Tracking**: Saves watch time automatically
- ✅ **Completion Detection**: Marks video as completed when fully watched

## Technical Stack

- **Backend**: Laravel 10
- **Frontend**: Blade Templates + HTML5 + CSS3 + Vanilla JavaScript
- **Database**: MySQL
- **Video Storage**: Local storage (public/uploads/videos)
- **Authentication**: Laravel built-in Auth with role-based middleware

## Database Schema

### users table
- id, name, email, password, role (admin/intern), timestamps

### videos table
- id, title, description, video_path, duration, timestamps

### video_progress table
- id, user_id, video_id, watched_duration, is_completed, timestamps

## Installation

1. Clone the repository
2. Run `composer install`
3. Run `npm install` (if using Vite)
4. Copy `.env.example` to `.env` and configure database
5. Run `php artisan migrate`
6. Run `php artisan db:seed` to create sample users
7. Run `php artisan storage:link`
8. Start the server: `php artisan serve`

## Sample Users

### Admin
- Email: admin@office.com
- Password: password

### Interns
- john@office.com / password
- jane@office.com / password
- bob@office.com / password

## Usage

1. **Login** as admin or intern
2. **Admin**: Upload GST videos, view reports
3. **Intern**: Watch assigned videos with restrictions
4. **Progress** is automatically saved and tracked

## Video Upload

- Only MP4 files allowed
- Maximum size: 100MB
- Stored in `storage/app/public/videos/`

## Security Features

- Role-based access control
- Video playback restrictions
- CSRF protection on progress saving
- Authentication required for all routes

## GST Modules

The system is designed for GST training modules like:
- GSTR-1 Basics
- GSTR-3B Filing
- ITC Rules
- And more...

## Future Enhancements

- Video duration detection
- Bulk video upload
- Quiz integration
- Certificate generation
- Email notifications

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
