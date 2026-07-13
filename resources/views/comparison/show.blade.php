@extends('layouts.dashboard')
@section('title', $countryA->country_name . ' vs ' . $countryB->country_name)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('compare') }}">Comparison</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">{{ $countryA->country_name }} vs {{ $countryB->country_name }}</span>
        </div>
        <h1>Country Comparison</h1>
        <p>Side-by-side risk analysis and decision support</p>
    </div>
    <a href="{{ route('compare') }}" class="btn-ghost">
        <i class="bi bi-arrow-left"></i> New Comparison
    </a>
</div>

{{-- SIDE BY SIDE HEADERS --}}
<div style="display:grid;grid-template-columns:1fr auto 1fr;gap:20px;margin-bottom:var(--space-xl);">
    {{-- Country A --}}
    <div class="glass-card" style="padding:24px;text-align:center;">
        <img src="https://flagcdn.com/w80/{{ strtolower($countryA->country_code) }}.png"
             style="width:80px;height:54px;object-fit:cover;border-radius:8px;border:2px solid var(--border);margin-bottom:12px;" alt="">
        <h2 style="font-size:20px;font-weight:800;margin:0 0 4px;">{{ $countryA->country_name }}</h2>
        <div style="font-size:13px;color:var(--text-muted);">{{ $countryA->region }}</div>
        <div style="font-size:36px;font-weight:800;color:{{ $dataA['risk']->risk_color }};margin:12px 0 4px;">
            {{ number_format($dataA['risk']->total_score, 1) }}
        </div>
        <span class="risk-badge risk-{{ strtolower($dataA['risk']->risk_level) }}">{{ $dataA['risk']->risk_level }}</span>
    </div>

    {{-- VS --}}
    <div style="display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:var(--text-muted);">VS</div>

    {{-- Country B --}}
    <div class="glass-card" style="padding:24px;text-align:center;">
        <img src="https://flagcdn.com/w80/{{ strtolower($countryB->country_code) }}.png"
             style="width:80px;height:54px;object-fit:cover;border-radius:8px;border:2px solid var(--border);margin-bottom:12px;" alt="">
        <h2 style="font-size:20px;font-weight:800;margin:0 0 4px;">{{ $countryB->country_name }}</h2>
        <div style="font-size:13px;color:var(--text-muted);">{{ $countryB->region }}</div>
        <div style="font-size:36px;font-weight:800;color:{{ $dataB['risk']->risk_color }};margin:12px 0 4px;">
            {{ number_format($dataB['risk']->total_score, 1) }}
        </div>
        <span class="risk-badge risk-{{ strtolower($dataB['risk']->risk_level) }}">{{ $dataB['risk']->risk_level }}</span>
    </div>
</div>

{{-- COMPARISON TABLE --}}
<div class="glass-card" style="overflow:hidden;margin-bottom:var(--space-xl);">
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
        <div class="section-title"><span></span> Detailed Comparison</div>
    </div>
    <table class="intel-table">
        <thead>
            <tr>
                <th>Metric</th>
                <th style="text-align:center;">{{ $countryA->country_name }}</th>
                <th style="text-align:center;">{{ $countryB->country_name }}</th>
                <th style="text-align:center;">Winner</th>
            </tr>
        </thead>
        <tbody>
            @php
            $rows = [
                ['Overall Risk',    $dataA['risk']->total_score,     $dataB['risk']->total_score,     'lower'],
                ['Weather Risk',    $dataA['risk']->weather_score,   $dataB['risk']->weather_score,   'lower'],
                ['Inflation Risk',  $dataA['risk']->inflation_score, $dataB['risk']->inflation_score, 'lower'],
                ['Currency Risk',   $dataA['risk']->currency_score,  $dataB['risk']->currency_score,  'lower'],
                ['News Risk',       $dataA['risk']->news_score,      $dataB['risk']->news_score,      'lower'],
                ['GDP (B USD)',     $dataA['economic'] ? round($dataA['economic']->gdp / 1e9, 1) : 0,
                                   $dataB['economic'] ? round($dataB['economic']->gdp / 1e9, 1) : 0, 'higher'],
                ['Inflation %',     $dataA['economic']?->inflation ?? 0, $dataB['economic']?->inflation ?? 0, 'lower'],
                ['Temperature °C', $dataA['weather']?->temperature ?? 0, $dataB['weather']?->temperature ?? 0, 'moderate'],
            ];
            @endphp

            @foreach($rows as $row)
            @php
            $label = $row[0];
            $valA  = (float)$row[1];
            $valB  = (float)$row[2];
            $prefer = $row[3];

            if ($prefer === 'lower') {
                $winner = $valA < $valB ? 'A' : ($valB < $valA ? 'B' : '=');
            } elseif ($prefer === 'higher') {
                $winner = $valA > $valB ? 'A' : ($valB > $valA ? 'B' : '=');
            } else {
                $winner = '=';
            }

            $aStyle = $winner === 'A' ? 'color:var(--risk-low);font-weight:700;' : '';
            $bStyle = $winner === 'B' ? 'color:var(--risk-low);font-weight:700;' : '';
            @endphp
            <tr>
                <td style="font-weight:600;font-size:13px;">{{ $label }}</td>
                <td style="text-align:center;{{ $aStyle }}">{{ number_format($valA, 2) }}</td>
                <td style="text-align:center;{{ $bStyle }}">{{ number_format($valB, 2) }}</td>
                <td style="text-align:center;">
                    @if($winner === 'A')
                        <span style="color:var(--risk-low);font-weight:700;font-size:12px;">✓ {{ $countryA->country_code }}</span>
                    @elseif($winner === 'B')
                        <span style="color:var(--risk-low);font-weight:700;font-size:12px;">✓ {{ $countryB->country_code }}</span>
                    @else
                        <span style="color:var(--text-muted);font-size:12px;">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- RADAR CHART --}}
<div class="two-col-grid" style="margin-bottom:var(--space-xl);">
    <div class="glass-card" style="padding:20px;">
        <div class="section-header" style="margin-bottom:16px;">
            <div class="section-title"><span></span> Risk Radar Chart</div>
        </div>
        <div class="chart-container" style="height:280px;">
            <canvas id="compareRadar"></canvas>
        </div>
    </div>
    <div class="glass-card" style="padding:20px;">
        <div class="section-header" style="margin-bottom:16px;">
            <div class="section-title"><span></span> Risk Score Breakdown</div>
        </div>
        <div class="chart-container" style="height:280px;">
            <canvas id="compareBar"></canvas>
        </div>
    </div>
</div>

{{-- RECOMMENDATIONS --}}
<div class="two-col-grid">
    <div class="recommendation-card {{ strtolower($recommendationA['level']) }}">
        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">{{ $countryA->country_name }}</div>
        <div style="font-size:18px;font-weight:800;color:{{ $recommendationA['color'] }};margin-bottom:12px;">
            {{ $recommendationA['action'] }}
        </div>
        @foreach($recommendationA['positive'] as $p)
        <div style="font-size:12px;color:var(--risk-low);margin-bottom:4px;"><i class="bi bi-check-circle-fill"></i> {{ $p }}</div>
        @endforeach
        @foreach($recommendationA['negative'] as $n)
        <div style="font-size:12px;color:var(--risk-high);margin-bottom:4px;"><i class="bi bi-exclamation-triangle-fill"></i> {{ $n }}</div>
        @endforeach
    </div>
    <div class="recommendation-card {{ strtolower($recommendationB['level']) }}">
        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">{{ $countryB->country_name }}</div>
        <div style="font-size:18px;font-weight:800;color:{{ $recommendationB['color'] }};margin-bottom:12px;">
            {{ $recommendationB['action'] }}
        </div>
        @foreach($recommendationB['positive'] as $p)
        <div style="font-size:12px;color:var(--risk-low);margin-bottom:4px;"><i class="bi bi-check-circle-fill"></i> {{ $p }}</div>
        @endforeach
        @foreach($recommendationB['negative'] as $n)
        <div style="font-size:12px;color:var(--risk-high);margin-bottom:4px;"><i class="bi bi-exclamation-triangle-fill"></i> {{ $n }}</div>
        @endforeach
    </div>
</div>

@endsection

@push('scripts')
<script>
const radarCtx = document.getElementById('compareRadar');
if (radarCtx) {
    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: ['News Risk', 'Weather Risk', 'Inflation Risk', 'Currency Risk'],
            datasets: [
                {
                    label: '{{ $countryA->country_name }}',
                    data: [{{ $dataA['risk']->news_score }},{{ $dataA['risk']->weather_score }},{{ $dataA['risk']->inflation_score }},{{ $dataA['risk']->currency_score }}],
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139,92,246,0.15)',
                    pointBackgroundColor: '#8B5CF6',
                    borderWidth: 2,
                },
                {
                    label: '{{ $countryB->country_name }}',
                    data: [{{ $dataB['risk']->news_score }},{{ $dataB['risk']->weather_score }},{{ $dataB['risk']->inflation_score }},{{ $dataB['risk']->currency_score }}],
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245,158,11,0.1)',
                    pointBackgroundColor: '#F59E0B',
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#94A3B8', font: { size: 11 } } } },
            scales: {
                r: {
                    min: 0, max: 100,
                    ticks: { color: '#64748B', backdropColor: 'transparent', stepSize: 25, font: { size: 10 } },
                    grid:  { color: 'rgba(148,163,184,0.1)' },
                    pointLabels: { color: '#94A3B8', font: { size: 11 } },
                    angleLines: { color: 'rgba(148,163,184,0.15)' },
                }
            }
        }
    });
}

const barCtx = document.getElementById('compareBar');
if (barCtx) {
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Overall Risk', 'Weather', 'Inflation', 'Currency', 'News'],
            datasets: [
                {
                    label: '{{ $countryA->country_name }}',
                    data: [{{ $dataA['risk']->total_score }},{{ $dataA['risk']->weather_score }},{{ $dataA['risk']->inflation_score }},{{ $dataA['risk']->currency_score }},{{ $dataA['risk']->news_score }}],
                    backgroundColor: 'rgba(139,92,246,0.7)',
                    borderColor: '#8B5CF6',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: '{{ $countryB->country_name }}',
                    data: [{{ $dataB['risk']->total_score }},{{ $dataB['risk']->weather_score }},{{ $dataB['risk']->inflation_score }},{{ $dataB['risk']->currency_score }},{{ $dataB['risk']->news_score }}],
                    backgroundColor: 'rgba(245,158,11,0.7)',
                    borderColor: '#F59E0B',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#94A3B8', font: { size: 11 } } } },
            scales: {
                x: { ticks: { color: '#64748B', font: { size: 10 } }, grid: { color: 'rgba(148,163,184,0.08)' } },
                y: { min: 0, max: 100, ticks: { color: '#64748B' }, grid: { color: 'rgba(148,163,184,0.08)' } }
            }
        }
    });
}
</script>
@endpush
