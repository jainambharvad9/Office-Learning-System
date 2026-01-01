@extends('layouts.auth')

@section('title', 'Login - Office Learning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="auth-container">
        <div class="logo">
            <h1><i class="fas fa-graduation-cap"></i> Office Learning</h1>
            <p>Sign in to your account</p>
        </div>

        @if($errors->any())
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope" style="margin-right: 0.5rem;"></i>
                    Email Address
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="Enter your email address" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock" style="margin-right: 0.5rem;"></i>
                    Password
                </label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>
        </form>
    </div>
@endsection