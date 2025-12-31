@extends('layouts.app')

@section('title', 'Office Learning - Login')

@section('styles')
    <style>
        /* Login specific styles */
        body {
            background: linear-gradient(135deg, var(--background) 0%, #E5E7EB 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo h1 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="login-container">
        <div class="logo">
            <h1>Office Learning</h1>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
        </form>
    </div>
@endsection