@extends('layouts.admin')
@section('title', 'Countries')
@section('breadcrumb', 'Countries')

@section('content')

<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="bi bi-globe2 me-2" style="color:var(--admin-accent);"></i>Country Management</h1>
        <p>{{ $countries->total() }} countries — data sourced from APIs (read-only)</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn-admin btn-admin-ghost btn-admin-sm" onclick="refreshData('weather')" id="btn-weather">
            <i class="bi bi-cloud-sun"></i> Refresh Weather
        </button>
        <button class="btn-admin btn-admin-ghost btn-admin-sm" onclick="refreshData('economic')" id="btn-economic">
            <i class="bi bi-graph-up"></i> Refresh Economic
        </button>
        <button class="btn-admin btn-admin-ghost btn-admin-sm" onclick="refreshData('currency')" id="btn-currency">
            <i class="bi bi-currency-exchange"></i> Refresh Currency
        </button>
        <button class="btn-admin btn-admin-primary btn-admin-sm" onclick="refreshData('countries')" id="btn-countries">
            <i class="bi bi-arrow-clockwise"></i> Refresh Countries
        </button>
    </div>
</div>

{{-- FILTERS --}}
<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.countries') }}" class="d-flex gap-3 align-items-center flex-wrap">
        <div class="admin-search-wrap" style="flex:1;min-width:200px;">
            <i class="bi bi-search admin-search-icon"></i>
            <input type="text" name="search" class="admin-input" placeholder="Search country name or code..."
                   value="{{ $search ?? '' }}">
        </div>
        <select name="region" class="admin-input" style="width:auto;min-width:160px;">
            <option value="">All Regions</option>
            @foreach($regions as $r)
            <option value="{{ $r }}" {{ ($region ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>
        <select name="risk_level" class="admin-input" style="width:auto;min-width:140px;">
            <option value="">All Risk Levels</option>
            @foreach(['Low','Medium','High','Critical'] as $rl)
            <option value="{{ $rl }}" {{ ($riskLevel ?? '') === $rl ? 'selected' : '' }}>{{ $rl }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="bi bi-funnel"></i> Filter
        </button>
        @if($search || $region || $riskLevel)
        <a href="{{ route('admin.countries') }}" class="btn-admin btn-admin-ghost">
            <i class="bi bi-x"></i> Clear
        </a>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div class="admin-card" style="padding:0;">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Code</th>
                    <th>Region</th>
                    <th>Currency</th>
                    <th>Population</th>
                    <th>Risk Score</th>
                    <th>Risk Level</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $c)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="{{ $c->flag_url }}" width="24" height="16"
                                 style="border-radius:2px;object-fit:cover;" alt="{{ $c->country_code }}"
                                 onerror="this.style.display='none'">
                            <span style="font-weight:600;">{{ $c->country_name }}</span>
                        </div>
                    </td>
                    <td><code style="font-size:11px;color:var(--admin-accent);">{{ $c->country_code }}</code></td>
                    <td style="color:var(--admin-muted);font-size:12px;">{{ $c->region }}</td>
                    <td style="font-size:12px;">
                        <span style="color:var(--admin-muted);">{{ $c->currency_code }}</span>
                    </td>
                    <td style="font-size:12px;color:var(--admin-muted);">{{ $c->formatted_population }}</td>
                    <td>
                        @if($c->riskScore)
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="width:60px;height:5px;background:rgba(255,255,255,0.07);border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $c->riskScore->total_score }}%;background:{{ $c->riskScore->risk_color }};border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:700;color:{{ $c->riskScore->risk_color }};">
                                {{ number_format($c->riskScore->total_score, 1) }}
                            </span>
                        </div>
                        @else
                        <span style="color:var(--admin-muted);font-size:11px;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($c->riskScore)
                        <span class="admin-badge badge-{{ strtolower($c->riskScore->risk_level) }}">
                            {{ $c->riskScore->risk_level }}
                        </span>
                        @else
                        <span style="color:var(--admin-muted);font-size:11px;">N/A</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--admin-muted);">
                        <i class="bi bi-globe" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No countries found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($countries->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $countries->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function refreshData(type) {
    const btnMap = {
        countries: 'btn-countries',
        weather:   'btn-weather',
        economic:  'btn-economic',
        currency:  'btn-currency',
    };

    const urlMap = {
        countries: '{{ route("admin.countries.refresh") }}',
        weather:   '{{ route("admin.weather.refresh") }}',
        economic:  '{{ route("admin.economic.refresh") }}',
        currency:  '{{ route("admin.currency.refresh") }}',
    };

    const btn = document.getElementById(btnMap[type]);
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Refreshing...';

    fetch(urlMap[type], {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        adminToast(data.message, data.success ? 'success' : 'error');
    })
    .catch(() => adminToast('Request failed', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = origHtml;
    });
}
</script>
@endpush
