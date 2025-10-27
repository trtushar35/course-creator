<nav id="sidebar">
    <div class="sidebar-header">
        <h3><i class="bi bi-mortarboard-fill"></i> COURSE STUDIO</h3>
    </div>
    <ul class="components">
        <li>
            <a href="{{ route('backend.dashboard') }}" class="{{ request()->routeIs('backend.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('backend.courses.index') }}" class="{{ 
                request()->routeIs('backend.courses.index') || 
                request()->routeIs('backend.courses.create') || 
                request()->routeIs('backend.courses.edit') ? 'active' : '' 
            }}">
                <i class="bi bi-journal"></i> <span>Course List</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>