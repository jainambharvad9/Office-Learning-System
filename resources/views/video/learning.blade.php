@extends('layouts.app')

@section('title', 'Video Learning - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                    <h1 class="dashboard-title">{{ $video->title }}</h1>
                    <span style="background: var(--primary); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                        Part {{ $video->part_number }}
                    </span>
                </div>
                <p class="dashboard-subtitle">
                    <i class="fas fa-folder"></i> {{ $video->category ? $video->category->name : 'Uncategorized' }} •
                    {{ $video->description ?? 'Continue your learning journey' }}
                </p>
            </div>

            <!-- Video Player Section -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <div style="max-width: 900px; margin: 0 auto;">
                        <div
                            style="position: relative; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-xl); background: #000;">
                            <video id="videoElement" controls controlsList="nodownload noplaybackrate"
                                style="width: 100%; display: block;">
                                <source src="{{ asset('storage/' . $video->video_path) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>

                        <!-- Progress Bar -->
                        <div style="margin-top: 1.5rem;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span style="font-weight: 600; color: var(--text-primary);">Your Progress</span>
                                <span id="progress-text" style="color: var(--text-secondary); font-size: 0.9rem;">
                                    @if($video->duration > 0)
                                        {{ gmdate('i:s', $progress->watched_duration) }} / {{ gmdate('i:s', $video->duration) }}
                                    @else
                                        {{ $progress->watched_duration }}s watched
                                    @endif
                                </span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="progress-fill"
                                    style="width: {{ $video->duration > 0 ? min(100, round(($progress->watched_duration / $video->duration) * 100)) : 0 }}%;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completion Status -->
                    @if($progress->is_completed)
                        <div class="alert alert-success" style="margin-top: 1.5rem;">
                            <i class="fas fa-check-circle"></i>
                            <strong>Lesson Completed!</strong> Congratulations on finishing this video.
                            @if($nextVideo)
                                <div style="margin-top: 1rem;">
                                    <p style="margin-bottom: 0.5rem;">Ready for the next part?</p>
                                    {{-- <a href="{{ route('video.watch', $nextVideo->id) }}" class="btn btn-primary" style="display: inline-block;">
                                        <i class="fas fa-play"></i> Start {{ $nextVideo->title }}
                                    </a> --}}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-info" style="margin-top: 1.5rem;">
                            <i class="fas fa-info-circle"></i>
                            <strong>Keep Watching!</strong> Continue from where you left off to complete this lesson.
                        </div>
                    @endif
                </div>
            </div>
            <!-- Navigation Buttons -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                        @php
                            $prevVideo = null;
                            $nextVideo = null;
                            if ($currentIndex !== false) {
                                if ($currentIndex > 0) {
                                    $prevVideo = $allVideos[$currentIndex - 1];
                                }
                                if ($currentIndex + 1 < $allVideos->count()) {
                                    $nextVideo = $allVideos[$currentIndex + 1];
                                }
                            }
                        @endphp

                        <div style="flex: 1;">
                            @if($prevVideo)
                                <a href="{{ route('video.watch', $prevVideo->id) }}" class="btn btn-outline" style="width: 100%;">
                                    <i class="fas fa-arrow-left"></i> Previous: {{ $prevVideo->title }}
                                </a>
                            @else
                                <button class="btn btn-outline" style="width: 100%; opacity: 0.5; cursor: not-allowed;" disabled>
                                    <i class="fas fa-arrow-left"></i> No Previous Part
                                </button>
                            @endif
                        </div>

                        <div style="flex: 1; text-align: center;">
                            <span style="background: var(--surface); padding: 0.75rem 1.5rem; border-radius: var(--radius); border: 1px solid var(--border);">
                                <strong>Part {{ $video->part_number }} of {{ $allVideos ? $allVideos->count() : 1 }}</strong>
                            </span>
                        </div>

                        <div style="flex: 1;">
                            @if($nextVideo)
                                <a href="{{ route('video.watch', $nextVideo->id) }}" class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-arrow-right"></i> Next: {{ $nextVideo->title }}
                                </a>
                            @else
                                <button class="btn btn-outline" style="width: 100%; opacity: 0.5; cursor: not-allowed;" disabled>
                                    <i class="fas fa-arrow-right"></i> No Next Part
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
                        {{-- <!-- Category Videos Section -->
            @if($allVideos && $allVideos->count() > 1)
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-list"></i>
                        {{ $video->category ? $video->category->name : 'Course' }} Videos
                    </h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                        @foreach($allVideos as $index => $courseVideo)
                            <div class="video-item {{ $courseVideo->id == $video->id ? 'current-video' : '' }}"
                                 style="border: 2px solid {{ $courseVideo->id == $video->id ? 'var(--primary)' : 'var(--border)' }};
                                        border-radius: var(--radius);
                                        padding: 1rem;
                                        background: {{ $courseVideo->id == $video->id ? 'rgba(var(--primary-rgb), 0.05)' : 'var(--surface)' }};
                                        transition: all 0.3s ease;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                                    <div style="background: {{ $courseVideo->id == $video->id ? 'var(--primary)' : 'var(--text-muted)' }};
                                                color: white;
                                                width: 2rem;
                                                height: 2rem;
                                                border-radius: 50%;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                font-weight: 600;
                                                font-size: 0.9rem;">
                                        {{ $courseVideo->part_number }}
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.25rem 0; font-size: 1rem; color: var(--text-primary);">
                                            {{ $courseVideo->title }}
                                        </h4>
                                        <small style="color: var(--text-secondary);">
                                            @if($courseVideo->duration > 0)
                                                {{ gmdate('i:s', $courseVideo->duration) }}
                                            @else
                                                Duration unknown
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                @php
                                    $videoProgress = $courseVideo->progress->where('user_id', auth()->id())->first();
                                @endphp

                                @if($courseVideo->id == $video->id)
                                    <div style="background: var(--primary); color: white; padding: 0.5rem; border-radius: var(--radius); text-align: center; font-weight: 600;">
                                        <i class="fas fa-play-circle"></i> Currently Watching
                                    </div>
                                @elseif($videoProgress && $videoProgress->is_completed)
                                    <div style="background: var(--success); color: white; padding: 0.5rem; border-radius: var(--radius); text-align: center;">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </div>
                                @else
                                    <a href="{{ route('video.watch', $courseVideo->id) }}"
                                       class="btn btn-primary"
                                       style="width: 100%; margin: 0; display: block; text-align: center;">
                                        <i class="fas fa-play"></i> Watch Now
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif --}}
        </div>

        <!-- Navigation -->
        <div style="text-align: center;">
            <a href="{{ route('intern.videos.all') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Videos
            </a>
        </div>
    </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Ensure video controls are visible and properly styled */
        video::-webkit-media-controls-panel {
            background: rgba(0, 0, 0, 0.7) !important;
            border-radius: 0 0 var(--radius) var(--radius) !important;
        }

        video::-webkit-media-controls-play-button,
        video::-webkit-media-controls-current-time-display,
        video::-webkit-media-controls-time-remaining-display,
        video::-webkit-media-controls-timeline,
        video::-webkit-media-controls-volume-slider,
        video::-webkit-media-controls-mute-button,
        video::-webkit-media-controls-fullscreen-button {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Hide download and playback rate controls */
        video::-webkit-media-controls-playback-rate-button,
        video::-webkit-media-controls-download-button {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }

        /* Firefox video controls */
        video::-moz-media-controls-panel {
            background: rgba(0, 0, 0, 0.7) !important;
        }

        /* General video styling */
        #videoElement {
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
        }

        #videoElement:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* Video items styling */
        .video-item {
            transition: all 0.3s ease;
        }

        .video-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .video-item.current-video {
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.2);
        }

        /* Navigation buttons styling */
        .btn-outline {
            background: transparent;
            border: 2px solid var(--border);
            color: var(--text-primary);
        }

        .btn-outline:hover:not(:disabled) {
            background: var(--surface-hover);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-outline:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
@endsection

@push('scripts')
    <script>
        // Video restrictions and progress tracking
        const video = document.querySelector('video');
        const csrfToken = '{{ csrf_token() }}';
        const videoId = {{ $video->id }};
        let lastWatchedTime = {{ $progress->watched_duration }};
        let lastSavedTime = 0;
        const saveInterval = 5000; // Save every 5 seconds
        let completionAlertShown = false; // Prevent multiple completion alerts
        let completionTriggered = false; // Prevent multiple completion saves
        
        // Data for part-wise playback
        const nextVideoId = @json($nextVideo?->id);
        const nextVideoUrl = @json($nextVideo ? route('video.watch', $nextVideo->id) : null);

        // Update duration when metadata loads
        video.addEventListener('loadedmetadata', function () {
            if ({{ $video->duration }} == 0 && video.duration > 0) {
                fetch('/update-video-duration', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        video_id: videoId,
                        duration: Math.floor(video.duration)
                    })
                }).catch(error => console.error('Duration update failed:', error));
            }
        });

        // Disable skipping
        video.addEventListener('seeking', function () {
            if (video.currentTime > lastWatchedTime + 5) { // Allow small seeks
                video.currentTime = lastWatchedTime;
            }
        });

        // Prevent mute
        video.muted = false;
        video.addEventListener('volumechange', function () {
            if (video.muted || video.volume === 0) {
                video.volume = 1;
                video.muted = false;
            }
        });

        // Throttled progress saving
        function saveProgress(currentTime, forceComplete = false) {
            const now = Date.now();
            if (!forceComplete && now - lastSavedTime < saveInterval) {
                return; // Too soon
            }
            lastSavedTime = now;

            fetch('/save-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    video_id: videoId,
                    watched_duration: currentTime,
                    completed: forceComplete
                })
            }).then(response => response.json())
                .then(data => {
                    if (data.completed && forceComplete && !completionTriggered) {
                        markVideoAsComplete();
                    }
                })
                .catch(error => console.error('Progress save failed:', error));
        }

        // Mark video complete and trigger auto-play next part
        function markVideoAsComplete() {
            fetch('/video/mark-complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    video_id: videoId
                })
            }).then(response => response.json())
                .then(data => {
                    if (data.success && data.nextVideo) {
                        // Show auto-play notification and redirect after 3 seconds
                        showAutoPlayNotification(data.nextVideo);
                    } else if (data.success && !data.nextVideo) {
                        // No next video, show completion message
                        showCompletionMessage();
                    }
                })
                .catch(error => console.error('Mark complete failed:', error));
        }

        // Show notification before auto-playing next part
        function showAutoPlayNotification(nextVideo) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: linear-gradient(135deg, var(--primary), var(--primary-light));
                color: white;
                padding: 1.5rem;
                border-radius: 12px;
                box-shadow: var(--shadow-xl);
                z-index: 10000;
                max-width: 400px;
                animation: slideIn 0.3s ease-out;
            `;
            
            notification.innerHTML = `
                <div style="margin-bottom: 1rem;">
                    <strong style="font-size: 1.1rem;"><i class="fas fa-check-circle"></i> Video Complete!</strong>
                    <p style="margin: 0.5rem 0 0 0; color: rgba(255, 255, 255, 0.9);">
                        Next part will start in <span id="countdown">3</span> seconds...
                    </p>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="${nextVideo.url}" class="btn btn-light" style="flex: 1; margin: 0; padding: 0.5rem 1rem; font-size: 0.9rem;">
                        <i class="fas fa-play"></i> Start Now
                    </a>
                    <button onclick="this.closest('div').remove()" class="btn btn-outline" style="flex: 1; margin: 0; padding: 0.5rem 1rem; font-size: 0.9rem; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.5); color: white;">
                        <i class="fas fa-times"></i> Skip
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-redirect after 3 seconds
            let countdown = 3;
            const countdownEl = document.getElementById('countdown');
            const interval = setInterval(() => {
                countdown--;
                if (countdownEl) countdownEl.textContent = countdown;
                if (countdown === 0) {
                    clearInterval(interval);
                    window.location.href = nextVideo.url;
                }
            }, 1000);
        }

        // Show completion message when no next video
        function showCompletionMessage() {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: linear-gradient(135deg, var(--success), #34d399);
                color: white;
                padding: 1.5rem;
                border-radius: 12px;
                box-shadow: var(--shadow-xl);
                z-index: 10000;
                max-width: 400px;
            `;
            
            notification.innerHTML = `
                <div>
                    <strong style="font-size: 1.1rem;"><i class="fas fa-trophy"></i> Course Complete!</strong>
                    <p style="margin: 0.5rem 0 0 0;">You have completed all parts of this course!</p>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => notification.remove(), 5000);
        }

        // Helper function to format time
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        // Track watch progress
        video.addEventListener('timeupdate', function () {
            const currentTime = video.currentTime;
            lastWatchedTime = Math.max(lastWatchedTime, currentTime);

            // Save progress periodically
            saveProgress(currentTime);

            // Update progress bar
            const duration = video.duration || {{ $video->duration }};
            if (duration > 0) {
                const progressPercent = (currentTime / duration) * 100;
                const progressFill = document.getElementById('progress-fill');
                const progressText = document.getElementById('progress-text');

                if (progressFill) {
                    progressFill.style.width = Math.min(100, progressPercent) + '%';
                }
                if (progressText) {
                    progressText.textContent = `${formatTime(currentTime)} / ${formatTime(duration)}`;
                }
            }
        });

        // Mark video completed on end
        video.addEventListener('ended', function () {
            if (!completionTriggered) {
                completionTriggered = true;
                saveProgress(video.duration || {{ $video->duration }}, true);
            }
        });

        // Set initial time
        video.addEventListener('loadeddata', function () {
            console.log('Video loaded, duration:', video.duration, 'stored:', {{ $video->duration }});
            if (lastWatchedTime > 0) {
                video.currentTime = lastWatchedTime;
            }
        });

        // Add CSS animation for notification
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(500px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
@endpush