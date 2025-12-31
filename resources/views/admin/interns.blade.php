@extends('layouts.app')

@section('title', 'Manage Interns - Office Learning')

@section('content')
<div class="dashboard">
    <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Manage Interns</h1>
                <p class="dashboard-subtitle">View and manage all interns in your learning platform.</p>
            </div>

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
                                        <td style="padding: 0.75rem; color: var(--text-secondary);">{{ $intern->email }}</td>
                                        <td style="padding: 0.75rem;">
                                            <span class="status-badge status-{{ strtolower($intern->role) }}">
                                                {{ ucfirst($intern->role) }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem; color: var(--text-secondary);">
                                            {{ $intern->created_at->format('M d, Y') }}</td>
                                        <td style="padding: 0.75rem;">
                                            <span class="status-badge status-completed">
                                                Active
                                            </span>
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
        </div>
    </div>
@endsection