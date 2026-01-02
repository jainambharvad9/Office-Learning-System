<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Office Learning')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- LMS Styles for CSS Variables -->
    @vite('resources/css/lms.css')
    @stack('styles')
</head>

<body>
    @yield('content')

    <!-- Theme and UI Scripts -->
    <script>
        // Dark Mode Toggle Functionality
        class ThemeManager {
            constructor() {
                this.themeToggle = document.getElementById('theme-toggle');
                this.html = document.documentElement;
                this.currentTheme = localStorage.getItem('theme') || 'light';

                this.init();
            }

            init() {
                this.setTheme(this.currentTheme);
                if (this.themeToggle) {
                    this.themeToggle.addEventListener('click', () => this.toggleTheme());
                }
            }

            setTheme(theme) {
                this.html.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);

                if (this.themeToggle) {
                    this.themeToggle.innerHTML = theme === 'dark'
                        ? '<i class="fas fa-sun"></i>'
                        : '<i class="fas fa-moon"></i>';
                }
            }

            toggleTheme() {
                const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
                this.setTheme(newTheme);
                this.currentTheme = newTheme;
            }
        }

        // Initialize theme manager when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new ThemeManager();
        });
    </script>
</body>

</html>