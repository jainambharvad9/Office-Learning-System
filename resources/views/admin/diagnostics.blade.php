@extends('layouts.app')

@section('title', 'System Diagnostics - Office Learning')

@section('content')
    <nav class="navbar">
        <h1>Office Learning</h1>
        <div class="navbar-actions">
            <span class="user-info">Welcome, {{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">Logout</button>
            </form>
        </div>
    </nav>
    <div class="flex-container">
        <aside class="sidebar">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="{{ route('admin.interns') }}"><i class="fas fa-users"></i> Interns</a></li>
                <li><a href="{{ route('admin.upload.form') }}"><i class="fas fa-video"></i> Upload Video</a></li>
                <li><a href="{{ route('admin.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="#" class="active"><i class="fas fa-cogs"></i> Diagnostics</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2>System Diagnostics</h2>

            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary); margin-bottom: 1rem;"><i class="fas fa-server"></i> PHP Configuration</h3>
                <div class="grid grid-cols-auto-fit" style="gap: 1rem;">
                    <div class="card" style="background: {{ $phpInfo['upload_ok'] ? '#D1FAE5' : '#FEE2E2' }};">
                        <h4>Upload Max Filesize</h4>
                        <p style="font-size: 1.25rem; font-weight: bold;">{{ $phpInfo['upload_max_filesize'] }}</p>
                        <small>{{ number_format($phpInfo['upload_max_filesize_bytes']) }} bytes</small>
                        <div style="margin-top: 0.5rem;">
                            @if($phpInfo['upload_ok'])
                                <span style="color: #065F46;"><i class="fas fa-check-circle"></i> OK (≥150MB)</span>
                            @else
                                <span style="color: #991B1B;"><i class="fas fa-exclamation-triangle"></i> Too Low</span>
                            @endif
                        </div>
                    </div>

                    <div class="card" style="background: {{ $phpInfo['post_ok'] ? '#D1FAE5' : '#FEE2E2' }};">
                        <h4>Post Max Size</h4>
                        <p style="font-size: 1.25rem; font-weight: bold;">{{ $phpInfo['post_max_size'] }}</p>
                        <small>{{ number_format($phpInfo['post_max_size_bytes']) }} bytes</small>
                        <div style="margin-top: 0.5rem;">
                            @if($phpInfo['post_ok'])
                                <span style="color: #065F46;"><i class="fas fa-check-circle"></i> OK (≥200MB)</span>
                            @else
                                <span style="color: #991B1B;"><i class="fas fa-exclamation-triangle"></i> Too Low</span>
                            @endif
                        </div>
                    </div>

                    <div class="card" style="background: {{ $phpInfo['memory_ok'] ? '#D1FAE5' : '#FEE2E2' }};">
                        <h4>Memory Limit</h4>
                        <p style="font-size: 1.25rem; font-weight: bold;">{{ $phpInfo['memory_limit'] }}</p>
                        <small>{{ number_format($phpInfo['memory_limit_bytes']) }} bytes</small>
                        <div style="margin-top: 0.5rem;">
                            @if($phpInfo['memory_ok'])
                                <span style="color: #065F46;"><i class="fas fa-check-circle"></i> OK (≥256MB)</span>
                            @else
                                <span style="color: #991B1B;"><i class="fas fa-exclamation-triangle"></i> Too Low</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary); margin-bottom: 1rem;"><i class="fas fa-info-circle"></i> Additional
                    Settings</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 0.75rem; font-weight: 600;">Max Execution Time</td>
                            <td style="padding: 0.75rem;">{{ $phpInfo['max_execution_time'] }} seconds</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 0.75rem; font-weight: 600;">Max Input Time</td>
                            <td style="padding: 0.75rem;">{{ $phpInfo['max_input_time'] }} seconds</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 0.75rem; font-weight: 600;">Max File Uploads</td>
                            <td style="padding: 0.75rem;">{{ $phpInfo['max_file_uploads'] }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 0.75rem; font-weight: 600;">Loaded Config File</td>
                            <td style="padding: 0.75rem; word-break: break-all;">{{ $phpInfo['loaded_config'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0.75rem; font-weight: 600;">Server Software</td>
                            <td style="padding: 0.75rem;">{{ $phpInfo['server_software'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if(!$phpInfo['upload_ok'] || !$phpInfo['post_ok'] || !$phpInfo['memory_ok'])
                <div class="card" style="background: #FEF3C7; border: 1px solid #F59E0B;">
                    <h3 style="color: #92400E; margin-bottom: 1rem;"><i class="fas fa-exclamation-triangle"></i> Configuration
                        Issues Detected</h3>
                    <p style="color: #92400E; margin-bottom: 1rem;">
                        Some PHP limits are too low for large video uploads. Please ensure the server is started with the
                        correct configuration.
                    </p>
                    <div style="background: white; padding: 1rem; border-radius: var(--radius); border: 1px solid #E5E7EB;">
                        <strong>How to fix:</strong>
                        <ol style="margin: 0.5rem 0 0 1.5rem; color: #92400E;">
                            <li>Stop the current server (Ctrl+C)</li>
                            <li>Double-click <code>start_server.bat</code> to restart with correct settings</li>
                            <li>Or run: <code>php -c php.ini artisan serve --host=127.0.0.1 --port=8000</code></li>
                        </ol>
                    </div>
                </div>
            @else
                <div class="card" style="background: #D1FAE5; border: 1px solid #10B981;">
                    <h3 style="color: #065F46; margin-bottom: 1rem;"><i class="fas fa-check-circle"></i> Configuration OK</h3>
                    <p style="color: #065F46; margin: 0;">
                        All PHP limits are properly configured for large video uploads. You can now upload files up to 150MB.
                    </p>
                </div>
            @endif
        </main>
    </div>
@endsection