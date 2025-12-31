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

@section('scripts')
    <script>     // Video restrictions and progress tracking     const video = document.querySelector('video');     const progressFill = document.getElementById('progress-fill');     const progressText = document.getElementById('progress-text');     let lastWatchedTime = {{ $progress->watched_duration }};     const csrfToken = '{{ csrf_token() }}';     const videoId = {{ $video->id }};
        // Update duration when metadata loads     video.addEventListener('loadedmetadata', function () {         if ({{ $video->duration }} == 0 && video.duration > 0) {             const xhr = new XMLHttpRequest();             xhr.open('POST', '/update-video-duration');             xhr.setRequestHeader('Content-Type', 'application/json');             xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);             xhr.setRequestHeader('Accept', 'application/json');             xhr.withCredentials = true;             xhr.send(JSON.stringify({                 video_id: videoId,                 duration: Math.floor(video.duration)             }));         }     });
        // Disable skipping     video.addEventListener('seeking', function () {         video.currentTime = lastWatchedTime;     });
        // Prevent mute     video.muted = false;     video.addEventListener('volumechange', function () {         if (video.muted || video.volume === 0) {             video.volume = 1;             video.muted = false;         }     });
        // Track watch progress     video.addEventListener('timeupdate', function () {         lastWatchedTime = video.currentTime;
        const xhr = new XMLHttpRequest(); xhr.open('POST', '/save-progress'); xhr.setRequestHeader('Content-Type', 'application/json'); xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken); xhr.setRequestHeader('Accept', 'application/json'); xhr.withCredentials = true; xhr.send(JSON.stringify({ video_id: videoId, watched_duration: video.currentTime }));
        // Update progress bar and text         const duration = video.duration || 0;         if (duration > 0) {             const progress = (video.currentTime / duration) * 100;             progressFill.style.width = progress + '%';             progressText.textContent = Math.round(progress * 10) / 10 + '%';
        // Check if completed (95% or within 10 seconds)             if (progress >= 95 || video.currentTime >= duration - 10) {                 const xhr2 = new XMLHttpRequest();                 xhr2.open('POST', '/save-progress');                 xhr2.setRequestHeader('Content-Type', 'application/json');                 xhr2.setRequestHeader('X-CSRF-TOKEN', csrfToken);                 xhr2.setRequestHeader('Accept', 'application/json');                 xhr2.withCredentials = true;                 xhr2.onload = function() {                     const result = JSON.parse(xhr2.responseText);                     if (result.completed && !{{ $progress->is_completed ? 'true' : 'false' }}) {                         alert("Video completed successfully!");                         location.reload();                     }                 };                 xhr2.send(JSON.stringify({ video_id: videoId, watched_duration: video.currentTime, completed: true }));             }         } else {             // Show time watched when duration is unknown             progressFill.style.width = '0%';             progressText.textContent = Math.floor(video.currentTime) + 's watched';         }     });
        // Mark video completed     video.addEventListener('ended', function () {         console.log('Video ended');         // Send final progress update with completion flag         const xhr = new XMLHttpRequest();         xhr.open('POST', '/save-progress');         xhr.setRequestHeader('Content-Type', 'application/json');         xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);         xhr.setRequestHeader('Accept', 'application/json');         xhr.withCredentials = true;         xhr.onload = function() {             const data = JSON.parse(xhr.responseText);             console.log('Completion response:', data);             alert("Video completed successfully! Completed: " + data.completed);             location.reload(); // Refresh to show completion         };         xhr.onerror = function() {             console.error('Error completing video');             alert("Error completing video");         };         xhr.send(JSON.stringify({             video_id: videoId,             watched_duration: video.currentTime,             completed: true         }));     });
    </script>
@endsection