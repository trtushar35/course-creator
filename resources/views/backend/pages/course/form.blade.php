@extends('backend.partials.main')

@section('content')

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="mb-2 mb-md-0 fw-bold text-primary">
        <i class="bi bi-{{ isset($course) ? 'pencil-square' : 'plus-circle' }}"></i>
        {{ isset($course) ? 'Edit Course' : 'Create New Course' }}
    </h2>
    <a href="{{ route('backend.courses.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Courses
    </a>
</div>

<!-- Course Form -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form id="courseForm" enctype="multipart/form-data"
                    action="{{ isset($course) ? route('backend.courses.update', $course->id) : route('backend.courses.store') }}">
                    @csrf
                    @if(isset($course)) @method('PUT') @endif

                    <!-- Basic Information Section -->
                    <div class="section-header mb-3">
                        <h5 class="text-primary"><i class="bi bi-info-circle"></i> Basic Information</h5>
                        <hr>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="courseTitle" class="form-label fw-semibold">
                                Course Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="courseTitle" name="title"
                                value="{{ old('title', $course->title ?? '') }}"
                                placeholder="Enter course title" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="coursePrice" class="form-label fw-semibold">
                                Course Price (৳) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="coursePrice" name="price"
                                value="{{ old('price', $course->price ?? '0') }}"
                                placeholder="0.00" step="0.01" min="0" required>
                            <small class="text-muted">Set to ৳0 for free enrollment</small>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label for="courseLevel" class="form-label fw-semibold">Level</label>
                            <select class="form-select" id="courseLevel" name="level">
                                <option value="">Choose...</option>
                                <option value="Beginner" {{ old('level', $course->level ?? '') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="Intermediate" {{ old('level', $course->level ?? '') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="Advanced" {{ old('level', $course->level ?? '') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                <option value="Expert" {{ old('level', $course->level ?? '') == 'Expert' ? 'selected' : '' }}>Expert</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="courseCategory" class="form-label fw-semibold">Category</label>
                            <select class="form-select" id="courseCategory" name="category">
                                <option value="">Choose...</option>
                                @php
                                    $categories = ['Web Development', 'Mobile Development', 'Data Science', 'Design', 'Marketing', 'Business'];
                                @endphp
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $course->category ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="courseStatus" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="courseStatus" name="status">
                                <option value="Active" {{ old('status', $course->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status', $course->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Course Summary with CKEditor -->
                    <div class="mb-4">
                        <label for="courseSummary" class="form-label fw-semibold">Course Summary</label>
                        <textarea class="form-control ckeditor" id="courseSummary" name="summary" rows="8"
                            placeholder="Enter detailed course summary">{{ old('summary', $course->summary ?? '') }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Media Section -->
                    <div class="section-header mb-3 mt-4">
                        <h5 class="text-primary"><i class="bi bi-image"></i> Course Media</h5>
                        <hr>
                    </div>

                    <!-- Feature Image -->
                    <div class="mb-4">
                        <label for="featureImage" class="form-label fw-semibold">Feature Image</label>
                        <input type="file" class="form-control" id="featureImage" name="feature_image" accept="image/*">
                        <small class="text-muted">JPEG, PNG, GIF | Max: 2MB</small>
                        
                        <!-- Current Image Display -->
                        @if(isset($course) && $course->feature_image)
                            <div class="mt-3 current-image-section">
                                <div class="current-file mb-2">
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Current Image: {{ basename($course->feature_image) }}</small>
                                </div>
                                <div class="image-preview-container">
                                    <img src="{{ $course->feature_image_url }}" class="img-fluid rounded" style="max-height: 300px; max-width: 100%;">
                                </div>
                            </div>
                        @endif
                        
                        <!-- New Image Preview -->
                        <div id="newImagePreview" class="mt-3"></div>
                    </div>

                    <!-- Feature Video -->
                    <div class="mb-4">
                        <label for="featureVideo" class="form-label fw-semibold">Feature Video</label>
                        <input type="file" class="form-control" id="featureVideo" name="feature_video" accept="video/*">
                        <small class="text-muted">MP4, MOV, AVI, WMV | Max: 100MB</small>
                        
                        @if(isset($course) && $course->feature_video)
                            <div class="mt-3 video-preview-section">
                                <div class="current-file mb-2">
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Current: {{ basename($course->feature_video) }}</small>
                                </div>
                                <div class="video-preview-container">
                                    <video controls class="w-100 rounded" style="max-height: 300px;">
                                        <source src="{{ $course->feature_video_url }}" type="video/mp4">
                                    </video>
                                </div>
                            </div>
                        @endif
                        <div id="newVideoPreview" class="mt-3"></div>
                    </div>

                    <!-- Course Curriculum Section -->
                    <div class="section-header mb-3 mt-5">
                        <h5 class="text-primary"><i class="bi bi-list-task"></i> Course Curriculum</h5>
                        <hr>
                    </div>

                    <div class="mb-4">
                        <button type="button" class="btn btn-primary" id="addModuleBtn">
                            <i class="bi bi-plus-circle"></i> Add Module
                        </button>
                    </div>

                    <!-- Modules Container -->
                    <div id="modulesContainer">
                        @if(isset($course) && $course->modules->count() > 0)
                            @foreach($course->modules as $moduleIndex => $module)
                                @include('backend.pages.course.partials.module', [
                                    'module' => $module,
                                    'moduleIndex' => $moduleIndex + 1
                                ])
                            @endforeach
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-5 pt-3 border-top">
                        <button type="submit" class="btn btn-success px-4 me-2">
                            <i class="bi bi-save"></i> {{ isset($course) ? 'Update' : 'Save' }} Course
                        </button>
                        <button type="button" class="btn btn-outline-danger px-4" onclick="window.history.back()">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
let editorInstance;

$(document).ready(function() {
    initializeCKEditor();
    initializeImageUpload();
    initializeFeatureVideoPreview();
    
    let moduleCount = {{ isset($course) && $course->modules->count() > 0 ? $course->modules->count() : 0 }};
    window.contentCounts = {};

    @if(isset($course))
        @foreach($course->modules as $moduleIndex => $module)
            window.contentCounts[{{ $moduleIndex + 1 }}] = {{ $module->contents->count() }};
        @endforeach
    @endif

    $('#addModuleBtn').on('click', function() {
        moduleCount++;
        window.contentCounts[moduleCount] = 0;
        const moduleHtml = generateModuleHTML(moduleCount);
        $('#modulesContainer').append(moduleHtml);
        attachModuleEvents(moduleCount);
    });

    // Initialize existing modules and contents
    for (let i = 1; i <= moduleCount; i++) {
        attachModuleEvents(i);
        initializeExistingContentEvents(i);
    }

    $('#courseForm').on('submit', function(e) {
        e.preventDefault();
        if (editorInstance) {
            $('#courseSummary').val(editorInstance.getData());
        }
        if (!validateForm()) return false;
        submitForm(this);
    });
});

function initializeCKEditor() {
    ClassicEditor
        .create(document.querySelector('#courseSummary'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ]
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            }
        })
        .then(editor => {
            editorInstance = editor;
        })
        .catch(error => {
            console.error('CKEditor initialization error:', error);
        });
}

function initializeImageUpload() {
    $('#featureImage').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                showAlert('error', 'Image size must be less than 2MB');
                $(this).val('');
                $('#newImagePreview').empty();
                return;
            }
            
            if (!file.type.startsWith('image/')) {
                showAlert('error', 'Please select a valid image file');
                $(this).val('');
                $('#newImagePreview').empty();
                return;
            }
            
            previewImage(file);
        } else {
            $('#newImagePreview').empty();
        }
    });
}

function previewImage(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        $('#newImagePreview').html(`
            <div class="image-preview-container">
                <p class="text-info mb-2"><i class="bi bi-info-circle"></i> New Image Preview</p>
                <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 300px; max-width: 100%;">
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-danger remove-new-image">
                        <i class="bi bi-trash"></i> Remove Image
                    </button>
                </div>
            </div>
        `);
        
        $('.remove-new-image').on('click', function() {
            $('#featureImage').val('');
            $('#newImagePreview').empty();
        });
    };
    reader.readAsDataURL(file);
}

function initializeFeatureVideoPreview() {
    $('#featureVideo').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 100 * 1024 * 1024) {
                showAlert('error', 'Video size must be less than 100MB');
                $(this).val('');
                return;
            }
            
            const videoURL = URL.createObjectURL(file);
            $('#newVideoPreview').html(`
                <div class="video-preview-section">
                    <p class="text-info mb-2"><i class="bi bi-info-circle"></i> New Video Preview</p>
                    <video controls class="w-100 rounded" style="max-height: 300px;">
                        <source src="${videoURL}" type="${file.type}">
                    </video>
                </div>
            `);
        } else {
            $('#newVideoPreview').empty();
        }
    });
}

function generateModuleHTML(moduleId) {
    return `
        <div class="module-card" data-module-id="${moduleId}">
            <div class="module-header" data-module-id="${moduleId}">
                <span class="fw-bold"><i class="bi bi-collection"></i> Module ${moduleId}</span>
                <div>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                    <button type="button" class="delete-btn ms-2" data-action="delete-module">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="module-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Module Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="modules[${moduleId}][title]" 
                           placeholder="Enter module title" required>
                </div>
                
                <button type="button" class="btn btn-sm btn-primary add-content" data-module-id="${moduleId}">
                    <i class="bi bi-plus"></i> Add Content
                </button>
                
                <div class="contents-container mt-3" id="contentsContainer${moduleId}"></div>
            </div>
        </div>
    `;
}

function generateContentHTML(moduleId, contentId) {
    return `
        <div class="content-item" data-content-id="${contentId}">
            <div class="content-header">
                <span class="fw-semibold"><i class="bi bi-file-earmark"></i> Content ${contentId}</span>
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
                               name="modules[${moduleId}][contents][${contentId}][title]" 
                               placeholder="Content title" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                        <select class="form-select content-type-select" 
                                name="modules[${moduleId}][contents][${contentId}][type]" 
                                data-module="${moduleId}" data-content="${contentId}" required>
                            <option value="">Choose...</option>
                            <option value="video">Video</option>
                            <option value="text">Text</option>
                            <option value="image">Image</option>
                            <option value="document">Document</option>
                            <option value="link">Link</option>
                        </select>
                    </div>
                </div>
                <div class="content-fields" id="contentFields${moduleId}_${contentId}"></div>
            </div>
        </div>
    `;
}

function generateContentFields(moduleId, contentId, type, contentData = {}) {
    const fields = {
        text: `
            <div class="mb-2">
                <label class="form-label">Text Content</label>
                <textarea class="form-control" name="modules[${moduleId}][contents][${contentId}][content]" 
                          rows="4" placeholder="Enter text content">${contentData.content || ''}</textarea>
            </div>
        `,
        video: `
            <input type="hidden" name="modules[${moduleId}][contents][${contentId}][video_file_old]" value="${contentData.video_file_old || ''}">
            <div class="mb-2">
                <label class="form-label fw-semibold">Source Type <span class="text-danger">*</span></label>
                <select class="form-select video-source" data-module="${moduleId}" data-content="${contentId}"
                        name="modules[${moduleId}][contents][${contentId}][video_source_type]" required>
                    <option value="">Choose...</option>
                    <option value="youtube" ${contentData.video_source_type === 'youtube' ? 'selected' : ''}>YouTube</option>
                    <option value="upload" ${contentData.video_source_type === 'upload' ? 'selected' : ''}>Upload File</option>
                </select>
            </div>
            <div class="video-url-field" style="${contentData.video_source_type === 'youtube' ? '' : 'display:none;'}">
                <div class="mb-2">
                    <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
                    <input type="url" class="form-control video-url-input"
                           name="modules[${moduleId}][contents][${contentId}][video_url]"
                           value="${contentData.video_url || ''}"
                           placeholder="https://www.youtube.com/watch?v=...">
                    <small class="text-muted">Enter YouTube URL (e.g., https://www.youtube.com/watch?v=...)</small>
                </div>
            </div>
            <div class="video-upload-field" style="${contentData.video_source_type === 'upload' ? '' : 'display:none;'}">
                <div class="mb-2">
                    <label class="form-label">Upload Video</label>
                    <input type="file" class="form-control video-file-input"
                           name="modules[${moduleId}][contents][${contentId}][video_file]"
                           accept="video/*">
                    <small class="text-muted">Max: 500MB</small>
                </div>
                ${contentData.video_file_url ? `
                <div class="current-preview mb-2">
                    <p class="text-success mb-2">
                        <i class="bi bi-check-circle"></i> Current: ${contentData.video_file_name || 'Video file'}
                    </p>
                    <div class="content-preview">
                        <p class="text-muted mb-2">Current Video Preview:</p>
                        <video controls class="w-100 rounded" style="max-height: 250px;">
                            <source src="${contentData.video_file_url}" type="video/mp4">
                        </video>
                    </div>
                </div>
                ` : ''}
                <div id="videoPreview${moduleId}_${contentId}"></div>
            </div>
            <div class="mb-2">
                <label class="form-label">Duration (MM:SS) <span class="text-danger">*</span></label>
                <input type="text" class="form-control video-length"
                       name="modules[${moduleId}][contents][${contentId}][video_length]"
                       value="${contentData.video_length || ''}"
                       placeholder="10:30" required>
            </div>
        `,
        image: `
            <input type="hidden" name="modules[${moduleId}][contents][${contentId}][file_old]" value="${contentData.file_old || ''}">
            <div class="mb-2">
                <label class="form-label">Upload Image</label>
                <input type="file" class="form-control file-input"
                       name="modules[${moduleId}][contents][${contentId}][file]"
                       accept="image/*" data-preview="imagePreview${moduleId}_${contentId}">
            </div>
            ${contentData.file_url ? `
            <div class="current-preview mb-2">
                <p class="text-success mb-2">
                    <i class="bi bi-check-circle"></i> Current: ${contentData.file_name || 'Image file'}
                </p>
                <div class="content-preview">
                    <p class="text-muted mb-2">Current Image Preview:</p>
                    <img src="${contentData.file_url}" class="img-fluid rounded" style="max-height: 200px;">
                </div>
            </div>
            ` : ''}
            <div id="imagePreview${moduleId}_${contentId}" class="content-preview"></div>
        `,
        document: `
            <input type="hidden" name="modules[${moduleId}][contents][${contentId}][file_old]" value="${contentData.file_old || ''}">
            <div class="mb-2">
                <label class="form-label">Upload Document</label>
                <input type="file" class="form-control file-input"
                       name="modules[${moduleId}][contents][${contentId}][file]"
                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                       data-preview="docPreview${moduleId}_${contentId}">
                <small class="text-muted">PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX</small>
            </div>
            ${contentData.file_url ? `
            <div class="current-preview mb-2">
                <p class="text-success mb-2">
                    <i class="bi bi-check-circle"></i> Current: ${contentData.file_name || 'Document file'}
                </p>
                <div class="content-preview">
                    <a href="${contentData.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Download Current File
                    </a>
                </div>
            </div>
            ` : ''}
            <div id="docPreview${moduleId}_${contentId}" class="content-preview"></div>
        `,
        link: `
            <div class="mb-2">
                <label class="form-label">URL</label>
                <input type="url" class="form-control"
                       name="modules[${moduleId}][contents][${contentId}][link_url]"
                       value="${contentData.link_url || ''}"
                       placeholder="https://example.com">
            </div>
        `
    };
    
    return fields[type] || '';
}

function attachModuleEvents(moduleId) {
    const $module = $(`.module-card[data-module-id="${moduleId}"]`);
    
    $module.find('.module-header').off('click').on('click', function(e) {
        if (!$(e.target).closest('button').length) {
            $(this).siblings('.module-body').slideToggle(300);
            $(this).find('.collapse-icon').toggleClass('collapsed');
        }
    });
    
    $module.find('[data-action="delete-module"]').off('click').on('click', function() {
        if (confirm('Delete this module and all its contents?')) {
            $module.remove();
        }
    });
    
    $module.find('.add-content').off('click').on('click', function() {
        addContent(moduleId);
    });
}

function addContent(moduleId) {
    if (!window.contentCounts[moduleId]) window.contentCounts[moduleId] = 0;
    
    window.contentCounts[moduleId]++;
    const contentId = window.contentCounts[moduleId];
    
    const html = generateContentHTML(moduleId, contentId);
    $(`#contentsContainer${moduleId}`).append(html);
    attachContentEvents(moduleId, contentId);
}

function attachContentEvents(moduleId, contentId) {
    const $content = $(`#contentsContainer${moduleId} .content-item[data-content-id="${contentId}"]`).last();
    
    $content.find('.content-header').off('click').on('click', function(e) {
        if (!$(e.target).closest('button').length) {
            $(this).siblings('.content-body').slideToggle(300);
            $(this).find('.collapse-icon').toggleClass('collapsed');
        }
    });
    
    $content.find('[data-action="delete-content"]').off('click').on('click', function() {
        if (confirm('Delete this content?')) {
            $content.remove();
        }
    });
    
    $content.find('.content-type-select').off('change').on('change', function() {
        handleContentTypeChange(moduleId, contentId, $(this).val());
    });
}

// Initialize existing content events for edit mode
function initializeExistingContentEvents(moduleId) {
    $(`#contentsContainer${moduleId} .content-type-select`).each(function() {
        const $select = $(this);
        const contentId = $select.data('content');
        const currentType = $select.val();
        
        if (currentType) {
            // Get existing content data
            const contentData = getExistingContentData(moduleId, contentId);
            handleContentTypeChange(moduleId, contentId, currentType, contentData);
        }
        
        $select.off('change').on('change', function() {
            handleContentTypeChange(moduleId, contentId, $(this).val());
        });
    });
}

function getExistingContentData(moduleId, contentId) {
    const $content = $(`#contentsContainer${moduleId} .content-item[data-content-id="${contentId}"]`);
    const data = {};
    
    // Get current values from hidden fields
    data.video_file_old = $content.find('input[name*="video_file_old"]').val() || '';
    data.file_old = $content.find('input[name*="file_old"]').val() || '';
    data.video_url = $content.find('input[name*="video_url"]').val() || '';
    data.video_length = $content.find('input[name*="video_length"]').val() || '';
    data.link_url = $content.find('input[name*="link_url"]').val() || '';
    data.content = $content.find('textarea[name*="content"]').val() || '';
    
    // Get video source type
    const videoSourceSelect = $content.find('select[name*="video_source_type"]');
    if (videoSourceSelect.length) {
        data.video_source_type = videoSourceSelect.val() || '';
    }
    
    // Get file URLs from preview sections
    const videoPreview = $content.find('.current-preview video source');
    if (videoPreview.length) {
        data.video_file_url = videoPreview.attr('src') || '';
        data.video_file_name = $content.find('.current-preview .text-success').text().replace('Current: ', '') || '';
    }
    
    const imagePreview = $content.find('.current-preview img');
    if (imagePreview.length) {
        data.file_url = imagePreview.attr('src') || '';
        data.file_name = $content.find('.current-preview .text-success').text().replace('Current: ', '') || '';
    }
    
    const docPreview = $content.find('.current-preview a');
    if (docPreview.length) {
        data.file_url = docPreview.attr('href') || '';
        data.file_name = $content.find('.current-preview .text-success').text().replace('Current: ', '') || '';
    }
    
    return data;
}

function handleContentTypeChange(moduleId, contentId, type, contentData = {}) {
    const $fieldsContainer = $(`#contentFields${moduleId}_${contentId}`);
    $fieldsContainer.html(generateContentFields(moduleId, contentId, type, contentData));
    
    if (type === 'video') {
        attachVideoSourceHandler(moduleId, contentId);
        // Trigger change event to show correct fields based on existing data
        const $videoSource = $(`#contentFields${moduleId}_${contentId} .video-source`);
        if ($videoSource.length && contentData.video_source_type) {
            $videoSource.trigger('change');
        }
    } else if (type === 'image' || type === 'document') {
        attachFilePreviewHandler(moduleId, contentId);
    }
}

function attachVideoSourceHandler(moduleId, contentId) {
    const $videoSource = $(`#contentFields${moduleId}_${contentId} .video-source`);
    
    $videoSource.off('change').on('change', function() {
        const type = $(this).val();
        const $container = $(this).closest('.content-fields');
        const $urlField = $container.find('.video-url-field');
        const $uploadField = $container.find('.video-upload-field');
        const $urlInput = $container.find('.video-url-input');
        const $fileInput = $container.find('.video-file-input');
        
        $urlField.hide();
        $uploadField.hide();
        $urlInput.removeAttr('required');
        $fileInput.removeAttr('required');
        
        if (type === 'youtube') {
            $urlField.show();
            $urlInput.attr('required', 'required');
        } else if (type === 'upload') {
            $uploadField.show();
            attachVideoFilePreview(moduleId, contentId);
        }
    });
}

function attachVideoFilePreview(moduleId, contentId) {
    $(`#contentFields${moduleId}_${contentId} .video-file-input`).off('change').on('change', function() {
        const file = this.files[0];
        if (file && file.type.startsWith('video/')) {
            const videoURL = URL.createObjectURL(file);
            const $previewContainer = $(`#videoPreview${moduleId}_${contentId}`);
            $previewContainer.html(`
                <div class="content-preview mt-2">
                    <p class="text-muted mb-2"><i class="bi bi-play-circle"></i> New Video Preview:</p>
                    <video controls class="w-100 rounded" style="max-height: 250px;">
                        <source src="${videoURL}" type="${file.type}">
                    </video>
                </div>
            `);
        }
    });
}

function attachFilePreviewHandler(moduleId, contentId) {
    $(`#contentFields${moduleId}_${contentId} .file-input`).off('change').on('change', function() {
        const file = this.files[0];
        const previewId = $(this).data('preview');
        
        if (file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(`#${previewId}`).html(`
                        <div class="mt-2">
                            <p class="text-muted mb-2"><i class="bi bi-image"></i> New Image Preview:</p>
                            <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            } else {
                $(`#${previewId}`).html(`
                    <div class="mt-2 alert alert-info">
                        <i class="bi bi-file-earmark"></i> <strong>${file.name}</strong> (${formatFileSize(file.size)})
                    </div>
                `);
            }
        }
    });
}

function validateForm() {
    let isValid = true;
    
    $('.is-invalid').removeClass('is-invalid');
    
    $('#courseForm').find('[required]').each(function() {
        if (!$(this).val() || $(this).val().trim() === '') {
            $(this).addClass('is-invalid');
            isValid = false;
        }
    });
    
    if (!isValid) {
        showAlert('error', 'Please fill all required fields');
        $('html, body').animate({
            scrollTop: $('.is-invalid').first().offset().top - 100
        }, 500);
    }
    
    return isValid;
}

function submitForm(form) {
    const formData = new FormData(form);
    const $submitBtn = $(form).find('button[type="submit"]');
    const originalText = $submitBtn.html();
    
    $submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Saving...');
    
    $.ajax({
        url: $(form).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1500);
            } else {
                showAlert('error', response.message || 'An error occurred');
                $submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr) {
            $submitBtn.prop('disabled', false).html(originalText);
            
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                console.log('Validation Errors:', errors);
                $.each(errors, function(key, value) {
                    const $input = $(`[name="${key}"]`);
                    $input.addClass('is-invalid');
                    $input.siblings('.invalid-feedback').text(value[0]);
                });
                showAlert('error', 'Please fix validation errors');
            } else {
                console.error('Server Error:', xhr);
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="bi bi-${icon}"></i> <strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(alert);
    setTimeout(() => $('.alert').fadeOut(300, function() { $(this).remove(); }), 5000);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
</script>
@endpush