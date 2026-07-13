@extends('layouts.dashboard')
@section('title', 'Admin — Risk Scores')
@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb"><a href="{{ route('admin.dashboard') }}">Admin</a><i class="bi bi-chevron-right"></i><span style="color:var(--accent)">Risk Scores</span></div>
        <h1>Risk Scores</h1><p>Manage and recalculate risk scores</p>
    </div>
</div>
@if(session('success'))<div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:var(--radius-md);padding:12px 16px;margin-bottom:16px;font-size:13px;color:var(--risk-low);">{{ session('success') }}</div>@endif
<div class="glass-card" style="overflow:hidden;">
    <table class="intel-table">
        <thead><tr><th>Country</th><th>Weather</th><th>Inflation</th><th>Currency</th><th>News</th><th>Total</th><th>Level</th><th>Calculated</th><th>Action</th></tr></thead>
        <tbody>
            @foreach($scores as $s)
            <tr id="row-{{ $s->country_id }}">
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <img src="https://flagcdn.com/w20/{{ strtolower($s->country?->country_code ?? 'xx') }}.png" width="18" height="12" style="border-radius:2px;" alt="">
                        <span style="font-size:13px;font-weight:600;">{{ $s->country?->country_name }}</span>
                    </div>
                </td>
                <td>{{ number_format($s->weather_score, 1) }}</td>
                <td>{{ number_format($s->inflation_score, 1) }}</td>
                <td>{{ number_format($s->currency_score, 1) }}</td>
                <td>{{ number_format($s->news_score, 1) }}</td>
                <td style="font-weight:700;color:{{ $s->risk_color }};">{{ number_format($s->total_score, 1) }}</td>
                <td><span class="risk-badge risk-{{ strtolower($s->risk_level) }}" style="font-size:10px;padding:2px 7px;">{{ $s->risk_level }}</span></td>
                <td style="font-size:11px;color:var(--text-muted);">{{ $s->calculated_at?->diffForHumans() ?? '—' }}</td>
                <td>
                    <button onclick="recalculate({{ $s->country_id }}, '{{ $s->country?->country_code }}')"
                        class="btn-ghost" style="padding:4px 10px;font-size:11px;" id="btn-{{ $s->country_id }}">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:16px;border-top:1px solid var(--border);">{{ $scores->links() }}</div>
</div>
@endsection
@push('scripts')
<script>
function recalculate(countryId, code) {
    const btn = document.getElementById(`btn-${countryId}`);
    btn.disabled = true;
    btn.innerHTML = '<div class="intel-spinner" style="width:12px;height:12px;display:inline-block;"></div>';
    fetch(`/admin/risk-scores/recalculate/${countryId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(`Recalculated: ${data.total_score} — ${data.risk_level}`, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(() => showToast('Recalculation failed', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
    });
}
</script>
@endpush
