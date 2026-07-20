<aside class="sidebar" id="sidebar">

    {{-- ===== TOGGLE BUTTON ===== --}}
    <button class="sidebar-toggle-btn" id="sidebarCollapseBtn" title="Toggle Sidebar">
        <i class="bi bi-chevron-left"></i>
    </button>

    {{-- ===== LOGO ===== --}}
    <a href="{{ route('dashboard') }}" class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="bi bi-globe-americas" style="color:white;font-size:18px;position:relative;z-index:1;"></i>
        </div>
        <div class="sidebar-logo-text">
            <h2>RiskChain</h2>
            <p>Intel Platform</p>
        </div>
    </a>

    {{-- ===== NAV (SCROLLABLE) ===== --}}
    <nav class="sidebar-nav" id="sidebarNav">

        <div class="sidebar-section-label">Main</div>

        <a href="{{ route('dashboard') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('global-map') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Global Map">
            <i class="bi bi-globe-americas"></i>
            <span>Global Map</span>
        </a>

        <a href="{{ route('countries') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Countries">
            <i class="bi bi-flag-fill"></i>
            <span>Countries</span>
        </a>

        <a href="{{ route('compare') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Comparison">
            <i class="bi bi-bar-chart-steps"></i>
            <span>Comparison</span>
        </a>

        <a href="{{ route('currency.dashboard') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Currency Dashboard">
            <i class="bi bi-currency-exchange"></i>
            <span>Currency Dashboard</span>
        </a>

        <a href="{{ route('data.visualization') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Data Visualization">
            <i class="bi bi-bar-chart-line-fill"></i>
            <span>Data Visualization</span>
        </a>

        <div class="sidebar-section-label">Intelligence</div>

        <a href="{{ route('news') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="News Intelligence">
            <i class="bi bi-newspaper"></i>
            <span>News Intelligence</span>
        </a>

        <a href="{{ route('ports') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Port Dashboard">
            <i class="bi bi-anchor"></i>
            <span>Port Dashboard</span>
        </a>

        <a href="{{ route('watchlist') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Watchlist">
            <i class="bi bi-star-fill"></i>
            <span>Watchlist</span>
        </a>

        @if(auth()->user() && auth()->user()->role_id === 1)
        <div class="sidebar-section-label">Admin</div>

        <a href="{{ route('admin.dashboard') }}" class="sidebar-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Admin Panel">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Admin Panel</span>
        </a>
        @endif

    </nav>

    {{-- ===== USER FOOTER ===== --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->role_id === 1 ? 'Administrator' : 'Analyst' }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                @csrf
                <button type="submit"
                    class="sidebar-logout-btn"
                    title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

</aside>
