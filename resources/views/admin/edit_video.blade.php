@extends('layouts.app')

@section('title', 'Edit Video - Office Learning')

@section('content')
    <div class="dashboard">
        <div class="content-wrapper" style="padding: 0 2rem;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Edit Training Video</h1>
                <p class="dashboard-subtitle">Update video details and content.</p>
            </div>

            <div style="margin-bottom: 2rem;">
                <a href="{{ route('admin.videos') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Videos
                </a>
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

            <!-- Edit Form -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-edit"></i>
                        Edit Video Form
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.videos.update', $video->id) }}"
                        enctype="multipart/form-data" id="uploadForm" onsubmit="handleUpload(event)">
                        @csrf

                        <div class="form-group">
                            <label for="title" class="form-label">Video Title <span
                                    style="color: var(--error);">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title', $video->title) }}" required
                                class="form-input" placeholder="Enter a descriptive title for the video">
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" rows="4" class="form-input form-textarea"
                                placeholder="Optional description of the video content">{{ old('description', $video->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="category_id" class="form-label">Category <span
                                    style="color: var(--error);">*</span></label>
                            <select id="category_id" name="category_id" class="form-input" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $video->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="part_number" class="form-label">Part Number</label>
                            <input type="number" id="part_number" name="part_number"
                                value="{{ old('part_number', $video->part_number) }}" min="1" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="video" class="form-label">Update Video File (Optional)</label>
                            <input type="file" id="video" name="video" accept=".mp4" class="form-input">
                            <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                                <i class="fas fa-info-circle"></i> Leave empty to keep the current video. current:
                                {{ basename($video->video_path) }}
                            </small>
                        </div>

                        <div style="display: flex; gap: 1rem; align-items: center; margin-top: 2rem;">
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="fas fa-save"></i> Save Changes
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
            const fileInput = document.getElementById('video');

            // If no file is selected, let the form submit normally
            if (fileInput.files.length === 0) {
                return; // Default submission
            }

            e.preventDefault();

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const form = document.getElementById('uploadForm');
            const formData = new FormData(form);
            const uploadBtn = document.getElementById('uploadBtn');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            const file = fileInput.files[0];
            const maxSize = 500 * 1024 * 1024; // 500MB

            if (file.size > maxSize) {
                alert('File size exceeds 500MB limit.');
                return;
            }

            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            uploadProgress.style.display = 'block';

            try {
                const response = await new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.timeout = 900000;

                    xhr.upload.onprogress = (e) => {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            progressBar.style.width = percentComplete + '%';
                            progressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                        }
                    };

                    xhr.onload = () => resolve(xhr);
                    xhr.onerror = () => reject(new Error('Network error'));
                    xhr.send(formData);
                });

                const result = JSON.parse(response.responseText);
                if (result.success) {
                    if (fileInput.files.length > 0 && result.video_id) {
                        // If new file was uploaded, trigger background duration processing
                        processVideoDurationAsync(result.video_id);
                        progressText.textContent = "Processing video duration...";
                        setTimeout(() => {
                            window.location.href = "{{ route('admin.videos') }}";
                        }, 2000);
                    } else {
                        window.location.href = "{{ route('admin.videos') }}";
                    }
                } else {
                    alert(result.message || 'Update failed');
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                }
            } catch (error) {
                alert('Error: ' + error.message);
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
            }
        }

        function processVideoDurationAsync(videoId) {
            fetch('/admin/process-video-duration/' + videoId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            }).catch(error => console.error('Duration processing failed:', error));
        }
    </script>

    <style>
        .progress-bar {
            background: var(--surface);
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: width 0.3s ease;
        }

        select.form-input {
            padding: 0.5rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--bg-primary);
            color: var(--text-primary);
            width: 100%;
        }

        html[data-theme="dark"] select.form-input {
            background-color: #1e293b;
            color: #f1f5f9;
        }
    </style>
@endsection