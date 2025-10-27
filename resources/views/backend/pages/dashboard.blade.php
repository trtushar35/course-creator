@extends('backend.partials.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="mb-2 mb-md-0" style="color: var(--primary-dark); font-weight: 700;">Dashboard Overview</h2>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Courses</h6>
                        <h3 class="mb-0" style="color: var(--primary-dark);">{{$totalCourses ?? ''}}</h3>
                    </div>
                    <div class="stat-icon bg-blue">
                        <i class="bi bi-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Revenue</h6>
                        <h3 class="mb-0" style="color: var(--primary-dark);">{{$totalPrice ?? ''}}</h3>
                    </div>
                    <div class="stat-icon bg-teal">
                        ৳
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection