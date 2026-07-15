@extends('layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

{{-- PAGE HEADER --}}
<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="bi bi-speedometer2 me-2" style="color:var(--admin-accent);"></i>Admin Dashboard</h1>
        <p>System overview and monitoring — {{ now()->format('D, d M Y') }}</p>
    </div>
    <a href="{{ route('admin.risk-scores') }}" class="btn-admin btn-admin-primary">
        <i class="bi bi-shield-exclamation"></i> Risk Monitoring
    </a>
</div>

{{-- STATS GRID --}}
<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <span class="admin-stat-icon">👥</span>
        <div class="admin-stat-value">{{ number_format($stats['users']) }}</div>
        <div class="admin-stat-label">Total Users</div>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-icon">🌍</span>
        <div class="admin-stat-value">{{ number_format($stats['countries']) }}</div>
        <div class="admin-stat-label">Countries</div>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-icon">⚓</span>
        <div class="admin-stat-value">{{ number_format($stats['ports']) }}</div>
        <div class="admin-stat-label">Ports</div>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-icon">📄</span>
        <div class="admin-stat-value">{{ number_format($stats['articles']) }}</div>
        <div class="admin-stat-label">Articles</div>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-icon">📰</span>
        <div class="admin-stat-value">{{ number_format($stats['news_cache']) }}</div>
        <div class="admin-stat-label">Cached News</div>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-icon">⭐</span>
        <div class="admin-stat-value">{{ number_format($stats['watchlists']) }}</div>
        <div class="admin-stat-label">Watchlists</div>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-icon">⚠️</span>
        <div class="admin-stat-value">{{ $stats['avg_risk'] }}</div>
        <div class="admin-stat-label">Avg Risk Score</div>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-icon">🎯</span>
        <div class="admin-stat-value">{{ number_format($stats['risk_scores']) }}</div>
        <div class="admin-stat-label">Risk Assessed</div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="row g-4 mb-4">
    {{-- Risk Distribution Doughnut --}}
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <div class="admin-card-title"><i class="bi bi-pie-chart-fill"></i> Risk Distribution</div>
            </div>
            <div class="admin-chart-container">
                <canvas id="riskDistChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Countries by Region Bar --}}
    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <div class="admin-card-title"><i class="bi bi-bar-chart-fill"></i> Countries by Region</div>
            </div>
            <div class="admin-chart-container">
                <canvas id="regionChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- BOTTOM ROW --}}
<div class="row g-4">

    {{-- Top Risk Countries --}}
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title"><i class="bi bi-exclamation-triangle-fill"></i> Top Risk Countries</div>
                <a href="{{ route('admin.risk-scores') }}" class="btn-admin btn-admin-ghost btn-admin-sm">View All</a>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Score</th>
                            <th>Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topRisk as $rs)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <img src="https://flagcdn.com/w20/{{ strtolower($rs->country?->country_code ?? 'xx') }}.png"
                                         width="18" height="12" style="border-radius:2px;" alt="">
                                    <span style="font-weight:600;">{{ $rs->country?->country_name }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="flex:1;height:6px;background:rgba(255,255,255,0.07);border-radius:3px;overflow:hidden;">
                                        <div style="height:100%;width:{{ $rs->total_score }}%;background:{{ $rs->risk_color }};border-radius:3px;"></div>
                                    </div>
                                    <span style="font-weight:700;color:{{ $rs->risk_color }};font-size:13px;">{{ number_format($rs->total_score, 1) }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="admin-badge badge-{{ strtolower($rs->risk_level) }}">{{ $rs->risk_level }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4" style="color:var(--admin-muted);">No risk data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Users + Latest News --}}
    <div class="col-lg-6">
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div class="admin-card-title"><i class="bi bi-people-fill"></i> Recent Users</div>
                <a href="{{ route('admin.users') }}" class="btn-admin btn-admin-ghost btn-admin-sm">View All</a>
            </div>
            @forelse($recentUsers as $u)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--admin-border);">
                <div class="admin-avatar" style="width:32px;height:32px;font-size:12px;">
                    {{ strtoupper(substr($u->name, 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;">{{ $u->name }}</div>
                    <div style="font-size:11px;color:var(--admin-muted);">{{ $u->email }}</div>
                </div>
                <span class="admin-badge {{ $u->role_id === 1 ? 'badge-admin' : 'badge-user' }}">
                    {{ $u->role_id === 1 ? 'Admin' : 'User' }}
                </span>
                <span style="font-size:11px;color:var(--admin-muted);">{{ $u->created_at->diffForHumans() }}</span>
            </div>
            @empty
            <p style="color:var(--admin-muted);font-size:13px;text-align:center;padding:20px 0;">No users found.</p>
            @endforelse
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title"><i class="bi bi-newspaper"></i> Latest News</div>
                <a href="{{ route('admin.news-cache') }}" class="btn-admin btn-admin-ghost btn-admin-sm">View All</a>
            </div>
            @forelse($latestNews as $n)
            <div style="padding:10px 0;border-bottom:1px solid var(--admin-border);">
                <div style="font-size:12px;font-weight:600;line-height:1.4;margin-bottom:4px;">
                    {{ Str::limit($n->title, 65) }}
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="admin-badge badge-{{ strtolower($n->sentiment ?? 'neu') }}" style="font-size:10px;">
                        {{ $n->sentiment ?? 'Neutral' }}
                    </span>
                    <span style="font-size:11px;color:var(--admin-muted);">{{ $n->source }}</span>
                    <span style="font-size:11px;color:var(--admin-muted);margin-left:auto;">{{ $n->published_at?->diffForHumans() }}</span>
                </div>
            </div>
            @empty
            <p style="color:var(--admin-muted);font-size:13px;text-align:center;padding:20px 0;">No news cached yet.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// ===== Risk Distribution Doughnut =====
const riskDistData = @json($riskDist);
const riskColors = { Low: '#10B981', Medium: '#F59E0B', High: '#F97316', Critical: '#EF4444' };

new Chart(document.getElementById('riskDistChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(riskDistData),
        datasets: [{
            data: Object.values(riskDistData),
            backgroundColor: Object.keys(riskDistData).map(k => riskColors[k] || '#6B7280'),
            borderWidth: 0,
            hoverOffset: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#94A3B8', font: { size: 11 }, padding: 12 }
            },
            tooltip: {
                backgroundColor: '#1A2236',
                titleColor: '#E2E8F0',
                bodyColor: '#94A3B8',
                borderColor: 'rgba(255,255,255,0.07)',
                borderWidth: 1,
            }
        }
    }
});

// ===== Countries by Region Bar =====
const regionData = @json($regionDist);
new Chart(document.getElementById('regionChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(regionData),
        datasets: [{
            label: 'Countries',
            data: Object.values(regionData),
            backgroundColor: 'rgba(139,92,246,0.6)',
            borderColor: 'rgba(139,92,246,1)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1A2236',
                titleColor: '#E2E8F0',
                bodyColor: '#94A3B8',
                borderColor: 'rgba(255,255,255,0.07)',
                borderWidth: 1,
            }
        },
        scales: {
            x: {
                ticks: { color: '#64748B', font: { size: 10 } },
                grid: { color: 'rgba(255,255,255,0.04)' },
            },
            y: {
                ticks: { color: '#64748B', font: { size: 11 } },
                grid: { color: 'rgba(255,255,255,0.04)' },
            }
        }
    }
});
</script>
@endpush
