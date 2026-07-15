@extends('layouts.admin')
@section('title', 'API Monitor')
@section('breadcrumb', 'API Monitor')

@section('content')

<div class="admin-page-header">
    <h1><i class="bi bi-activity me-2" style="color:var(--admin-accent);"></i>API Monitor</h1>
    <p>Real-time status of all integrated external APIs</p>
</div>

{{-- API STATUS CARDS --}}
<div class="row g-4 mb-4">
    @foreach($apiStatus as $key => $api)
    <div class="col-md-6 col-xl-4">
        <div class="admin-card" style="position:relative;overflow:hidden;">
            {{-- Glow accent --}}
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:{{ $api['color'] }};"></div>

            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px;">
                <div style="width:48px;height:48px;border-radius:12px;background:{{ $api['color'] }}20;border:1px solid {{ $api['color'] }}30;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi {{ $api['icon'] }}" style="font-size:22px;color:{{ $api['color'] }};"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-size:15px;font-weight:700;color:var(--admin-text);">{{ $api['name'] }}</div>
                    <div style="font-size:11px;color:var(--admin-muted);">{{ $api['endpoint'] }}</div>
                </div>
                @if($api['is_success'] === true)
                    <span class="admin-badge badge-ok"><i class="bi bi-check-circle-fill"></i> Online</span>
                @elseif($api['is_success'] === false)
                    <span class="admin-badge badge-fail"><i class="bi bi-x-circle-fill"></i> Error</span>
                @else
                    <span class="admin-badge badge-neu"><i class="bi bi-dash-circle"></i> No Data</span>
                @endif
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div>
                    <div style="font-size:10px;color:var(--admin-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Last Sync</div>
                    <div style="font-size:12px;font-weight:600;">
                        {{ $api['last_sync'] ? $api['last_sync']->diffForHumans() : 'Never' }}
                    </div>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--admin-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Status Code</div>
                    <div style="font-size:12px;font-weight:600;color:{{ $api['is_success'] ? '#34D399' : '#FC8181' }};">
                        {{ $api['status_code'] ?? 'N/A' }}
                    </div>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--admin-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Total Calls</div>
                    <div style="font-size:12px;font-weight:600;">{{ number_format($api['total_calls']) }}</div>
                </div>
            </div>

            @if($api['response_ms'])
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--admin-border);">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:11px;color:var(--admin-muted);">Response Time</span>
                    <span style="font-size:12px;font-weight:600;color:{{ $api['color'] }};">{{ $api['response_ms'] }}ms</span>
                </div>
                <div style="height:4px;background:rgba(255,255,255,0.07);border-radius:3px;overflow:hidden;">
                    <div style="height:100%;width:{{ min(100, ($api['response_ms'] / 5000) * 100) }}%;background:{{ $api['color'] }};border-radius:3px;"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- RECENT API LOGS --}}
<div class="admin-card" style="padding:0;">
    <div class="admin-card-header" style="padding:20px;">
        <div class="admin-card-title"><i class="bi bi-clock-history"></i> Recent API Calls</div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>API</th>
                    <th>Endpoint</th>
                    <th>Status</th>
                    <th>Response Time</th>
                    <th>Requested At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                <tr>
                    <td style="font-weight:600;font-size:13px;">{{ str_replace('_', ' ', Str::title($log->api_name)) }}</td>
                    <td style="font-size:12px;color:var(--admin-muted);">
                        <code>{{ Str::limit($log->endpoint, 50) }}</code>
                    </td>
                    <td>
                        <span class="admin-badge {{ ($log->status_code >= 200 && $log->status_code < 300) ? 'badge-ok' : 'badge-fail' }}">
                            {{ $log->status_code }}
                        </span>
                    </td>
                    <td style="font-size:12px;">
                        <span style="color:{{ $log->response_time < 1000 ? '#34D399' : ($log->response_time < 3000 ? '#F59E0B' : '#FC8181') }};">
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
                        <i class="bi bi-activity" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No API calls logged yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
