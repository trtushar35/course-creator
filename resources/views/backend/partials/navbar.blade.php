<!-- Top Navbar -->
<nav class="top-navbar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn menu-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="mb-0 ms-3 d-none d-md-block" style="color: var(--primary-dark); font-weight: 600;">Course Management</h5>
        </div>
        <div class="d-flex align-items-center gap-3">

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=Course+Admin&background=1a1f36&color=d4af37&bold=true" alt="Admin" class="rounded-circle" width="40" height="40">
                    <span class="ms-2 d-none d-md-inline" style="color: var(--text-dark); font-weight: 600;">{{ auth()->user()->name ?? 'Admin' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="{{ route('backend.logout') }}"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>