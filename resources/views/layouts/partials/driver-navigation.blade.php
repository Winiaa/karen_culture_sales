<ul class="nav flex-column mb-auto">
    <li class="nav-item">
        <a href="{{ route('driver.dashboard') }}" class="nav-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('driver.deliveries.index') }}" class="nav-link {{ request()->routeIs('driver.deliveries.index') ? 'active' : '' }}">
            <i class="fas fa-list"></i> All Deliveries
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('driver.deliveries.assigned') }}" class="nav-link {{ request()->routeIs('driver.deliveries.assigned') ? 'active' : '' }}">
            <i class="fas fa-truck-loading"></i> Assigned Deliveries
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('driver.deliveries.completed') }}" class="nav-link {{ request()->routeIs('driver.deliveries.completed') ? 'active' : '' }}">
            <i class="fas fa-check-circle"></i> Completed Deliveries
        </a>
    </li>
    <li class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('driver.profile.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#profileSubmenu">
            <i class="fas fa-user-circle"></i> My Account
        </a>
        <div class="collapse {{ request()->routeIs('driver.profile.*') ? 'show' : '' }}" id="profileSubmenu">
            <ul class="nav flex-column ms-3">
                <li class="nav-item">
                    <a href="{{ route('driver.profile.edit') }}" class="nav-link {{ request()->routeIs('driver.profile.edit') ? 'active' : '' }}">
                        <i class="fas fa-id-card"></i> Driver Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('driver.profile.password') }}" class="nav-link {{ request()->routeIs('driver.profile.password') ? 'active' : '' }}">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul> 