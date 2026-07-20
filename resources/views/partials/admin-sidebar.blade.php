<aside class="admin-sidebar" id="adminSidebar">

    {{-- ===== LOGO ===== --}}
    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-logo">
        <div class="admin-sidebar-logo-icon">
            <i class="bi bi-shield-lock-fill" style="color:white;font-size:18px;"></i>
        </div>
        <div>
            <h2>RiskChain</h2>
            <p>Admin Panel</p>
        </div>
    </a>

    {{-- ===== NAV ===== --}}
    <nav class="admin-nav">

        <div class="admin-nav-label">Overview</div>

        <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <div class="admin-nav-label">Management</div>

        <a href="{{ route('admin.users') }}" class="admin-nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Users</span>
        </a>

        <a href="{{ route('admin.countries') }}" class="admin-nav-item {{ request()->routeIs('admin.countries*') ? 'active' : '' }}">
            <i class="bi bi-globe2"></i>
            <span>Countries</span>
        </a>

        <a href="{{ route('admin.ports') }}" class="admin-nav-item {{ request()->routeIs('admin.ports*') ? 'active' : '' }}">
            <i class="bi bi-anchor"></i>
            <span>Ports</span>
        </a>

        <a href="{{ route('admin.articles') }}" class="admin-nav-item {{ request()->routeIs('admin.articles*') ? 'active' : '' }}">
            <i class="bi bi-file-text-fill"></i>
            <span>Articles</span>
        </a>

        <a href="{{ route('admin.contacts') }}" class="admin-nav-item {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
            <i class="bi bi-envelope-fill"></i>
            <span>Contact Messages</span>
            @php
                $unreadCount = \App\Models\Contact::unread()->count();
            @endphp
            @if($unreadCount > 0)
            <span class="badge rounded-pill" style="background:#EF4444;color:white;font-size:10px;margin-left:auto;">{{ $unreadCount }}</span>
            @endif
        </a>

        <div class="admin-nav-label">Intelligence</div>

        <a href="{{ route('admin.news-cache') }}" class="admin-nav-item {{ request()->routeIs('admin.news-cache*') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i>
            <span>News Intelligence</span>
        </a>

        <a href="{{ route('admin.risk-scores') }}" class="admin-nav-item {{ request()->routeIs('admin.risk-scores*') ? 'active' : '' }}">
            <i class="bi bi-shield-exclamation"></i>
            <span>Risk Monitoring</span>
        </a>

        <div class="admin-nav-label">System</div>

        <a href="{{ route('admin.api-monitor') }}" class="admin-nav-item {{ request()->routeIs('admin.api-monitor*') ? 'active' : '' }}">
            <i class="bi bi-activity"></i>
            <span>API Monitor</span>
        </a>

        <a href="{{ route('admin.logs') }}" class="admin-nav-item {{ request()->routeIs('admin.logs*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i>
            <span>System Logs</span>
        </a>

        <a href="{{ route('admin.settings') }}" class="admin-nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i>
            <span>Settings</span>
        </a>

    </nav>

    {{-- ===== FOOTER ===== --}}
    <div class="admin-sidebar-footer">
        <div class="admin-user-card">
            <div class="admin-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div class="admin-user-name text-truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="admin-user-role">Administrator</div>
            </div>
        </div>
    </div>

</aside>
