@extends('layouts.app')

@section('title', 'Quiz Results - ' . $quiz->title)

@section('content')
    <link rel="stylesheet" href="{{ asset('css/lms.css') }}">

    <div class="container" style="padding: 2rem;">
        <!-- Page Header -->
        <div class="results-header" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                        <i class="fas fa-chart-bar" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quiz Results
                    </h1>
                    <p style="color: var(--text-secondary); margin: 0;">{{ $quiz->title }}</p>
                </div>
                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Quizzes
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        {{-- <div class="stats-grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="stat-card"
                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center;">
                <div
                    style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 1.25rem;">
                    <i class="fas fa-users"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ $totalAttempts }}</div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;">Total Attempts</div>
            </div>

            <div class="stat-card"
                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center;">
                <div
                    style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--success), #34D399); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 1.25rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--success);">{{ $passedCount }}</div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;">Passed</div>
            </div>

            <div class="stat-card"
                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center;">
                <div
                    style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--error), #F87171); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 1.25rem;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--error);">{{ $failedCount }}</div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;">Failed</div>
            </div>

            <div class="stat-card"
                style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center;">
                <div
                    style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--warning), #FBBF24); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 1.25rem;">
                    <i class="fas fa-percentage"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--warning);">{{ $averageScore }}%</div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;">Avg Score</div>
            </div>
        </div> --}}

        <!-- Pass Rate Progress Bar -->
        <div class="card"
            style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 2rem;">
            <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                Pass Rate
            </h3>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="flex: 1; height: 12px; background: var(--border); border-radius: 6px; overflow: hidden;">
                    <div
                        style="height: 100%; width: {{ $passRate }}%; background: linear-gradient(90deg, var(--success), #34D399); border-radius: 6px; transition: width 0.5s ease;">
                    </div>
                </div>
                <span style="font-weight: 600; color: var(--success); min-width: 50px;">{{ $passRate }}%</span>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.75rem; margin-bottom: 0;">
                Passing Score Required: <strong>{{ $quiz->passing_score }}%</strong>
            </p>
        </div>

        <!-- Results Table -->
        <div class="card"
            style="background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); background: var(--surface);">
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-primary); margin: 0;">
                    <i class="fas fa-list-alt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                    Intern Results
                </h3>
            </div>

            @if($attempts->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--surface);">
                                <th
                                    style="padding: 1rem 1.5rem; text-align: left; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border);">
                                    #</th>
                                <th
                                    style="padding: 1rem 1.5rem; text-align: left; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border);">
                                    Intern Name</th>
                                <th
                                    style="padding: 1rem 1.5rem; text-align: left; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border);">
                                    Email</th>
                                <th
                                    style="padding: 1rem 1.5rem; text-align: center; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border);">
                                    Score</th>
                                <th
                                    style="padding: 1rem 1.5rem; text-align: center; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border);">
                                    Correct</th>
                                <th
                                    style="padding: 1rem 1.5rem; text-align: center; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border);">
                                    Time Taken</th>
                                <th
                                    style="padding: 1rem 1.5rem; text-align: center; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border);">
                                    Status</th>
                                <th
                                    style="padding: 1rem 1.5rem; text-align: center; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border);">
                                    Date</th>
                                <th
                                    style="padding: 1rem 1.5rem; text-align: center; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid var(--border);">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $index => $attempt)
                                @php
                                    $isPassed = $attempt->percentage >= $quiz->passing_score;
                                @endphp
                                <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s ease;"
                                    onmouseover="this.style.background='var(--surface)'"
                                    onmouseout="this.style.background='transparent'">
                                    <td style="padding: 1rem 1.5rem; color: var(--text-secondary);">{{ $index + 1 }}</td>
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div
                                                style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.85rem;">
                                                {{ strtoupper(substr($attempt->user->name, 0, 1)) }}
                                            </div>
                                            <span
                                                style="font-weight: 500; color: var(--text-primary);">{{ $attempt->user->name }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: 1rem 1.5rem; color: var(--text-secondary);">{{ $attempt->user->email }}</td>
                                    <td style="padding: 1rem 1.5rem; text-align: center;">
                                        <span
                                            style="font-weight: 700; font-size: 1.1rem; color: {{ $isPassed ? 'var(--success)' : 'var(--error)' }};">
                                            {{ round($attempt->percentage) }}%
                                        </span>
                                    </td>
                                    <td style="padding: 1rem 1.5rem; text-align: center; color: var(--text-secondary);">
                                        {{ $attempt->correct_answers }} / {{ $attempt->total_questions }}
                                    </td>
                                    <td style="padding: 1rem 1.5rem; text-align: center; color: var(--text-secondary);">
                                        @if($attempt->time_taken)
                                            @php
                                                $minutes = floor($attempt->time_taken / 60);
                                                $seconds = $attempt->time_taken % 60;
                                            @endphp
                                            {{ $minutes }}m {{ $seconds }}s
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td style="padding: 1rem 1.5rem; text-align: center;">
                                        @if($isPassed)
                                            <span
                                                style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.85rem; background: rgba(16, 185, 129, 0.15); color: var(--success); border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                                <i class="fas fa-check-circle"></i> PASSED
                                            </span>
                                        @else
                                            <span
                                                style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.85rem; background: rgba(239, 68, 68, 0.15); color: var(--error); border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                                <i class="fas fa-times-circle"></i> FAILED
                                            </span>
                                        @endif
                                    </td>
                                    <td
                                        style="padding: 1rem 1.5rem; text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                                        {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : $attempt->created_at->format('M d, Y') }}
                                    </td>
                                    <td style="padding: 1rem 1.5rem; text-align: center;">
                                        <a href="{{ route('admin.quizzes.attempt-detail', $attempt->id) }}" class="btn btn-sm"
                                            style="padding: 0.4rem 0.75rem; font-size: 0.8rem; background: var(--primary); color: white; border-radius: var(--radius-sm); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($attempts->hasPages())
                    <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: center;">
                        {{ $attempts->links() }}
                    </div>
                @endif

            @else
                <div style="padding: 3rem; text-align: center;">
                    <i class="fas fa-clipboard-list"
                        style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--text-secondary); margin-bottom: 0.5rem;">No Attempts Yet</h3>
                    <p style="color: var(--text-muted); margin: 0;">No interns have taken this quiz yet.</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            table th:nth-child(3),
            table td:nth-child(3),
            table th:nth-child(6),
            table td:nth-child(6) {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr !important;
            }

            table th:nth-child(5),
            table td:nth-child(5),
            table th:nth-child(8),
            table td:nth-child(8) {
                display: none;
            }
        }
    </style>
@endsection