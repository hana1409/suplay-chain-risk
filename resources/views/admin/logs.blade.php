@extends('layouts.admin')
@section('title', 'System Logs')
@section('breadcrumb', 'System Logs')

@section('content')

<div class="admin-page-header">
    <h1><i class="bi bi-journal-text me-2" style="color:var(--admin-accent);"></i>System Logs</h1>
    <p>Comparison logs, watchlist activity, and API synchronization history</p>
</div>

{{-- TABS --}}
<ul class="nav nav-tabs mb-4" id="logTabs" style="border-bottom:1px solid var(--admin-border);">
    @foreach([
        ['comparison', 'bi-bar-chart-steps', 'Comparison Logs'],
        ['watchlist',  'bi-star-fill',        'Watchlist Logs'],
        ['api',        'bi-cloud-arrow-up',   'API Sync Logs'],
    ] as [$tabKey, $icon, $label])
    <li class="nav-item">
        <a href="?tab={{ $tabKey }}"
           class="nav-link {{ $tab === $tabKey ? 'active' : '' }}"
           style="background:transparent;border:none;border-bottom:2px solid {{ $tab === $tabKey ? 'var(--admin-accent)' : 'transparent' }};
                  color:{{ $tab === $tabKey ? 'var(--admin-accent)' : 'var(--admin-muted)' }};
                  padding:10px 20px;font-size:13px;font-weight:600;transition:all 0.2s;">
            <i class="bi {{ $icon }} me-2"></i>{{ $label }}
        </a>
    </li>
    @endforeach
</ul>

{{-- COMPARISON LOGS --}}
@if($tab === 'comparison')
<div class="admin-card" style="padding:0;">
    <div class="admin-card-header" style="padding:20px;">
        <div class="admin-card-title"><i class="bi bi-bar-chart-steps"></i> Comparison Logs</div>
        <span style="font-size:12px;color:var(--admin-muted);">{{ $comparisonLogs->total() }} records</span>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Country A</th>
                    <th>Country B</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comparisonLogs as $log)
                <tr>
                    <td style="color:var(--admin-muted);font-size:12px;">{{ $log->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="admin-avatar" style="width:28px;height:28px;font-size:11px;">
                                {{ strtoupper(substr($log->user?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size:12px;font-weight:600;">{{ $log->user?->name ?? 'Deleted' }}</div>
                                <div style="font-size:11px;color:var(--admin-muted);">{{ $log->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <img src="https://flagcdn.com/w20/{{ strtolower($log->countryOne?->country_code ?? 'xx') }}.png"
                                 width="16" height="11" style="border-radius:2px;" alt="">
                            <span style="font-size:12px;font-weight:600;">{{ $log->countryOne?->country_name ?? '—' }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <img src="https://flagcdn.com/w20/{{ strtolower($log->countryTwo?->country_code ?? 'xx') }}.png"
                                 width="16" height="11" style="border-radius:2px;" alt="">
                            <span style="font-size:12px;font-weight:600;">{{ $log->countryTwo?->country_name ?? '—' }}</span>
                        </div>
                    </td>
                    <td style="font-size:11px;color:var(--admin-muted);">{{ $log->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:var(--admin-muted);">
                        No comparison logs yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($comparisonLogs->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $comparisonLogs->appends(['tab' => 'comparison'])->links() }}
    </div>
    @endif
</div>
@endif

{{-- WATCHLIST LOGS --}}
@if($tab === 'watchlist')
<div class="admin-card" style="padding:0;">
    <div class="admin-card-header" style="padding:20px;">
        <div class="admin-card-title"><i class="bi bi-star-fill"></i> Watchlist Logs</div>
        <span style="font-size:12px;color:var(--admin-muted);">{{ $watchlistLogs->total() }} entries</span>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Country</th>
                    <th>Added On</th>
                </tr>
            </thead>
            <tbody>
                @forelse($watchlistLogs as $wl)
                <tr>
                    <td style="color:var(--admin-muted);font-size:12px;">{{ $wl->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="admin-avatar" style="width:28px;height:28px;font-size:11px;">
                                {{ strtoupper(substr($wl->user?->name ?? 'U', 0, 1)) }}
                            </div>
                            <span style="font-size:12px;font-weight:600;">{{ $wl->user?->name ?? 'Deleted' }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <img src="https://flagcdn.com/w20/{{ strtolower($wl->country?->country_code ?? 'xx') }}.png"
                                 width="16" height="11" style="border-radius:2px;" alt="">
                            <span style="font-size:12px;font-weight:600;">{{ $wl->country?->country_name ?? '—' }}</span>
                        </div>
                    </td>
                    <td style="font-size:11px;color:var(--admin-muted);">{{ $wl->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:48px;color:var(--admin-muted);">
                        No watchlist entries yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($watchlistLogs->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $watchlistLogs->appends(['tab' => 'watchlist'])->links() }}
    </div>
    @endif
</div>
@endif

{{-- API SYNC LOGS --}}
@if($tab === 'api')
<div class="admin-card" style="padding:0;">
    <div class="admin-card-header" style="padding:20px;">
        <div class="admin-card-title"><i class="bi bi-cloud-arrow-up"></i> API Sync Logs</div>
        <span style="font-size:12px;color:var(--admin-muted);">{{ $apiLogs->total() }} records</span>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>API</th>
                    <th>Endpoint</th>
                    <th>Status</th>
                    <th>Response Time</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($apiLogs as $log)
                <tr>
                    <td style="font-weight:600;font-size:13px;">{{ str_replace('_', ' ', Str::title($log->api_name)) }}</td>
                    <td style="font-size:12px;color:var(--admin-muted);">
                        <code>{{ Str::limit($log->endpoint, 60) }}</code>
                    </td>
                    <td>
                        <span class="admin-badge {{ ($log->status_code >= 200 && $log->status_code < 300) ? 'badge-ok' : 'badge-fail' }}">
                            {{ $log->status_code }}
                        </span>
                    </td>
                    <td style="font-size:12px;">
                        <span style="color:{{ $log->response_time < 1000 ? '#34D399' : '#FC8181' }};">
                            {{ $log->response_time }}ms
                        </span>
                    </td>
                    <td style="font-size:11px;color:var(--admin-muted);">
                        {{ $log->requested_at?->format('d M Y H:i:s') ?? $log->created_at->format('d M Y H:i:s') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:var(--admin-muted);">
                        No API logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($apiLogs->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $apiLogs->appends(['tab' => 'api'])->links() }}
    </div>
    @endif
</div>
@endif

@endsection
