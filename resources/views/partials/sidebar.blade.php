<aside class="sidebar" id="sidebar">

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

    {{-- ===== NAV ===== --}}
    <nav class="sidebar-nav">

        <div class="sidebar-section-label">Main</div>

        <a href="{{ route('dashboard') }}" class="sidebar-item">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('global-map') }}" class="sidebar-item">
            <i class="bi bi-globe-americas"></i>
            <span>Global Map</span>
        </a>

        <a href="{{ route('countries') }}" class="sidebar-item">
            <i class="bi bi-flag-fill"></i>
            <span>Countries</span>
        </a>

        <a href="{{ route('compare') }}" class="sidebar-item">
            <i class="bi bi-bar-chart-steps"></i>
            <span>Comparison</span>
        </a>

        <div class="sidebar-section-label">Intelligence</div>

        <a href="{{ route('news') }}" class="sidebar-item">
            <i class="bi bi-newspaper"></i>
            <span>News Intelligence</span>
        </a>

        <a href="{{ route('ports') }}" class="sidebar-item">
            <i class="bi bi-anchor"></i>
            <span>Port Dashboard</span>
        </a>

        <a href="{{ route('watchlist') }}" class="sidebar-item">
            <i class="bi bi-star-fill"></i>
            <span>Watchlist</span>
        </a>

        @if(auth()->user() && auth()->user()->role_id === 1)
        <div class="sidebar-section-label">Admin</div>

        <a href="{{ route('admin.dashboard') }}" class="sidebar-item">
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
                    style="background:transparent;border:none;color:var(--text-muted);cursor:pointer;padding:4px;border-radius:6px;transition:color 0.2s;"
                    onmouseover="this.style.color='var(--risk-critical)'"
                    onmouseout="this.style.color='var(--text-muted)'"
                    title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

</aside>
