<div class="content-item" data-content-id="{{ $contentIndex }}">
    <div class="content-header">
        <span class="fw-semibold"><i class="bi bi-file-earmark"></i> Content {{ $contentIndex }}</span>
        <div>
            <i class="bi bi-chevron-down collapse-icon"></i>
            <button type="button" class="delete-btn ms-2" data-action="delete-content">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <div class="content-body mt-3">
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control"
                    name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][title]"
                    value="{{ $content->title }}"
                    placeholder="Content title" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                <select class="form-select content-type-select"
                    name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][type]"
                    data-module="{{ $moduleIndex }}"
                    data-content="{{ $contentIndex }}" required>
                    <option value="">Choose...</option>
                    <option value="video" {{ $content->type === 'video' ? 'selected' : '' }}>Video</option>
                    <option value="text" {{ $content->type === 'text' ? 'selected' : '' }}>Text</option>
                    <option value="image" {{ $content->type === 'image' ? 'selected' : '' }}>Image</option>
                    <option value="document" {{ $content->type === 'document' ? 'selected' : '' }}>Document</option>
                    <option value="link" {{ $content->type === 'link' ? 'selected' : '' }}>Link</option>
                </select>
            </div>
        </div>

        <div class="content-fields" id="contentFields{{ $moduleIndex }}_{{ $contentIndex }}">
            <!-- content fields will load dynamically -->
            @if($content->type === 'text')
            <div class="mb-2">
                <label class="form-label">Text Content</label>
                <textarea class="form-control"
                    name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][content]"
                    rows="4">{{ $content->content }}</textarea>
            </div>
            @elseif($content->type === 'video')
            <input type="hidden"
                name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][video_file_old]"
                value="{{ $content->video_file }}">

            <div class="mb-2">
                <label class="form-label fw-semibold">Source Type <span class="text-danger">*</span></label>
                <select class="form-select video-source"
                    name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][video_source_type]"
                    data-module="{{ $moduleIndex }}"
                    data-content="{{ $contentIndex }}" required>
                    <option value="">Choose...</option>
                    <option value="youtube" {{ $content->video_source_type === 'youtube' ? 'selected' : '' }}>YouTube</option>
                    <option value="upload" {{ $content->video_source_type === 'upload' ? 'selected' : '' }}>Upload File</option>
                </select>
            </div>

            @if($content->video_source_type === 'youtube')
            <div class="video-url-field">
                <div class="mb-2">
                    <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
                    <input type="url" class="form-control video-url-input"
                        name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][video_url]"
                        value="{{ $content->video_url }}"
                        placeholder="https://www.youtube.com/watch?v=...">
                    <small class="text-muted">Enter YouTube URL (e.g., https://www.youtube.com/watch?v=...)</small>
                </div>
            </div>
            @elseif($content->video_source_type === 'upload')
            <div class="video-upload-field">
                <div class="mb-2">
                    <label class="form-label">Upload Video</label>
                    <input type="file" class="form-control video-file-input"
                        name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][video_file]"
                        accept="video/*">
                    <small class="text-muted">Max: 500MB</small>
                </div>
                @if($content->video_file_url)
                <div class="current-preview mb-2">
                    <p class="text-success mb-2">
                        <i class="bi bi-check-circle"></i> Current: {{ basename($content->video_file) }}
                    </p>
                    <div class="content-preview">
                        <p class="text-muted mb-2">Current Video Preview:</p>
                        <video controls class="w-100 rounded" style="max-height: 250px;">
                            <source src="{{ $content->video_file_url }}" type="video/mp4">
                        </video>
                    </div>
                </div>
                @endif
                <div id="videoPreview{{ $moduleIndex }}_{{ $contentIndex }}"></div>
            </div>
            @endif

            <div class="mb-2">
                <label class="form-label">Duration (MM:SS) <span class="text-danger">*</span></label>
                <input type="text" class="form-control video-length"
                    name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][video_length]"
                    value="{{ $content->video_length }}"
                    placeholder="10:30" required>
            </div>
            @elseif($content->type === 'image')
            <input type="hidden"
                name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][file_old]"
                value="{{ $content->file }}">

            <div class="mb-2">
                <label class="form-label">Upload Image</label>
                <input type="file" class="form-control file-input"
                    name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][file]"
                    accept="image/*"
                    data-preview="imagePreview{{ $moduleIndex }}_{{ $contentIndex }}">
            </div>

            @if($content->file_url)
            <div class="current-preview mb-2">
                <p class="text-success mb-2">
                    <i class="bi bi-check-circle"></i> Current: {{ basename($content->file) }}
                </p>
                <div class="content-preview">
                    <p class="text-muted mb-2">Current Image Preview:</p>
                    <img src="{{ $content->file_url }}" class="img-fluid rounded" style="max-height: 200px;">
                </div>
            </div>
            @endif
            <div id="imagePreview{{ $moduleIndex }}_{{ $contentIndex }}"></div>
            @elseif($content->type === 'document')
            <input type="hidden"
                name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][file_old]"
                value="{{ $content->file }}">

            <div class="mb-2">
                <label class="form-label">Upload Document</label>
                <input type="file" class="form-control file-input"
                    name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][file]"
                    accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                    data-preview="docPreview{{ $moduleIndex }}_{{ $contentIndex }}">
                <small class="text-muted">PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX</small>
            </div>

            @if($content->file_url)
            <div class="current-preview mb-2">
                <p class="text-success mb-2">
                    <i class="bi bi-check-circle"></i> Current: {{ basename($content->file) }}
                </p>
                <div class="content-preview">
                    <a href="{{ $content->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Download Current File
                    </a>
                </div>
            </div>
            @endif
            <div id="docPreview{{ $moduleIndex }}_{{ $contentIndex }}"></div>
            @elseif($content->type === 'link')
            <div class="mb-2">
                <label class="form-label">URL</label>
                <input type="url" class="form-control"
                    name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][link_url]"
                    value="{{ $content->link_url }}"
                    placeholder="https://example.com">
            </div>
            @endif
        </div>
    </div>
</div>