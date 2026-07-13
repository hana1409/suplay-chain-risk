<header class="topbar">

    {{-- Mobile Sidebar Toggle --}}
    <button id="sidebarToggle"
        style="display:none;background:transparent;border:none;color:var(--text-secondary);font-size:20px;cursor:pointer;padding:8px;"
        class="d-lg-none">
        <i class="bi bi-list"></i>
    </button>

    {{-- Global Search --}}
    <div class="topbar-search" style="position:relative;">
        <div class="search-input-wrap">
            <i class="bi bi-search"></i>
            <input
                type="text"
                id="globalSearch"
                class="intel-input"
                placeholder="Search country, port, region..."
                autocomplete="off"
                style="padding-right:44px;"
            >
            <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:10px;color:var(--text-muted);background:var(--bg-card);border:1px solid var(--border);border-radius:4px;padding:1px 5px;pointer-events:none;">⌘K</span>
        </div>
        {{-- Search results dropdown --}}
        <div id="searchResults"
            style="display:none;position:absolute;top:100%;left:0;right:0;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);margin-top:6px;z-index:200;max-height:320px;overflow-y:auto;box-shadow:var(--shadow-lg);">
        </div>
    </div>

    {{-- Right side --}}
    <div class="topbar-right">

        {{-- Threat Level --}}
        @php
            $criticalCount = \App\Models\RiskScore::where('risk_level', 'Critical')->count();
            $threatClass   = $criticalCount >= 10 ? 'threat-critical' : ($criticalCount >= 5 ? 'threat-high' : ($criticalCount >= 1 ? 'threat-medium' : 'threat-low'));
            $threatLabel   = $criticalCount >= 10 ? 'CRITICAL' : ($criticalCount >= 5 ? 'HIGH' : ($criticalCount >= 1 ? 'ELEVATED' : 'NORMAL'));
        @endphp
        <div class="threat-badge {{ $threatClass }}" title="{{ $criticalCount }} critical countries">
            <span class="threat-dot"></span>
            <span class="threat-label">{{ $threatLabel }}</span>
        </div>

        {{-- Live Status --}}
        <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--risk-low);font-weight:600;letter-spacing:0.3px;">
            <span class="pulse-dot"></span>
            <span>LIVE</span>
        </div>

        {{-- Watchlist --}}
        <a href="{{ route('watchlist') }}" class="topbar-icon-btn" title="Watchlist">
            <i class="bi bi-star-fill" style="color:#F59E0B;font-size:14px;"></i>
        </a>

        {{-- Notifications (static for now) --}}
        <div class="topbar-icon-btn" title="Alerts">
            <i class="bi bi-bell-fill"></i>
            <span class="badge-dot"></span>
        </div>

        {{-- Clock --}}
        <div class="topbar-time">
            <span id="topbar-clock" style="font-weight:600;color:var(--text-secondary);">00:00:00</span>
            <small id="topbar-utc">00:00:00 UTC</small>
        </div>

    </div>

</header>

<script>
// Global search AJAX
(function() {
    const input = document.getElementById('globalSearch');
    const results = document.getElementById('searchResults');
    if (!input || !results) return;

    let timeout;

    input.addEventListener('input', () => {
        clearTimeout(timeout);
        const q = input.value.trim();

        if (q.length < 2) {
            results.style.display = 'none';
            return;
        }

        timeout = setTimeout(() => {
            fetch(`/countries?search=${encodeURIComponent(q)}&format=json`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.length) {
                    results.innerHTML = '<div style="padding:14px 16px;color:var(--text-muted);font-size:13px;display:flex;align-items:center;gap:8px;"><i class="bi bi-search" style="font-size:16px;"></i> No results found</div>';
                } else {
                    results.innerHTML = data.slice(0, 8).map(c => `
                        <a href="/countries/${c.country_code}"
                           style="display:flex;align-items:center;gap:10px;padding:10px 16px;text-decoration:none;color:var(--text-primary);border-bottom:1px solid var(--border);transition:background 0.2s;"
                           onmouseover="this.style.background='var(--accent-subtle)'"
                           onmouseout="this.style.background='transparent'">
                            <img src="https://flagcdn.com/w20/${c.country_code.toLowerCase()}.png" width="20" height="14" style="border-radius:2px;border:1px solid var(--border);" alt="">
                            <span style="font-size:13px;font-weight:500;">${c.country_name}</span>
                            <span style="margin-left:auto;font-size:11px;color:var(--text-muted);background:var(--bg-base);padding:2px 8px;border-radius:999px;">${c.region ?? ''}</span>
                        </a>
                    `).join('');
                }
                results.style.display = 'block';
            })
            .catch(() => { results.style.display = 'none'; });
        }, 280);
    });

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !results.contains(e.target)) {
            results.style.display = 'none';
        }
    });

    // Keyboard shortcut ⌘K / Ctrl+K
    document.addEventListener('keydown', e => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            input.focus();
            input.select();
        }
    });
})();
</script>