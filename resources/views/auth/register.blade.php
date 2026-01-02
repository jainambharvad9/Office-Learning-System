@extends('layouts.auth')

@section('title', 'Register - Office Learning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="auth-container register-container">
        <div class="logo">
            <h1><i class="fas fa-user-plus"></i> Office Learning</h1>
            <p>Create your account</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">
                    <i class="fas fa-user" style="margin-right: 0.5rem;"></i>
                    Full Name
                </label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter your full name">

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope" style="margin-right: 0.5rem;"></i>
                    Email Address
                </label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email address">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock" style="margin-right: 0.5rem;"></i>
                    Password
                </label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="new-password" placeholder="Create a strong password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm">
                    <i class="fas fa-lock" style="margin-right: 0.5rem;"></i>
                    Confirm Password
                </label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                    autocomplete="new-password" placeholder="Confirm your password">
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-user-plus"></i>
                Create Account
            </button>
        </form>

        <div class="auth-links">
            <p style="color: var(--text-secondary); margin: 0 0 0.5rem 0; font-size: 0.9rem;">
                Already have an account?
                <a href="{{ route('login') }}">Sign in here</a>
            </p>
        </div>
    </div>
@endsection