@extends('backend.partials.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="mb-2 mb-md-0" style="color: var(--primary-dark); font-weight: 700;">
        Course Management
    </h2>
    <a href="{{ route('backend.courses.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Create New Course
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="content-card p-4">
            @if($courses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>S/L</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Level</th>
                                <th>Price</th>
                                <th>Modules</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $key=>$course)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->category ?? 'N/A' }}</td>
                                    <td>{{ $course->level ?? 'N/A' }}</td>
                                    <td>{{ $course->formatted_price }}</td>
                                    <td>{{ $course->modules->count() }}</td>
                                    <td>
                                        <span class="badge bg-{{ $course->is_active ? 'success' : 'secondary' }}">
                                            {{ $course->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('backend.courses.edit', $course->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-course" 
                                                    data-id="{{ $course->id }}" data-title="{{ $course->title }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-x" style="font-size: 3rem; color: #6c757d;"></i>
                    <h4 class="mt-3">No courses found</h4>
                    <p class="text-muted">Get started by creating your first course.</p>
                    <a href="{{ route('backend.courses.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Course
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.delete-course').on('click', function() {
            const courseId = $(this).data('id');
            const courseTitle = $(this).data('title');
            
            if (confirm(`Are you sure you want to delete "${courseTitle}"? This action cannot be undone.`)) {
                $.ajax({
                    url: `{{ route('backend.courses.index') }}/${courseId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Failed to delete course. Please try again.');
                    }
                });
            }
        });
    });
</script>
@endpush