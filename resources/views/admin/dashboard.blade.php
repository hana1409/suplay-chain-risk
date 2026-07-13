@extends('layouts.dashboard')
@section('title', 'Admin Panel')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">Admin Panel</span>
        </div>
        <h1>Administration</h1>
        <p>System management and monitoring</p>
    </div>
</div>

{{-- STATS GRID --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👤</div>
        <div class="stat-value">{{ $stats['users'] }}</div>
        <div class="stat-label">Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🌍</div>
        <div class="stat-value">{{ $stats['countries'] }}</div>
        <div class="stat-label">Countries</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⚓</div>
        <div class="stat-value">{{ number_format($stats['ports']) }}</div>
        <div class="stat-label">Ports</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📰</div>
        <div class="stat-value">{{ $stats['news_cache'] }}</div>
        <div class="stat-label">Cached News</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⚠️</div>
        <div class="stat-value">{{ $stats['risk_scores'] }}</div>
        <div class="stat-label">Risk Scores</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📄</div>
        <div class="stat-value">{{ $stats['articles'] }}</div>
        <div class="stat-label">Articles</div>
    </div>
</div>

<div class="two-col-grid" style="margin-top:var(--space-xl);">
    {{-- Recent Users --}}
    <div class="glass-card" style="padding:20px;">
        <div class="section-header" style="margin-bottom:16px;">
            <div class="section-title"><span></span> Recent Users</div>
            <a href="{{ route('admin.users') }}" style="font-size:12px;color:var(--accent);text-decoration:none;">View All</a>
        </div>
        <table class="intel-table">
            <thead><tr><th>Name</th><th>Email</th><th>Joined</th></tr></thead>
            <tbody>
                @foreach($recentUsers as $u)
                <tr>
                    <td style="font-weight:600;">{{ $u->name }}</td>
                    <td style="color:var(--text-muted);">{{ $u->email }}</td>
                    <td style="color:var(--text-muted);">{{ $u->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Top Risk --}}
    <div class="glass-card" style="padding:20px;">
        <div class="section-header" style="margin-bottom:16px;">
            <div class="section-title"><span></span> Top Risk Countries</div>
            <a href="{{ route('admin.risk-scores') }}" style="font-size:12px;color:var(--accent);text-decoration:none;">View All</a>
        </div>
        @foreach($topRisk as $rs)
        <div style="display:flex;align-items:center;gap:12px;padding:8px;border-bottom:1px solid var(--border);">
            <img src="https://flagcdn.com/w20/{{ strtolower($rs->country?->country_code ?? 'xx') }}.png" width="20" height="14" style="border-radius:2px;" alt="">
            <span style="flex:1;font-size:13px;color:var(--text-primary);">{{ $rs->country?->country_name }}</span>
            <span style="font-size:13px;font-weight:700;color:{{ $rs->risk_color }};">{{ number_format($rs->total_score, 1) }}</span>
            <span class="risk-badge risk-{{ strtolower($rs->risk_level) }}" style="font-size:10px;padding:2px 7px;">{{ $rs->risk_level }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- ADMIN LINKS --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-top:var(--space-xl);">
    @foreach([
        ['admin.users', 'bi-people-fill', 'Manage Users', 'Manage user accounts'],
        ['admin.ports', 'bi-anchor', 'Manage Ports', 'Add/Edit/Delete ports'],
        ['admin.articles', 'bi-file-text-fill', 'Manage Articles', 'Blog & research articles'],
        ['admin.news-cache', 'bi-newspaper', 'News Cache', 'Cached news articles'],
        ['admin.risk-scores', 'bi-shield-exclamation', 'Risk Scores', 'Recalculate risk scores'],
    ] as [$routeName, $icon, $title, $desc])
    <a href="{{ route($routeName) }}" class="glass-card" style="padding:20px;text-decoration:none;transition:var(--transition);" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor=''">
        <i class="bi {{ $icon }}" style="font-size:24px;color:var(--accent);display:block;margin-bottom:10px;"></i>
        <div style="font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:4px;">{{ $title }}</div>
        <div style="font-size:12px;color:var(--text-muted);">{{ $desc }}</div>
    </a>
    @endforeach
</div>

@endsection
