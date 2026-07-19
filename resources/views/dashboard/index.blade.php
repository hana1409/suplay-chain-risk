@extends('layouts.dashboard')

@section('title', 'Dashboard — Global Risk Intelligence')

@push('styles')
<style>
/* ============================================
   DASHBOARD PAGE — Intel Ops Center Styles
   ============================================ */

/* ── KPI card accent bars ── */
.kpi-card-countries  { --kpi-color: var(--accent); }
.kpi-card-avg-risk   { --kpi-color: var(--risk-medium); }
.kpi-card-high       { --kpi-color: var(--risk-high); }
.kpi-card-critical   { --kpi-color: var(--risk-critical); }
.kpi-card-ports      { --kpi-color: var(--risk-low); }

.kpi-card::before { background: linear-gradient(90deg, var(--kpi-color), transparent); }

/* ── Section divider ── */
.dash-section {
    margin-bottom: var(--space-xl);
}

/* ── Map tile toggle active style ── */
.tile-btn.active {
    border-color: var(--accent) !important;
    color: var(--accent) !important;
    background: rgba(139,92,246,0.15) !important;
}

/* ── Scrollable top risk table ── */
.risk-table-scroll {
    max-height: 420px;
    overflow-y: auto;
}

/* ── News card hover negative glow ── */
.news-card.sentiment-negative-card:hover {
    box-shadow: 0 0 12px rgba(239,68,68,0.15);
}

/* ── Donut center label ── */
.donut-center {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}

.donut-center-value {
    font-size: 26px;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
}

.donut-center-label {
    font-size: 10px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 2px;
}

.chart-wrapper-donut {
    position: relative;
    max-width: 220px;
    margin: 0 auto;
}

/* ── Pulse ring on critical markers ── */
@keyframes markerPulse {
    0%   { transform: scale(1); opacity: 0.8; }
    70%  { transform: scale(2.2); opacity: 0; }
    100% { transform: scale(1); opacity: 0; }
}

/* ── Counter animation ── */
@keyframes fadeCountUp {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.kpi-value { animation: fadeCountUp 0.6s ease forwards; }

/* ── Section empty state ── */
.empty-state {
    padding: 40px 24px;
    text-align: center;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 32px;
    display: block;
    margin-bottom: 10px;
    opacity: 0.4;
}

/* ── View all link ── */
.view-all-link {
    font-size: 12px;
    color: var(--accent);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: var(--transition);
}
.view-all-link:hover { color: var(--accent-light); gap: 7px; }

/* ── Risk distribution legend ── */
.donut-legend {
    display: flex;
    flex-direction: column;
    gap: 10px;
    justify-content: center;
}

.donut-legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
}

.donut-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.donut-legend-label {
    color: var(--text-secondary);
    flex: 1;
}

.donut-legend-count {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-primary);
}

/* ── Leaflet Zoom Control for Light Theme ── */
.leaflet-bar a {
    background-color: rgba(255, 255, 255, 0.9) !important;
    color: #475569 !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
}
.leaflet-bar a:hover {
    background-color: #ffffff !important;
    color: var(--accent) !important;
}
.leaflet-bar {
    border: none !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    border-radius: 8px !important;
    overflow: hidden;
}
</style>
@endpush

@section('content')

{{-- ========================= --}}
{{-- PAGE HEADER              --}}
{{-- ========================= --}}
<div class="page-header fade-in-up">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <i class="bi bi-house-fill"></i>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">Dashboard</span>
        </div>
        <h1>Global Risk Intelligence</h1>
        <p>Real-time supply chain risk monitoring across <strong style="color:var(--text-primary)">{{ $totalCountries }}</strong> countries and <strong style="color:var(--text-primary)">{{ number_format($totalPorts) }}</strong> ports</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <button class="btn-ghost" onclick="refreshDashboard()" id="refreshBtn" style="font-size:13px;">
            <i class="bi bi-arrow-clockwise" id="refreshIcon"></i> Refresh
        </button>
        <a href="{{ route('global-map') }}" class="btn-primary-custom" style="font-size:13px;">
            <i class="bi bi-globe-americas"></i> Full Map View
        </a>
    </div>
</div>

{{-- ========================= --}}
{{-- KPI STRIP                --}}
{{-- ========================= --}}
<div class="kpi-strip fade-in-up fade-in-up-1">

    {{-- Countries --}}
    <div class="kpi-card kpi-card-countries">
        <div class="kpi-card-bg-orb" style="background:var(--accent);"></div>
        <div class="kpi-header">
            <div class="kpi-icon" style="background:var(--accent-subtle);border:1px solid rgba(139,92,246,0.2);">
                <i class="bi bi-globe2" style="color:var(--accent);"></i>
            </div>
            <span class="kpi-trend up"><i class="bi bi-arrow-up"></i> Global</span>
        </div>
        <div class="kpi-value" data-target="{{ $totalCountries }}" id="kpi-countries">{{ number_format($totalCountries) }}</div>
        <div class="kpi-label">Countries Monitored</div>
    </div>

    {{-- Avg Risk --}}
    <div class="kpi-card kpi-card-avg-risk">
        <div class="kpi-card-bg-orb" style="background:var(--risk-medium);"></div>
        <div class="kpi-header">
            <div class="kpi-icon" style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);">
                <i class="bi bi-activity" style="color:var(--risk-medium);"></i>
            </div>
            <span class="kpi-trend {{ $avgRisk > 50 ? 'down' : 'up' }}">
                <i class="bi bi-arrow-{{ $avgRisk > 50 ? 'up' : 'down' }}"></i>
                {{ $avgRisk > 50 ? 'Above avg' : 'Normal' }}
            </span>
        </div>
        <div class="kpi-value" style="color:var(--risk-medium);" id="kpi-avgrisk">{{ number_format($avgRisk, 1) }}</div>
        <div class="kpi-label">Average Risk Score</div>
    </div>

    {{-- High Risk --}}
    <div class="kpi-card kpi-card-high">
        <div class="kpi-card-bg-orb" style="background:var(--risk-high);"></div>
        <div class="kpi-header">
            <div class="kpi-icon" style="background:rgba(249,115,22,0.1);border:1px solid rgba(249,115,22,0.2);">
                <i class="bi bi-exclamation-triangle-fill" style="color:var(--risk-high);"></i>
            </div>
            <span class="kpi-trend down"><i class="bi bi-exclamation-circle"></i> Alert</span>
        </div>
        <div class="kpi-value" style="color:var(--risk-high);" id="kpi-high">{{ $highRiskCount }}</div>
        <div class="kpi-label">High Risk Countries</div>
    </div>

    {{-- Critical --}}
    <div class="kpi-card kpi-card-critical">
        <div class="kpi-card-bg-orb" style="background:var(--risk-critical);"></div>
        <div class="kpi-header">
            <div class="kpi-icon" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);">
                <i class="bi bi-shield-exclamation" style="color:var(--risk-critical);"></i>
            </div>
            <span class="kpi-trend down"><i class="bi bi-x-circle"></i> Critical</span>
        </div>
        <div class="kpi-value" style="color:var(--risk-critical);" id="kpi-critical">{{ $criticalCount }}</div>
        <div class="kpi-label">Critical Alerts</div>
    </div>

    {{-- Ports --}}
    <div class="kpi-card kpi-card-ports">
        <div class="kpi-card-bg-orb" style="background:var(--risk-low);"></div>
        <div class="kpi-header">
            <div class="kpi-icon" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);">
                <i class="bi bi-anchor" style="color:var(--risk-low);"></i>
            </div>
            <span class="kpi-trend up"><i class="bi bi-arrow-up"></i> All regions</span>
        </div>
        <div class="kpi-value" style="color:var(--risk-low);" id="kpi-ports">{{ number_format($totalPorts) }}</div>
        <div class="kpi-label">Ports Tracked</div>
    </div>

</div>

{{-- ========================= --}}
{{-- WORLD MAP + SIDEBAR      --}}
{{-- ========================= --}}
<div class="main-sidebar-grid dash-section fade-in-up fade-in-up-2">

    {{-- WORLD MAP --}}
    <div>
        <div class="section-header">
            <div class="section-title">
                <span></span> Global Risk Map
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                {{-- Tile layer toggle --}}
                <div style="display:flex;gap:4px;">
                    <button class="map-ctrl-btn tile-btn active" id="tileLight" onclick="setTileLayer('light')">
                        <i class="bi bi-sun-fill"></i> Light
                    </button>
                    <button class="map-ctrl-btn tile-btn" id="tileSatellite" onclick="setTileLayer('satellite')">
                        <i class="bi bi-layers-fill"></i> Satellite
                    </button>
                </div>
                {{-- Risk filter --}}
                <select id="mapFilter" class="intel-input" style="width:auto;padding:6px 12px;font-size:12px;" onchange="filterMapByRisk(this.value)">
                    <option value="all">All Risk Levels</option>
                    <option value="Low">🟢 Low Risk</option>
                    <option value="Medium">🟡 Medium Risk</option>
                    <option value="High">🟠 High Risk</option>
                    <option value="Critical">🔴 Critical</option>
                </select>
            </div>
        </div>

        <div class="dashboard-map-wrap">
            {{-- Loading Overlay --}}
            <div class="map-loading" id="mapLoading">
                <div class="intel-spinner"></div>
                <span>Loading risk data...</span>
            </div>

            {{-- Map --}}
            <div id="world-map"></div>

            {{-- Map Legend --}}
            <div style="position:absolute;bottom:24px;left:24px;z-index:500;background:rgba(255,255,255,0.95);backdrop-filter:blur(8px);border:1px solid rgba(0,0,0,0.05);border-radius:12px;padding:14px 18px;box-shadow:0 4px 16px rgba(0,0,0,0.08);display:flex;flex-direction:column;gap:10px;font-size:12px;color:var(--text-secondary);">
                <div style="font-size:10px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">Risk Level</div>
                <div style="display:flex;align-items:center;gap:8px;"><span style="width:10px;height:10px;border-radius:50%;background:#10B981;opacity:0.75;border:1px solid #fff;"></span> Low</div>
                <div style="display:flex;align-items:center;gap:8px;"><span style="width:10px;height:10px;border-radius:50%;background:#F59E0B;opacity:0.75;border:1px solid #fff;"></span> Medium</div>
                <div style="display:flex;align-items:center;gap:8px;"><span style="width:10px;height:10px;border-radius:50%;background:#F97316;opacity:0.75;border:1px solid #fff;"></span> High</div>
                <div style="display:flex;align-items:center;gap:8px;"><span style="width:10px;height:10px;border-radius:50%;background:#EF4444;opacity:0.75;border:1px solid #fff;"></span> Critical</div>
            </div>
            
            {{-- Hidden counter text to prevent JS errors --}}
            <div id="mapCounterText" style="display:none;"></div>

            {{-- Country Popup Panel --}}
            <div class="country-panel" id="countryPanel">
                <div class="panel-header">
                    <img src="" id="panelFlag" class="panel-flag" alt="">
                    <div>
                        <div class="panel-country-name" id="panelName">—</div>
                        <div class="panel-region" id="panelRegion">—</div>
                    </div>
                    <button class="panel-close" onclick="closePanel()"><i class="bi bi-x"></i></button>
                </div>
                <div class="panel-body">
                    {{-- Risk Score Row --}}
                    <div class="panel-risk-score">
                        <div>
                            <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Risk Score</div>
                            <div class="panel-risk-number" id="panelScore">—</div>
                        </div>
                        <div style="text-align:right;">
                            <span class="risk-badge" id="panelBadge">—</span>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px;" id="panelWeather">—</div>
                        </div>
                    </div>

                    {{-- Economic Indicators — sourced from economic_caches --}}
                    <div style="font-size:9px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.6px;margin-bottom:6px;display:flex;align-items:center;gap:5px;">
                        <i class="bi bi-database-fill" style="font-size:9px;"></i> Economic Data
                    </div>
                    <div class="panel-grid">
                        <div class="panel-metric">
                            <div class="panel-metric-label"><i class="bi bi-graph-up" style="font-size:9px;margin-right:3px;"></i>GDP</div>
                            <div class="panel-metric-value" id="panelGdp">—</div>
                        </div>
                        <div class="panel-metric">
                            <div class="panel-metric-label"><i class="bi bi-percent" style="font-size:9px;margin-right:3px;"></i>Inflation</div>
                            <div class="panel-metric-value" id="panelInflation">—</div>
                        </div>
                        <div class="panel-metric">
                            <div class="panel-metric-label"><i class="bi bi-people-fill" style="font-size:9px;margin-right:3px;"></i>Population</div>
                            <div class="panel-metric-value" id="panelPop">—</div>
                        </div>
                        <div class="panel-metric">
                            <div class="panel-metric-label"><i class="bi bi-box-arrow-up-right" style="font-size:9px;margin-right:3px;"></i>Exports</div>
                            <div class="panel-metric-value" id="panelExports" style="color:var(--risk-low);">—</div>
                        </div>
                    </div>
                    {{-- Imports — full-width card --}}
                    <div class="panel-metric" style="margin-bottom:14px;">
                        <div class="panel-metric-label"><i class="bi bi-box-arrow-in-down-left" style="font-size:9px;margin-right:3px;"></i>Imports</div>
                        <div class="panel-metric-value" id="panelImports" style="color:var(--risk-medium);">—</div>
                    </div>

                    <div class="panel-actions">
                        <a href="#" id="panelDetailBtn" class="panel-btn-detail">
                            <i class="bi bi-arrow-right-circle"></i> Detail
                        </a>
                        <a href="#" id="panelCompareBtn" class="panel-btn-compare">
                            <i class="bi bi-bar-chart-steps"></i> Compare
                        </a>
                        <button class="panel-btn-watch" id="panelWatchBtn" onclick="toggleWatch()" title="Watchlist">
                            <i class="bi bi-star"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RISK SIDEBAR --}}
    <div>
        {{-- Top Risk Countries --}}
        <div class="section-header">
            <div class="section-title"><span></span> Top Risk Countries</div>
            <a href="{{ route('countries') }}" class="view-all-link">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="glass-card" style="padding:8px;margin-bottom:var(--space-lg);">
            @forelse($topRiskCountries->take(8) as $i => $rs)
            <a href="{{ route('countries.show', $rs->country?->country_code ?? '#') }}"
               class="risk-list-item"
               onclick="flyToCountry('{{ $rs->country?->country_code }}')">
                <div class="risk-rank" style="{{ $i < 3 ? 'color:var(--accent);' : '' }}">{{ $i + 1 }}</div>
                <img src="https://flagcdn.com/w20/{{ strtolower($rs->country?->country_code ?? 'xx') }}.png"
                     width="20" height="14"
                     style="border-radius:2px;border:1px solid var(--border);"
                     alt="">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $rs->country?->country_name ?? '—' }}
                    </div>
                    <div class="risk-score-bar" style="width:{{ $rs->total_score }}%;background:{{ $rs->risk_color }};"></div>
                </div>
                <div style="font-size:13px;font-weight:700;color:{{ $rs->risk_color }};flex-shrink:0;">
                    {{ number_format($rs->total_score, 1) }}
                </div>
                <span class="risk-badge {{ strtolower('risk-' . $rs->risk_level) }}" style="font-size:10px;padding:2px 8px;">
                    {{ $rs->risk_level }}
                </span>
            </a>
            @empty
            <div class="empty-state">
                <i class="bi bi-info-circle"></i>
                No risk scores yet.<br>
                <a href="{{ route('countries') }}" style="color:var(--accent);font-size:13px;">View countries</a>
            </div>
            @endforelse
        </div>

        {{-- Region Risk --}}
        @if($regionRisk->isNotEmpty())
        <div class="section-header">
            <div class="section-title"><span></span> Risk by Region</div>
        </div>
        <div class="chart-panel" style="padding:16px 18px;">
            @foreach($regionRisk->take(6) as $region)
            @if($region->region)
            @php
                $rScore = $region->avg_risk ?? 0;
                $rColor = $rScore > 75 ? '#EF4444' : ($rScore > 50 ? '#F97316' : ($rScore > 25 ? '#F59E0B' : '#10B981'));
            @endphp
            <div class="region-bar-item">
                <div class="region-bar-header">
                    <span class="region-bar-name">{{ $region->region }}</span>
                    <span class="region-bar-score" style="color:{{ $rColor }};">{{ number_format($rScore, 1) }}</span>
                </div>
                <div class="region-bar-track">
                    <div class="region-bar-fill" style="width:{{ min(100, $rScore) }}%;background:{{ $rColor }};"></div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @endif

    </div>

</div>

{{-- ========================= --}}
{{-- CHARTS ROW               --}}
{{-- ========================= --}}
<div class="charts-grid dash-section fade-in-up fade-in-up-3">

    {{-- Risk Distribution Donut --}}
    <div class="chart-panel">
        <div class="chart-panel-header">
            <div class="chart-panel-title">
                <i class="bi bi-pie-chart-fill"></i> Risk Distribution
            </div>
            <span class="chart-panel-meta">All Countries</span>
        </div>
        <div style="display:flex;align-items:center;gap:28px;">
            <div class="chart-wrapper-donut" style="flex-shrink:0;">
                <canvas id="riskDonutChart" width="220" height="220"></canvas>
                <div class="donut-center">
                    <div class="donut-center-value">{{ $totalCountries }}</div>
                    <div class="donut-center-label">Countries</div>
                </div>
            </div>
            <div class="donut-legend" style="flex:1;">
                @php
                    $lowCount      = $topRiskCountries->where('risk_level', 'Low')->count();
                    $mediumCount   = $topRiskCountries->where('risk_level', 'Medium')->count();
                    $highCount     = $topRiskCountries->where('risk_level', 'High')->count();
                    $critCount     = $topRiskCountries->where('risk_level', 'Critical')->count();
                    // For the full counts via model
                    $allLow        = \App\Models\RiskScore::where('risk_level', 'Low')->count();
                    $allMedium     = \App\Models\RiskScore::where('risk_level', 'Medium')->count();
                    $allHigh       = \App\Models\RiskScore::where('risk_level', 'High')->count();
                    $allCritical   = \App\Models\RiskScore::where('risk_level', 'Critical')->count();
                    $withScore     = $allLow + $allMedium + $allHigh + $allCritical;
                    $noData        = $totalCountries - $withScore;
                @endphp
                <div class="donut-legend-item">
                    <span class="donut-legend-dot" style="background:#10B981;"></span>
                    <span class="donut-legend-label">Low Risk</span>
                    <span class="donut-legend-count" style="color:#10B981;">{{ $allLow }}</span>
                </div>
                <div class="donut-legend-item">
                    <span class="donut-legend-dot" style="background:#F59E0B;"></span>
                    <span class="donut-legend-label">Medium Risk</span>
                    <span class="donut-legend-count" style="color:#F59E0B;">{{ $allMedium }}</span>
                </div>
                <div class="donut-legend-item">
                    <span class="donut-legend-dot" style="background:#F97316;"></span>
                    <span class="donut-legend-label">High Risk</span>
                    <span class="donut-legend-count" style="color:#F97316;">{{ $allHigh }}</span>
                </div>
                <div class="donut-legend-item">
                    <span class="donut-legend-dot" style="background:#EF4444;"></span>
                    <span class="donut-legend-label">Critical</span>
                    <span class="donut-legend-count" style="color:#EF4444;">{{ $allCritical }}</span>
                </div>
                @if($noData > 0)
                <div class="donut-legend-item">
                    <span class="donut-legend-dot" style="background:#374151;"></span>
                    <span class="donut-legend-label">No Data</span>
                    <span class="donut-legend-count" style="color:var(--text-muted);">{{ $noData }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Region Bar Chart --}}
    <div class="chart-panel">
        <div class="chart-panel-header">
            <div class="chart-panel-title">
                <i class="bi bi-bar-chart-line-fill"></i> Avg Risk by Region
            </div>
            <span class="chart-panel-meta">Top {{ $regionRisk->count() }} Regions</span>
        </div>
        <div style="height:220px;">
            <canvas id="regionBarChart"></canvas>
        </div>
    </div>

</div>

{{-- ========================= --}}
{{-- TOP RISK TABLE           --}}
{{-- ========================= --}}
<div class="dash-section fade-in-up fade-in-up-4">
    <div class="section-header">
        <div class="section-title"><span></span> Risk Leaderboard</div>
        <a href="{{ route('countries') }}" class="view-all-link">
            All Countries <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="chart-panel" style="padding:0;overflow:hidden;">
        @if($topRiskCountries->isNotEmpty())
        <div class="risk-table-scroll">
            <table class="intel-risk-table">
                <thead>
                    <tr>
                        <th style="width:48px;padding-left:18px;">#</th>
                        <th>Country</th>
                        <th>Region</th>
                        <th style="min-width:120px;">Risk Score</th>
                        <th>Status</th>
                        <th>Components</th>
                        <th style="width:80px;text-align:right;padding-right:18px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topRiskCountries as $i => $rs)
                    <tr onclick="window.location='{{ route('countries.show', $rs->country?->country_code ?? '#') }}'">
                        <td style="padding-left:18px;">
                            <span class="risk-rank-num {{ $i < 3 ? 'top' : '' }}">{{ $i + 1 }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="https://flagcdn.com/w20/{{ strtolower($rs->country?->country_code ?? 'xx') }}.png"
                                     width="20" height="14"
                                     style="border-radius:2px;border:1px solid var(--border);"
                                     alt="">
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ $rs->country?->country_name ?? '—' }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $rs->country?->country_code ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size:12px;color:var(--text-muted);">{{ $rs->country?->region ?? '—' }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="score-bar-wrap" style="width:80px;">
                                    <div class="score-bar-fill" style="width:{{ $rs->total_score }}%;background:{{ $rs->risk_color }};"></div>
                                </div>
                                <span style="font-size:13px;font-weight:700;color:{{ $rs->risk_color }};min-width:36px;">{{ number_format($rs->total_score, 1) }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="risk-badge {{ $rs->risk_badge_class }}" style="font-size:11px;">
                                {{ $rs->risk_level }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                @if($rs->weather_score)
                                <span title="Weather" style="font-size:10px;color:var(--text-muted);background:var(--bg-base);padding:2px 6px;border-radius:4px;border:1px solid var(--border);">
                                    🌡️ {{ number_format($rs->weather_score, 0) }}
                                </span>
                                @endif
                                @if($rs->inflation_score)
                                <span title="Inflation" style="font-size:10px;color:var(--text-muted);background:var(--bg-base);padding:2px 6px;border-radius:4px;border:1px solid var(--border);">
                                    📈 {{ number_format($rs->inflation_score, 0) }}
                                </span>
                                @endif
                                @if($rs->news_score)
                                <span title="News" style="font-size:10px;color:var(--text-muted);background:var(--bg-base);padding:2px 6px;border-radius:4px;border:1px solid var(--border);">
                                    📰 {{ number_format($rs->news_score, 0) }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align:right;padding-right:18px;">
                            <a href="{{ route('countries.show', $rs->country?->country_code ?? '#') }}"
                               style="font-size:12px;color:var(--accent);text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border:1px solid rgba(139,92,246,0.3);border-radius:var(--radius-sm);transition:var(--transition);"
                               onmouseover="this.style.background='var(--accent-subtle)'"
                               onmouseout="this.style.background='transparent'"
                               onclick="event.stopPropagation()">
                                Detail <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <i class="bi bi-database-x"></i>
            No risk scores available. <a href="{{ route('countries') }}" style="color:var(--accent);">Go to Countries</a>
        </div>
        @endif
    </div>
</div>

{{-- ========================= --}}
{{-- INTELLIGENCE FEED        --}}
{{-- ========================= --}}
@if($recentNews->isNotEmpty())
<div class="dash-section fade-in-up fade-in-up-4">
    <div class="section-header">
        <div class="section-title"><span></span> Intelligence Feed</div>
        <a href="{{ route('news') }}" class="view-all-link">
            All Intelligence <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="news-grid">
        @foreach($recentNews as $n)
        @php
            $cardClass = match($n->sentiment) {
                'Negative' => 'sentiment-negative-card',
                'Positive' => 'sentiment-positive-card',
                default    => 'sentiment-neutral-card',
            };
        @endphp
        <a href="{{ $n->url }}" target="_blank" rel="noopener" class="news-card {{ $cardClass }}">
            <div class="news-card-title">{{ $n->title }}</div>
            <div class="news-card-meta">
                @if($n->country)
                <img src="https://flagcdn.com/w20/{{ strtolower($n->country->country_code ?? 'xx') }}.png"
                     width="14" height="10"
                     style="border-radius:1px;border:1px solid var(--border);" alt="">
                @endif
                <span class="sentiment-{{ strtolower($n->sentiment ?? 'neutral') }}" style="font-size:10px;padding:1px 7px;">
                    {{ $n->sentiment ?? 'Neutral' }}
                </span>
                @if($n->source)
                <span style="font-size:10px;color:var(--text-muted);">{{ Str::limit($n->source, 20) }}</span>
                @endif
                <span class="news-card-time">{{ $n->published_at?->diffForHumans() }}</span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// =========================================
// WORLD MAP SETUP
// =========================================
const map = L.map('world-map', {
    center: [20, 0],
    zoom: 2,
    minZoom: 2,
    maxZoom: 10,
    zoomControl: false,
});

// Tile layers
const tileLayers = {
    light: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap © CARTO',
        subdomains: 'abcd',
        maxZoom: 20,
    }),
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri',
        maxZoom: 20,
    }),
};

let currentTile = 'light';
tileLayers.light.addTo(map);

// Custom zoom position
L.control.zoom({ position: 'bottomright' }).addTo(map);

// =========================================
// MARKER DATA
// =========================================
let allMarkers     = [];
let allCountryData = {};
let currentPanel   = null;
let markerGroup    = L.layerGroup().addTo(map);

function getRiskSize(score) {
    if (score > 75) return 7;
    if (score > 50) return 6;
    if (score > 25) return 5.5;
    return 5;
}

function createCircleMarker(country) {
    const color = country.color || '#374151';
    const score = country.score || 0;
    const size  = getRiskSize(score);

    const marker = L.circleMarker([country.lat, country.lng], {
        radius:      size,
        fillColor:   color,
        color:       '#ffffff',
        weight:      1.5,
        opacity:     0.9,
        fillOpacity: 0.8,
    });

    marker.on('click', () => loadCountryPanel(country.code));

    marker.bindTooltip(`
        <div style="font-size:12px;font-weight:700;margin-bottom:3px;">${country.flag || ''} ${country.name}</div>
        <div style="font-size:11px;color:${color};">Score: ${score.toFixed(1)} — ${country.level}</div>
        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">${country.region || ''}</div>
    `, { className: 'intel-tooltip', direction: 'top' });

    return marker;
}

function loadMapData() {
    document.getElementById('mapLoading').style.display = 'flex';
    fetch('/api/map/countries')
        .then(r => r.json())
        .then(data => {
            document.getElementById('mapLoading').style.display = 'none';
            markerGroup.clearLayers();
            allMarkers = [];

            let counts = { Low: 0, Medium: 0, High: 0, Critical: 0, Unknown: 0 };

            data.forEach(country => {
                allCountryData[country.code] = country;
                const marker = createCircleMarker(country);
                marker._countryData = country;
                allMarkers.push(marker);
                marker.addTo(markerGroup);
                counts[country.level] = (counts[country.level] || 0) + 1;
            });

            document.getElementById('mapCounterText').innerHTML =
                `${data.length} countries loaded — ` +
                `<span style="color:#10B981">${counts.Low||0} Low</span> · ` +
                `<span style="color:#F59E0B">${counts.Medium||0} Medium</span> · ` +
                `<span style="color:#F97316">${counts.High||0} High</span> · ` +
                `<span style="color:#EF4444">${counts.Critical||0} Critical</span>`;
        })
        .catch(err => {
            console.error('Map load error:', err);
            document.getElementById('mapLoading').style.display = 'none';
            document.getElementById('mapCounterText').textContent = 'Failed to load map data.';
        });
}

function filterMapByRisk(level) {
    markerGroup.clearLayers();
    let shown = 0;
    allMarkers.forEach(m => {
        if (level === 'all' || m._countryData?.level === level) {
            m.addTo(markerGroup);
            shown++;
        }
    });
    if (level !== 'all') {
        document.getElementById('mapCounterText').textContent = `Showing ${shown} countries with ${level} risk`;
    }
}

function setTileLayer(type) {
    if (currentTile === type) return;
    tileLayers[currentTile].remove();
    tileLayers[type].addTo(map);
    map.eachLayer(l => { if (l !== tileLayers[type]) {} });
    // Re-add marker group on top
    markerGroup.addTo(map);
    currentTile = type;
    document.querySelectorAll('.tile-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tile' + type.charAt(0).toUpperCase() + type.slice(1))?.classList.add('active');
}

function refreshDashboard() {
    const icon = document.getElementById('refreshIcon');
    const btn  = document.getElementById('refreshBtn');
    icon.style.animation = 'spin 0.8s linear infinite';
    btn.disabled = true;
    markerGroup.clearLayers();
    allMarkers = [];
    allCountryData = {};
    loadMapData();
    setTimeout(() => {
        icon.style.animation = '';
        btn.disabled = false;
        showToast('Dashboard refreshed', 'success');
    }, 1800);
}

// =========================================
// COUNTRY PANEL
// =========================================
let panelCountryId = null;
let panelWatched   = false;

function loadCountryPanel(code) {
    const panel = document.getElementById('countryPanel');
    panel.classList.add('visible');

    // Quick fill from cache
    const cached = allCountryData[code];
    if (cached) {
        document.getElementById('panelName').textContent   = cached.name;
        document.getElementById('panelRegion').textContent = cached.region || '—';
        document.getElementById('panelScore').textContent  = cached.score?.toFixed(1) || '—';
        document.getElementById('panelScore').style.color  = cached.color;
        document.getElementById('panelFlag').src           = `https://flagcdn.com/w80/${code.toLowerCase()}.png`;
        document.getElementById('panelBadge').textContent  = cached.level;
        document.getElementById('panelBadge').className    = `risk-badge risk-${(cached.level||'').toLowerCase()}`;
    }

    // Load full data from API (all economic fields from economic_caches)
    fetch(`/api/map/country/${code}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('panelGdp').textContent       = data.gdp        || 'N/A';
            document.getElementById('panelInflation').textContent = data.inflation   || 'N/A';
            document.getElementById('panelPop').textContent       = data.population  || 'N/A';
            document.getElementById('panelExports').textContent   = data.exports     || 'N/A';
            document.getElementById('panelImports').textContent   = data.imports     || 'N/A';
            document.getElementById('panelWeather').textContent   = `${data.weather_icon || ''} ${data.temperature || 'N/A'}`;
            document.getElementById('panelDetailBtn').href        = data.detail_url;
            document.getElementById('panelCompareBtn').href       = data.compare_url;
            panelCountryId = data.id;
            panelWatched   = data.is_watched;
            updateWatchBtn();
        })
        .catch(() => {});
}

function closePanel() {
    document.getElementById('countryPanel').classList.remove('visible');
}

function updateWatchBtn() {
    const btn = document.getElementById('panelWatchBtn');
    if (!btn) return;
    btn.classList.toggle('active', panelWatched);
    btn.querySelector('i').className = `bi bi-star${panelWatched ? '-fill' : ''}`;
}

function toggleWatch() {
    if (!panelCountryId) return;
    fetch('/watchlist/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ country_id: panelCountryId }),
    })
    .then(r => r.json())
    .then(data => {
        panelWatched = data.action === 'added';
        updateWatchBtn();
        showToast(data.message, 'success');
    })
    .catch(() => showToast('Request failed', 'error'));
}

function flyToCountry(code) {
    const c = allCountryData[code];
    if (c) {
        map.flyTo([c.lat, c.lng], 5, { duration: 1.2 });
        loadCountryPanel(code);
    }
}

// =========================================
// CHART.JS — RISK DONUT
// =========================================
(function() {
    const ctx = document.getElementById('riskDonutChart');
    if (!ctx) return;

    const allLow      = {{ \App\Models\RiskScore::where('risk_level','Low')->count() }};
    const allMedium   = {{ \App\Models\RiskScore::where('risk_level','Medium')->count() }};
    const allHigh     = {{ \App\Models\RiskScore::where('risk_level','High')->count() }};
    const allCritical = {{ \App\Models\RiskScore::where('risk_level','Critical')->count() }};
    const noData      = {{ $totalCountries }} - allLow - allMedium - allHigh - allCritical;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Low', 'Medium', 'High', 'Critical', 'No Data'],
            datasets: [{
                data: [allLow, allMedium, allHigh, allCritical, Math.max(0, noData)],
                backgroundColor: ['#10B981', '#F59E0B', '#F97316', '#EF4444', '#374151'],
                borderColor: 'transparent',
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            cutout: '72%',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.95)',
                    borderColor: 'rgba(148,163,184,0.1)',
                    borderWidth: 1,
                    titleColor: '#F1F5F9',
                    bodyColor: '#94A3B8',
                    padding: 12,
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.raw} countries`,
                    }
                },
            },
        },
    });
})();

// =========================================
// CHART.JS — REGION HORIZONTAL BARS
// =========================================
(function() {
    const ctx = document.getElementById('regionBarChart');
    if (!ctx) return;

    const regionData = @json($regionRisk->whereNotNull('region')->take(8)->values());

    const labels = regionData.map(r => r.region);
    const scores = regionData.map(r => parseFloat(r.avg_risk || 0).toFixed(1));
    const colors = scores.map(s => {
        if (s > 75) return '#EF4444';
        if (s > 50) return '#F97316';
        if (s > 25) return '#F59E0B';
        return '#10B981';
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Avg Risk Score',
                data: scores,
                backgroundColor: colors.map(c => c + '33'),
                borderColor: colors,
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.95)',
                    borderColor: 'rgba(148,163,184,0.1)',
                    borderWidth: 1,
                    titleColor: '#F1F5F9',
                    bodyColor: '#94A3B8',
                    padding: 12,
                    callbacks: {
                        label: ctx => ` Avg Score: ${ctx.raw}`,
                    }
                },
            },
            scales: {
                x: {
                    min: 0,
                    max: 100,
                    grid: { color: 'rgba(148,163,184,0.06)' },
                    ticks: { color: '#64748B', font: { size: 11 } },
                    border: { color: 'transparent' },
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#94A3B8', font: { size: 11 } },
                    border: { color: 'transparent' },
                },
            },
        },
    });
})();

// =========================================
// INIT
// =========================================
loadMapData();
</script>
@endpush