@extends('layouts.app')

@section('title', 'Manage Interns - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Manage Interns</h1>
                <p class="dashboard-subtitle">View and manage all interns in your learning platform.</p>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 2rem;">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error" style="margin-bottom: 2rem;">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid" style="grid-template-columns: 1fr 400px; gap: 2rem; margin-bottom: 3rem;">
                <!-- Interns Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 style="margin: 0; color: var(--text-primary);">All Interns ({{ $interns->count() }})</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--border);">
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Name</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Email</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Role</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Joined Date</th>
                                        <th
                                            style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($interns as $intern)
                                        <tr style="border-bottom: 1px solid var(--border-light);">
                                            <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 500;">
                                                {{ $intern->name }}</td>
                                            <td style="padding: 0.75rem; color: var(--text-secondary);">{{ $intern->email }}
                                            </td>
                                            <td style="padding: 0.75rem;">
                                                <span class="status-badge status-{{ strtolower($intern->role) }}">
                                                    {{ ucfirst($intern->role) }}
                                                </span>
                                            </td>
                                            <td style="padding: 0.75rem; color: var(--text-secondary);">
                                                {{ $intern->created_at->format('M d, Y') }}</td>
                                            <td style="padding: 0.75rem;">
                                                <span class="status-badge status-completed">Active</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($interns->isEmpty())
                            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                                <p>No interns found.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Register New Intern -->
                <div class="card">
                    <div class="card-header">
                        <h3 style="margin: 0; color: var(--text-primary);">
                            <i class="fas fa-user-plus" style="margin-right: 0.5rem;"></i>
                            Register New Intern
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            @csrf

                            <div class="form-group">
                                <label for="name"
                                    style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Full
                                    Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius); font-size: 1rem; background: var(--card-bg); color: var(--text-primary);"
                                    placeholder="Enter intern's full name" required>
                                @error('name')
                                    <div style="color: var(--error); font-size: 0.875rem; margin-top: 0.25rem;">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email"
                                    style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Email
                                    Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius); font-size: 1rem; background: var(--card-bg); color: var(--text-primary);"
                                    placeholder="Enter intern's email" required>
                                @error('email')
                                    <div style="color: var(--error); font-size: 0.875rem; margin-top: 0.25rem;">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password"
                                    style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Password</label>
                                <input type="password" id="password" name="password"
                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius); font-size: 1rem; background: var(--card-bg); color: var(--text-primary);"
                                    placeholder="Create a password" required>
                                @error('password')
                                    <div style="color: var(--error); font-size: 0.875rem; margin-top: 0.25rem;">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation"
                                    style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Confirm
                                    Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius); font-size: 1rem; background: var(--card-bg); color: var(--text-primary);"
                                    placeholder="Confirm the password" required>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                                <i class="fas fa-user-plus"></i> Register Intern
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection