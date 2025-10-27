<div class="module-card" data-module-id="{{ $moduleIndex }}">
    <div class="module-header" data-module-id="{{ $moduleIndex }}">
        <span class="fw-bold"><i class="bi bi-collection"></i> Module {{ $moduleIndex }}</span>
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
            <input type="text" class="form-control" 
                   name="modules[{{ $moduleIndex }}][title]" 
                   value="{{ $module->title }}"
                   placeholder="Enter module title" required>
        </div>
        
        <button type="button" class="btn btn-sm btn-primary add-content" data-module-id="{{ $moduleIndex }}">
            <i class="bi bi-plus"></i> Add Content
        </button>
        
        <div class="contents-container mt-3" id="contentsContainer{{ $moduleIndex }}">
            @foreach($module->contents as $contentIndex => $content)
                @include('backend.pages.course.partials.content', [
                    'content' => $content,
                    'moduleIndex' => $moduleIndex,
                    'contentIndex' => $contentIndex + 1
                ])
            @endforeach
        </div>
    </div>
</div>