@extends('layouts.admin')
@section('title', 'Risk Monitoring')
@section('breadcrumb', 'Risk Monitoring')

@section('content')

<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="bi bi-shield-exclamation me-2" style="color:var(--admin-accent);"></i>Risk Monitoring</h1>
        <p>{{ $scores->total() }} countries with risk assessments</p>
    </div>
    <button class="btn-admin btn-admin-primary" onclick="recalculateAll()" id="btn-recalc-all">
        <i class="bi bi-arrow-repeat"></i> Recalculate All
    </button>
</div>

{{-- AVG SCORE CARDS --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Avg Weather Score', 'value' => $avgScores['weather'], 'icon' => 'bi-cloud-lightning', 'color' => '#38BDF8'],
        ['label' => 'Avg Inflation Score', 'value' => $avgScores['inflation'], 'icon' => 'bi-graph-up-arrow', 'color' => '#F59E0B'],
        ['label' => 'Avg Currency Score', 'value' => $avgScores['currency'], 'icon' => 'bi-currency-exchange', 'color' => '#34D399'],
        ['label' => 'Avg News Score', 'value' => $avgScores['news'], 'icon' => 'bi-newspaper', 'color' => '#F87171'],
    ] as $avg)
    <div class="col-6 col-lg-3">
        <div class="admin-card" style="text-align:center;padding:16px;">
            <i class="bi {{ $avg['icon'] }}" style="font-size:22px;color:{{ $avg['color'] }};display:block;margin-bottom:8px;"></i>
            <div style="font-size:22px;font-weight:800;color:{{ $avg['color'] }};">{{ $avg['value'] }}</div>
            <div style="font-size:11px;color:var(--admin-muted);margin-top:4px;">{{ $avg['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- FILTERS --}}
<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.risk-scores') }}" class="d-flex gap-3 align-items-center flex-wrap">
        <div class="admin-search-wrap" style="flex:1;min-width:200px;">
            <i class="bi bi-search admin-search-icon"></i>
            <input type="text" name="search" class="admin-input" placeholder="Search country..."
                   value="{{ $search ?? '' }}">
        </div>
        <select name="risk_level" class="admin-input" style="width:auto;min-width:140px;">
            <option value="">All Risk Levels</option>
            @foreach(['Low','Medium','High','Critical'] as $rl)
            <option value="{{ $rl }}" {{ ($riskLevel ?? '') === $rl ? 'selected' : '' }}>{{ $rl }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-admin btn-admin-primary"><i class="bi bi-funnel"></i> Filter</button>
        @if($search || $riskLevel)
        <a href="{{ route('admin.risk-scores') }}" class="btn-admin btn-admin-ghost"><i class="bi bi-x"></i> Clear</a>
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
                    <th>Weather</th>
                    <th>Inflation</th>
                    <th>Currency</th>
                    <th>News</th>
                    <th>Total Score</th>
                    <th>Risk Level</th>
                    <th>Last Calc</th>
                    <th>Recalculate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scores as $s)
                <tr id="row-{{ $s->country_id }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <img src="https://flagcdn.com/w20/{{ strtolower($s->country?->country_code ?? 'xx') }}.png"
                                 width="18" height="12" style="border-radius:2px;" alt="">
                            <span style="font-weight:600;font-size:13px;">{{ $s->country?->country_name }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="width:40px;height:4px;background:rgba(255,255,255,0.07);border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $s->weather_score }}%;background:#38BDF8;border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;">{{ number_format($s->weather_score, 1) }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="width:40px;height:4px;background:rgba(255,255,255,0.07);border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $s->inflation_score }}%;background:#F59E0B;border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;">{{ number_format($s->inflation_score, 1) }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="width:40px;height:4px;background:rgba(255,255,255,0.07);border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $s->currency_score }}%;background:#34D399;border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;">{{ number_format($s->currency_score, 1) }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="width:40px;height:4px;background:rgba(255,255,255,0.07);border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $s->news_score }}%;background:#F87171;border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;">{{ number_format($s->news_score, 1) }}</span>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:16px;font-weight:800;color:{{ $s->risk_color }};">
                            {{ number_format($s->total_score, 1) }}
                        </span>
                    </td>
                    <td>
                        <span class="admin-badge badge-{{ strtolower($s->risk_level) }}">
                            {{ $s->risk_level }}
                        </span>
                    </td>
                    <td style="font-size:11px;color:var(--admin-muted);">
                        {{ $s->calculated_at?->diffForHumans() ?? '—' }}
                    </td>
                    <td>
                        <button class="btn-admin btn-admin-ghost btn-admin-sm btn-admin-icon"
                                id="btn-{{ $s->country_id }}"
                                title="Recalculate"
                                onclick="recalculate({{ $s->country_id }})">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($scores->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $scores->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function recalculate(countryId) {
    const btn = document.getElementById(`btn-${countryId}`);
    btn.disabled = true;
    btn.querySelector('i').className = 'bi bi-hourglass-split';

    fetch(`/admin/risk-scores/recalculate/${countryId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            adminToast(`Score: ${data.total_score} — ${data.risk_level}`, 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            adminToast('Recalculation failed', 'error');
        }
    })
    .catch(() => adminToast('Request failed', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.querySelector('i').className = 'bi bi-arrow-clockwise';
    });
}

function recalculateAll() {
    const btn = document.getElementById('btn-recalc-all');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Calculating...';

    fetch('{{ route("admin.risk-scores.recalculate-all") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        adminToast(data.message, data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 1500);
    })
    .catch(() => adminToast('Request failed', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Recalculate All';
    });
}
</script>
@endpush
