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

    <!-- LMS Styles -->
    @vite('resources/css/lms.css')

    @stack('styles')
</head>

<body>
    @include('partials.header')

    <main class="main-content">
        @yield('content')
    </main>

    @include('partials.footer')

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
                this.currentTheme = theme;
                localStorage.setItem('theme', theme);

                if (this.themeToggle) {
                    const moonIcon = this.themeToggle.querySelector('.fa-moon');
                    const sunIcon = this.themeToggle.querySelector('.fa-sun');

                    if (theme === 'dark') {
                        moonIcon.style.display = 'none';
                        sunIcon.style.display = 'inline';
                    } else {
                        moonIcon.style.display = 'inline';
                        sunIcon.style.display = 'none';
                    }
                }
            }

            toggleTheme() {
                const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
                this.setTheme(newTheme);
            }
        }

        // User Menu Toggle
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');

            if (userMenu && dropdown && !userMenu.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Modal functions (placeholders for future implementation)
        function showProfile() {
            alert('Profile feature coming soon!');
        }

        function showSettings() {
            alert('Settings feature coming soon!');
        }

        function showHelp() {
            alert('Help Center coming soon!');
        }

        function showContact() {
            alert('Contact form coming soon!');
        }

        function showFAQ() {
            alert('FAQ section coming soon!');
        }

        function showFeedback() {
            alert('Feedback form coming soon!');
        }

        function showPrivacy() {
            alert('Privacy Policy coming soon!');
        }

        function showTerms() {
            alert('Terms of Service coming soon!');
        }

        function showCookies() {
            alert('Cookie Policy coming soon!');
        }

        function showProgress() {
            alert('Progress tracking coming soon!');
        }

        // Initialize theme manager when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            new ThemeManager();
        });
    </script>

    @stack('scripts')
</body>

</html>