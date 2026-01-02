@extends('layouts.app')

@section('title', 'Video Learning - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">{{ $video->title }}</h1>
                <p class="dashboard-subtitle">{{ $video->description ?? 'Continue your learning journey' }}</p>
            </div>

            <!-- Video Player Section -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <div style="max-width: 900px; margin: 0 auto;">
                        <div
                            style="position: relative; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-xl); background: #000;">
                            <video id="videoElement" controls controlsList="nodownload"
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
                                        {{ round(($progress->watched_duration / $video->duration) * 100, 1) }}%
                                    @else
                                        {{ $progress->watched_duration }}s watched
                                    @endif
                                </span>
                            </div> 
                             <div class="progress-bar">
                                <div class="progress-fill" id="progress-fill"
                                    style="width: {{ $video->duration > 0 ? min(100, ($progress->watched_duration / $video->duration) * 100) : 0 }}%;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completion Status -->
                    @if($progress->is_completed)
                        <div class="alert alert-success" style="margin-top: 1.5rem;">
                            <i class="fas fa-check-circle"></i>
                            <strong>Lesson Completed!</strong> Congratulations on finishing this video.
                        </div>
                    @else
                        <div class="alert alert-info" style="margin-top: 1.5rem;">
                            <i class="fas fa-info-circle"></i>
                            <strong>Keep Watching!</strong> Continue from where you left off to complete this lesson.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div style="text-align: center;">
            <a href="{{ route('intern.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    </div>
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
                  if (data.completed && !{{ $progress->is_completed ? 'true' : 'false' }} && !completionAlertShown) {
                      completionAlertShown = true;
                      alert("Video completed successfully!");
                      location.reload();
                  }
              })
              .catch(error => console.error('Progress save failed:', error));
        }

        // Track watch progress
        video.addEventListener('timeupdate', function () {
            const currentTime = video.currentTime;
            lastWatchedTime = Math.max(lastWatchedTime, currentTime);

            // Save progress periodically
            saveProgress(currentTime);

            // Check for completion only if not already completed and not already triggered
            const isAlreadyCompleted = {{ $progress->is_completed ? 'true' : 'false' }};
            if (!isAlreadyCompleted && !completionTriggered) {
                const duration = video.duration || {{ $video->duration }};
                if (duration > 0) {
                    const progressPercent = (currentTime / duration) * 100;
                    if (progressPercent >= 95 || currentTime >= duration - 10) {
                        completionTriggered = true;
                        saveProgress(currentTime, true);
                    }
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
        video.addEventListener('loadeddata', function() {
            console.log('Video loaded, duration:', video.duration, 'stored:', {{ $video->duration }});
            if (lastWatchedTime > 0) {
                video.currentTime = lastWatchedTime;
            }
        });
    </script>
@endpush