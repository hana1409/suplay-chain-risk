@extends('layouts.dashboard')
@section('title', 'Watchlist — My Countries')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">Watchlist</span>
        </div>
        <h1>My Watchlist</h1>
        <p>{{ $watchlists->count() }} countries monitored</p>
    </div>
    <a href="{{ route('countries') }}" class="btn-primary-custom">
        <i class="bi bi-plus-circle"></i> Add Countries
    </a>
</div>

@if($watchlists->isEmpty())
<div class="glass-card" style="padding:80px;text-align:center;">
    <div style="font-size:64px;margin-bottom:16px;">⭐</div>
    <div style="font-size:20px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">Your watchlist is empty</div>
    <div style="font-size:14px;color:var(--text-muted);margin-bottom:24px;">Add countries from the map or country pages to monitor them here.</div>
    <a href="{{ route('dashboard') }}" class="btn-primary-custom">
        <i class="bi bi-globe-americas"></i> Explore Map
    </a>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
    @foreach($watchlists as $wl)
    @php $c = $wl->country; $risk = $c?->riskScore; @endphp
    <div class="glass-card" style="padding:20px;position:relative;">
        {{-- Remove Button --}}
        <button onclick="removeWatch({{ $c?->id }}, this)"
            style="position:absolute;top:12px;right:12px;background:transparent;border:none;color:var(--text-muted);cursor:pointer;font-size:16px;padding:4px;border-radius:6px;transition:color 0.2s;"
            title="Remove from watchlist"
            onmouseover="this.style.color='var(--risk-critical)'"
            onmouseout="this.style.color='var(--text-muted)'">
            <i class="bi bi-x-circle"></i>
        </button>

        {{-- Country Info --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <img src="https://flagcdn.com/w40/{{ strtolower($c?->country_code ?? 'xx') }}.png"
                 width="48" height="32"
                 style="border-radius:4px;border:1px solid var(--border);object-fit:cover;" alt="">
            <div>
                <div style="font-weight:700;font-size:15px;color:var(--text-primary);">{{ $c?->country_name ?? '—' }}</div>
                <div style="font-size:12px;color:var(--text-muted);">{{ $c?->region }} · {{ $c?->capital }}</div>
            </div>
        </div>

        {{-- Risk Score --}}
        @if($risk)
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding:10px;background:var(--bg-base);border-radius:var(--radius-sm);border:1px solid var(--border);">
            <div>
                <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Risk Score</div>
                <div style="font-size:22px;font-weight:800;color:{{ $risk->risk_color }};">{{ number_format($risk->total_score, 1) }}</div>
            </div>
            <span class="risk-badge risk-{{ strtolower($risk->risk_level) }}">{{ $risk->risk_level }}</span>
        </div>
        @endif

        {{-- Economic Data --}}
        @php $eco = $c?->economicCache; @endphp
        @if($eco)
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
            <div style="font-size:11px;color:var(--text-muted);">GDP</div>
            <div style="font-size:11px;font-weight:600;color:var(--text-primary);text-align:right;">{{ $eco->formatted_gdp }}</div>
            <div style="font-size:11px;color:var(--text-muted);">Inflation</div>
            <div style="font-size:11px;font-weight:600;color:var(--text-primary);text-align:right;">{{ number_format($eco->inflation ?? 0, 2) }}%</div>
        </div>
        @endif

        {{-- Actions --}}
        <div style="display:flex;gap:8px;">
            <a href="{{ route('countries.show', $c?->country_code ?? '#') }}" class="btn-primary-custom" style="flex:1;justify-content:center;font-size:12px;padding:8px;">
                <i class="bi bi-eye"></i> View
            </a>
            <a href="{{ route('compare') }}?a={{ $c?->country_code }}" class="btn-ghost" style="flex:1;justify-content:center;font-size:12px;padding:8px;">
                <i class="bi bi-bar-chart-steps"></i> Compare
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection

@push('scripts')
<script>
function removeWatch(countryId, btn) {
    if (!countryId) return;
    fetch('/watchlist/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ country_id: countryId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.action === 'removed') {
            btn.closest('.glass-card').style.transition = 'opacity 0.3s,transform 0.3s';
            btn.closest('.glass-card').style.opacity = '0';
            btn.closest('.glass-card').style.transform = 'scale(0.95)';
            setTimeout(() => btn.closest('.glass-card').remove(), 300);
            showToast('Removed from watchlist', 'success');
        }
    })
    .catch(() => showToast('Request failed', 'error'));
}
</script>
@endpush
