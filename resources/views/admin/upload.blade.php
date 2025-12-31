@extends('layouts.app')

@section('title', 'Upload Video - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Upload Training Video</h1>
                <p class="dashboard-subtitle">Add new video content to your learning platform.</p>
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

            @if($errors->any())
                <div class="alert alert-error" style="margin-bottom: 2rem;">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Upload Guidelines -->
            <div class="card"
                style="margin-bottom: 2rem; background: linear-gradient(135deg, rgba(124, 58, 237, 0.05), rgba(236, 72, 153, 0.05)); border: 1px solid rgba(124, 58, 237, 0.1);">
                <div class="card-body">
                    <h3 style="margin: 0 0 1rem 0; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-info-circle"></i>
                        Upload Guidelines
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: var(--text-primary);">File Requirements</h4>
                            <ul style="margin: 0; padding-left: 1.5rem; color: var(--text-secondary);">
                                <li>Format: MP4 video files only</li>
                                <li>Maximum size: 150MB</li>
                                <li>HD quality recommended</li>
                            </ul>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: var(--text-primary);">Best Practices</h4>
                            <ul style="margin: 0; padding-left: 1.5rem; color: var(--text-secondary);">
                                <li>Use descriptive titles</li>
                                <li>Upload may take several minutes</li>
                                <li>Do not close page during upload</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Server Status -->
            {{-- <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-server"></i>
                        Server Configuration Status
                    </h3>
                </div>
                <div class="card-body">
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                        <div
                            style="text-align: center; padding: 1rem; background: var(--surface); border-radius: var(--radius);">
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--primary);">
                                {{ ini_get('upload_max_filesize') }}
                            </div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);">Upload Limit</div>
                        </div>
                        <div
                            style="text-align: center; padding: 1rem; background: var(--surface); border-radius: var(--radius);">
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--primary);">
                                {{ ini_get('post_max_size') }}
                            </div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);">Post Limit</div>
                        </div>
                        <div
                            style="text-align: center; padding: 1rem; background: var(--surface); border-radius: var(--radius);">
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--primary);">
                                {{ ini_get('memory_limit') }}
                            </div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);">Memory Limit</div>
                        </div>
                    </div>

                    @if(ini_get('upload_max_filesize') === '150M' && ini_get('post_max_size') === '200M' &&
                    ini_get('memory_limit') === '256M')
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Configuration OK</strong> - Large video uploads are supported!
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Configuration Issue</strong> - Uploads may fail. Use <code>run_server.bat</code> for correct
                        limits.
                    </div>
                    @endif
                </div>
            </div> --}}

            <!-- Upload Form -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Video Upload Form
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.upload.video') }}" enctype="multipart/form-data"
                        id="uploadForm" onsubmit="handleUpload(event)">
                        @csrf

                        <div class="form-group">
                            <label for="title" class="form-label">Video Title <span
                                    style="color: var(--error);">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                class="form-input" placeholder="Enter a descriptive title for the video">
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" rows="4" class="form-input form-textarea"
                                placeholder="Optional description of the video content">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="video" class="form-label">Video File <span
                                    style="color: var(--error);">*</span></label>
                            <input type="file" id="video" name="video" accept=".mp4" required class="form-input">
                            <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                                <i class="fas fa-info-circle"></i> MP4 format only, maximum 150MB
                            </small>
                        </div>

                        <div style="display: flex; gap: 1rem; align-items: center; margin-top: 2rem;">
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="fas fa-upload"></i> Upload Video
                            </button>

                            <div id="uploadProgress" style="display: none; flex: 1;">
                                <div class="progress-bar" style="height: 8px; margin-bottom: 0.5rem;">
                                    <div class="progress-fill" id="progressBar" style="width: 0%;"></div>
                                </div>
                                <small id="progressText" style="color: var(--text-secondary);">Uploading...</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function handleUpload(e) {
            e.preventDefault();

            // Check CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page.');
                return;
            }

            const form = document.getElementById('uploadForm');
            const formData = new FormData(form);
            formData.append('_token', csrfToken.getAttribute('content'));
            const uploadBtn = document.getElementById('uploadBtn');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            // Validate file
            const fileInput = document.getElementById('video');
            if (fileInput.files.length === 0) {
                alert('Please select a video file.');
                return;
            }

            const file = fileInput.files[0];
            const maxSize = 150 * 1024 * 1024; // 150MB

            if (file.size > maxSize) {
                alert('File size exceeds 150MB limit. Please choose a smaller file.');
                return;
            }

            // Show progress
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            uploadProgress.style.display = 'block';

            try {
                // Upload the video
                const response = await new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.withCredentials = true;
                    xhr.onload = () => resolve(xhr);
                    xhr.onerror = () => reject(new Error('Network error'));
                    xhr.send(formData);
                });

                console.log('Response status:', response.status);
                console.log('Response url:', response.responseURL);
                console.log('Response text:', response.responseText);

                if (response.status === 302) {
                    throw new Error('Redirected to login. Please refresh the page and try again.');
                }

                let result;
                try {
                    result = JSON.parse(response.responseText);
                    console.log('Parsed result:', result);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Invalid response from server: ' + response.responseText.substring(0, 200));
                }

                // Check if upload was successful - be more lenient for production
                const isSuccess = response.ok && (result.success === true || (result.success === undefined && result.message && result.message.includes('successfully')));

                if (isSuccess) {
                    console.log('Upload successful, processing duration update...');
                    // Extract video duration and update
                    const duration = await getVideoDuration(file);
                    console.log('Client-side duration:', duration);
                    if (duration > 0 && result.video_id) {
                        console.log('Updating duration on server...');
                        await updateVideoDuration(result.video_id, duration);
                    }

                    // Show success message
                    progressText.textContent = result.message || 'Upload completed!';
                    progressBar.style.width = '100%';
                    progressBar.style.background = 'var(--success)';

                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    console.error('Upload failed - response.ok:', response.ok, 'result.success:', result.success, 'result.message:', result.message, 'full result:', result);
                    let errorMessage = result.message || 'Upload failed';
                    if (result.errors) {
                        errorMessage += ': ' + Object.values(result.errors).flat().join(', ');
                    }
                    throw new Error(errorMessage);
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Upload failed: ' + error.message);
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Video';
                uploadProgress.style.display = 'none';
            }
        }

        function getVideoDuration(file) {
            return new Promise((resolve) => {
                const video = document.createElement('video');
                video.preload = 'metadata';

                video.onloadedmetadata = function () {
                    window.URL.revokeObjectURL(video.src);
                    resolve(Math.floor(video.duration));
                };

                video.onerror = function () {
                    resolve(0);
                };

                video.src = window.URL.createObjectURL(file);
            });
        }

        async function updateVideoDuration(videoId, duration) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const response = await new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route("admin.update.video.duration") }}');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.withCredentials = true;
                    xhr.onload = () => resolve(xhr);
                    xhr.onerror = () => reject(new Error('Network error'));
                    xhr.send(JSON.stringify({
                        video_id: videoId,
                        duration: duration
                    }));
                });

                return JSON.parse(response.responseText);
            } catch (error) {
                console.error('Duration update failed:', error);
            }
        }

        function formatDuration(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return mins + ':' + (secs < 10 ? '0' : '') + secs;
        }

        // File size validation on selection
        document.getElementById('video').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const maxSize = 150 * 1024 * 1024; // 150MB
                if (file.size > maxSize) {
                    alert('File size exceeds 150MB limit. Please choose a smaller file.');
                    e.target.value = '';
                }
            }
        });
    </script>
@endsection