@extends('layouts.dashboard')

@section('title', $country->country_name . ' — Risk Profile')

@push('styles')
<style>
/* Risk Gauge */
.risk-gauge-wrap {
    position: relative;
    width: 160px;
    height: 90px;
    margin: 0 auto;
}

.risk-gauge-svg { overflow: visible; }

.risk-gauge-track {
    fill: none;
    stroke: var(--bg-base);
    stroke-width: 16;
    stroke-linecap: round;
}

.risk-gauge-fill {
    fill: none;
    stroke-width: 16;
    stroke-linecap: round;
    transition: stroke-dashoffset 1.5s cubic-bezier(0.4,0,0.2,1);
}

.risk-gauge-value {
    font-size: 28px;
    font-weight: 800;
    text-anchor: middle;
    dominant-baseline: middle;
}

.risk-gauge-label {
    font-size: 11px;
    text-anchor: middle;
    fill: var(--text-muted);
}

/* Country Banner */
.country-banner {
    background: var(--gradient-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 32px;
    margin-bottom: var(--space-xl);
    display: flex;
    align-items: center;
    gap: 32px;
    position: relative;
    overflow: hidden;
}

.country-banner::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 200px; height: 200px;
    background: var(--accent-subtle);
    border-radius: 50%;
    filter: blur(60px);
}

.banner-flag {
    width: 120px;
    height: 80px;
    object-fit: cover;
    border-radius: var(--radius-md);
    border: 2px solid var(--border);
    box-shadow: var(--shadow-md);
    flex-shrink: 0;
}

.banner-info h1 {
    font-size: 30px;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 6px;
}

.banner-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 10px;
}

.banner-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-muted);
}

.banner-meta-item i { color: var(--accent); }

.banner-actions {
    margin-left: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-shrink: 0;
}

/* Info Cards Grid */
.info-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: var(--space-xl);
}

.info-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.info-card:hover {
    border-color: var(--border-hover);
    transform: translateY(-2px);
}

.info-card-icon {
    font-size: 22px;
    margin-bottom: 12px;
    display: block;
}

.info-card-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.7px;
    margin-bottom: 4px;
    font-weight: 600;
}

.info-card-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
}

.info-card-sub {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 4px;
}

/* Recommendation Card */
.recommendation-card {
    border-radius: var(--radius-xl);
    padding: 24px;
    position: relative;
    overflow: hidden;
}

.recommendation-card.low      { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.3); }
.recommendation-card.medium   { background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.3); }
.recommendation-card.high     { background: rgba(249,115,22,0.08); border: 1px solid rgba(249,115,22,0.3); }
.recommendation-card.critical { background: rgba(239,68,68,0.08);  border: 1px solid rgba(239,68,68,0.3); }

/* News Cards */
.news-card {
    display: flex;
    gap: 14px;
    padding: 14px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    background: var(--bg-card);
    transition: var(--transition);
    text-decoration: none;
    color: inherit;
}

.news-card:hover {
    border-color: var(--border-hover);
    background: var(--bg-card-hover);
    transform: translateX(3px);
}

.news-thumb {
    width: 72px;
    height: 56px;
    object-fit: cover;
    border-radius: var(--radius-sm);
    flex-shrink: 0;
    background: var(--bg-base);
}

/* Risk Breakdown */
.risk-breakdown-item {
    margin-bottom: 16px;
}

.risk-breakdown-header {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-bottom: 6px;
}

.risk-breakdown-bar {
    height: 6px;
    background: var(--bg-base);
    border-radius: 3px;
    overflow: hidden;
}

.risk-breakdown-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 1.5s cubic-bezier(0.4,0,0.2,1);
}
</style>
@endpush

@section('content')

{{-- BREADCRUMB --}}
<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('countries') }}">Countries</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">{{ $country->country_name }}</span>
        </div>
        <h1>{{ $country->country_name }}</h1>
        <p>Supply Chain Risk Intelligence Report</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('compare') }}?a={{ $country->country_code }}" class="btn-ghost">
            <i class="bi bi-bar-chart-steps"></i> Compare
        </a>
        <button id="watchBtn" onclick="toggleWatchlist()"
            class="btn-ghost {{ $isWatched ? 'active' : '' }}"
            style="{{ $isWatched ? 'border-color:rgba(245,158,11,0.5);color:#F59E0B;' : '' }}">
            <i class="bi bi-star{{ $isWatched ? '-fill' : '' }}" id="watchIcon"></i>
            {{ $isWatched ? 'Watching' : 'Watch' }}
        </button>
    </div>
</div>

{{-- COUNTRY BANNER --}}
<div class="country-banner fade-in-up">
    <img src="{{ $country->flag_png ?? 'https://flagcdn.com/w160/' . strtolower($country->country_code) . '.png' }}"
         class="banner-flag"
         alt="{{ $country->country_name }} flag">
    <div class="banner-info">
        <h1>{{ $country->country_name }}</h1>
        <div style="font-size:14px;color:var(--text-muted);">{{ $country->official_name }}</div>
        <div class="banner-meta">
            @if($country->capital)
            <div class="banner-meta-item"><i class="bi bi-geo-alt-fill"></i> {{ $country->capital }}</div>
            @endif
            @if($country->region)
            <div class="banner-meta-item"><i class="bi bi-globe"></i> {{ $country->region }}</div>
            @endif
            @if($country->currency_code)
            <div class="banner-meta-item"><i class="bi bi-currency-exchange"></i> {{ $country->currency_code }} — {{ $country->currency_name }}</div>
            @endif
            @if($country->language)
            <div class="banner-meta-item"><i class="bi bi-translate"></i> {{ $country->language }}</div>
            @endif
        </div>
    </div>

    {{-- Risk Gauge --}}
    <div style="flex-shrink:0;text-align:center;">
        <div class="risk-gauge-wrap">
            <svg class="risk-gauge-svg" viewBox="0 0 160 100" width="160" height="100">
                {{-- Track --}}
                <path d="M 20 90 A 70 70 0 0 1 140 90" class="risk-gauge-track" />
                {{-- Fill (animated via JS) --}}
                <path id="gaugeFill" d="M 20 90 A 70 70 0 0 1 140 90"
                      class="risk-gauge-fill"
                      stroke="{{ $riskScore?->risk_color ?? '#6B7280' }}"
                      stroke-dasharray="220"
                      stroke-dashoffset="220" />
                <text x="80" y="68" class="risk-gauge-value" fill="{{ $riskScore?->risk_color ?? '#6B7280' }}">
                    {{ number_format($riskScore?->total_score ?? 0, 1) }}
                </text>
                <text x="80" y="86" class="risk-gauge-label">/ 100</text>
            </svg>
        </div>
        <span class="risk-badge risk-{{ strtolower($riskScore?->risk_level ?? 'unknown') }}" style="margin-top:4px;display:inline-flex;">
            {{ $riskScore?->risk_level ?? 'No Data' }}
        </span>
    </div>
</div>

{{-- INFO CARDS --}}
<div class="info-cards-grid fade-in-up fade-in-up-1">

    <div class="info-card">
        <span class="info-card-icon">💰</span>
        <div class="info-card-label">GDP</div>
        <div class="info-card-value">{{ $economic?->formatted_gdp ?? 'N/A' }}</div>
        <div class="info-card-sub">World Bank {{ $economic?->year ?? '—' }}</div>
    </div>

    <div class="info-card">
        <span class="info-card-icon">📈</span>
        <div class="info-card-label">Inflation</div>
        <div class="info-card-value" style="color:{{ ($economic?->inflation ?? 0) > 10 ? 'var(--risk-critical)' : (($economic?->inflation ?? 0) > 5 ? 'var(--risk-high)' : 'var(--risk-low)') }}">
            {{ $economic ? number_format($economic->inflation ?? 0, 2) . '%' : 'N/A' }}
        </div>
        <div class="info-card-sub">Consumer Price Index</div>
    </div>

    <div class="info-card">
        <span class="info-card-icon">👥</span>
        <div class="info-card-label">Population</div>
        <div class="info-card-value">
            @if($economic?->population)
                {{ number_format((int) $economic->population) }}
            @else
                N/A
            @endif
        </div>
        <div class="info-card-sub">{{ $country->country_name }}</div>
    </div>

    <div class="info-card">
        <span class="info-card-icon">💱</span>
        <div class="info-card-label">Exchange Rate</div>
        <div class="info-card-value">
            {{ $currency ? '1 USD = ' . number_format($currency->exchange_rate, 4) . ' ' . $currency->target_currency : 'N/A' }}
        </div>
        <div class="info-card-sub">Open Exchange Rates</div>
    </div>

    <div class="info-card">
        <span class="info-card-icon">📦</span>
        <div class="info-card-label">Exports</div>
        @php
            $exports = $economic?->exports;
            $exportsFormatted = match(true) {
                $exports === null        => 'N/A',
                $exports >= 1_000_000_000_000 => '$' . round($exports / 1_000_000_000_000, 2) . 'T',
                $exports >= 1_000_000_000     => '$' . round($exports / 1_000_000_000, 2) . 'B',
                $exports >= 1_000_000         => '$' . round($exports / 1_000_000, 2) . 'M',
                default                       => '$' . number_format($exports, 0),
            };
        @endphp
        <div class="info-card-value" style="color:{{ $exports ? 'var(--risk-low)' : 'var(--text-primary)' }}">
            {{ $exportsFormatted }}
        </div>
        <div class="info-card-sub">Economic Cache</div>
    </div>

    <div class="info-card">
        <span class="info-card-icon">🛳️</span>
        <div class="info-card-label">Imports</div>
        @php
            $imports = $economic?->imports;
            $importsFormatted = match(true) {
                $imports === null        => 'N/A',
                $imports >= 1_000_000_000_000 => '$' . round($imports / 1_000_000_000_000, 2) . 'T',
                $imports >= 1_000_000_000     => '$' . round($imports / 1_000_000_000, 2) . 'B',
                $imports >= 1_000_000         => '$' . round($imports / 1_000_000, 2) . 'M',
                default                       => '$' . number_format($imports, 0),
            };
        @endphp
        <div class="info-card-value" style="color:{{ $imports ? 'var(--risk-medium)' : 'var(--text-primary)' }}">
            {{ $importsFormatted }}
        </div>
        <div class="info-card-sub">Economic Cache</div>
    </div>

    <div class="info-card">
        <span class="info-card-icon">{{ $weather?->weather_icon ?? '🌡️' }}</span>
        <div class="info-card-label">Temperature</div>
        <div class="info-card-value">{{ $weather ? $weather->temperature . '°C' : 'N/A' }}</div>
        <div class="info-card-sub">{{ $weather?->weather_condition ?? 'Open Meteo' }}</div>
    </div>

    <div class="info-card">
        <span class="info-card-icon">💨</span>
        <div class="info-card-label">Wind Speed</div>
        <div class="info-card-value">{{ $weather ? $weather->wind_speed . ' km/h' : 'N/A' }}</div>
        <div class="info-card-sub">Humidity: {{ $weather ? $weather->humidity . '%' : 'N/A' }}</div>
    </div>

    <div class="info-card">
        <span class="info-card-icon">⚓</span>
        <div class="info-card-label">Ports</div>
        <div class="info-card-value">{{ $ports->count() }}</div>
        <div class="info-card-sub">Active Ports</div>
    </div>

    <div class="info-card">
        <span class="info-card-icon">📰</span>
        <div class="info-card-label">News</div>
        <div class="info-card-value">{{ $news->count() }}</div>
        <div class="info-card-sub">Recent Articles</div>
    </div>

</div>

{{-- MAIN CONTENT: Charts + Risk + Recommendation + Ports + News --}}
<div class="two-col-grid fade-in-up fade-in-up-2">

    {{-- LEFT: Charts + News --}}
    <div style="display:flex;flex-direction:column;gap:var(--space-lg);">

        {{-- GDP + Inflation Chart --}}
        @if($economicHistory->isNotEmpty())
        <div class="glass-card" style="padding:20px;">
            <div class="section-header" style="margin-bottom:16px;">
                <div class="section-title"><span></span> Economic Trend</div>
            </div>
            <div class="chart-container" style="height:200px;">
                <canvas id="economicChart"></canvas>
            </div>
        </div>
        @endif

        {{-- News --}}
        <div class="glass-card" style="padding:20px;">
            <div class="section-header" style="margin-bottom:16px;">
                <div class="section-title"><span></span> Latest News</div>
                <a href="{{ route('news') }}" style="font-size:12px;color:var(--accent);text-decoration:none;">All News <i class="bi bi-arrow-right"></i></a>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px;">
                @forelse($news as $article)
                <a href="{{ $article->url }}" target="_blank" class="news-card">
                    @if($article->image)
                    <img src="{{ $article->image }}" class="news-thumb" alt="" onerror="this.style.display='none'">
                    @endif
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--text-primary);line-height:1.35;margin-bottom:6px;">
                            {{ Str::limit($article->title, 80) }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <span class="sentiment-{{ strtolower($article->sentiment ?? 'neutral') }}" style="font-size:10px;padding:1px 7px;">
                                {{ $article->sentiment_icon ?? '→' }} {{ $article->sentiment ?? 'Neutral' }}
                            </span>
                            @if($article->source)
                            <span style="font-size:10px;color:var(--text-muted);">{{ $article->source }}</span>
                            @endif
                            <span style="font-size:10px;color:var(--text-muted);margin-left:auto;">
                                {{ $article->published_at?->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </a>
                @empty
                <div style="text-align:center;padding:32px;color:var(--text-muted);">
                    <i class="bi bi-newspaper" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    No news available yet.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Ports --}}
        @if($ports->isNotEmpty())
        <div class="glass-card" style="padding:20px;">
            <div class="section-header" style="margin-bottom:16px;">
                <div class="section-title"><span></span> Ports in {{ $country->country_name }}</div>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                @foreach($ports as $port)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 12px;background:var(--bg-base);border-radius:var(--radius-sm);border:1px solid var(--border);">
                    <i class="bi bi-anchor" style="color:var(--accent);font-size:14px;"></i>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ $port->port_name }}</div>
                        @if($port->city)
                        <div style="font-size:11px;color:var(--text-muted);">{{ $port->city }}</div>
                        @endif
                    </div>
                    <span style="font-size:11px;color:var(--text-muted);background:var(--bg-secondary);padding:2px 8px;border-radius:999px;">{{ $port->port_type }}</span>
                    <span style="font-size:11px;color:{{ $port->status === 'Active' ? 'var(--risk-low)' : 'var(--text-muted)' }};">● {{ $port->status }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT: Risk Breakdown + Recommendation --}}
    <div style="display:flex;flex-direction:column;gap:var(--space-lg);">

        {{-- Risk Score Breakdown --}}
        <div class="glass-card" style="padding:20px;">
            <div class="section-header" style="margin-bottom:20px;">
                <div class="section-title"><span></span> Risk Breakdown</div>
            </div>

            <div class="risk-breakdown-item">
                <div class="risk-breakdown-header">
                    <span style="color:var(--text-secondary);">📰 News Sentiment</span>
                    <span style="color:var(--text-primary);font-weight:700;">{{ number_format($riskScore?->news_score ?? 0, 1) }} / 100</span>
                </div>
                <div class="risk-breakdown-bar">
                    <div class="risk-breakdown-fill"
                         style="width:0%;background:var(--accent);"
                         data-width="{{ $riskScore?->news_score ?? 0 }}"></div>
                </div>
                <div style="font-size:10px;color:var(--text-muted);margin-top:3px;">Weight: 40%</div>
            </div>

            <div class="risk-breakdown-item">
                <div class="risk-breakdown-header">
                    <span style="color:var(--text-secondary);">🌦️ Weather Risk</span>
                    <span style="color:var(--text-primary);font-weight:700;">{{ number_format($riskScore?->weather_score ?? 0, 1) }} / 100</span>
                </div>
                <div class="risk-breakdown-bar">
                    <div class="risk-breakdown-fill"
                         style="width:0%;background:#60A5FA;"
                         data-width="{{ $riskScore?->weather_score ?? 0 }}"></div>
                </div>
                <div style="font-size:10px;color:var(--text-muted);margin-top:3px;">Weight: 30%</div>
            </div>

            <div class="risk-breakdown-item">
                <div class="risk-breakdown-header">
                    <span style="color:var(--text-secondary);">📈 Inflation Risk</span>
                    <span style="color:var(--text-primary);font-weight:700;">{{ number_format($riskScore?->inflation_score ?? 0, 1) }} / 100</span>
                </div>
                <div class="risk-breakdown-bar">
                    <div class="risk-breakdown-fill"
                         style="width:0%;background:var(--risk-high);"
                         data-width="{{ $riskScore?->inflation_score ?? 0 }}"></div>
                </div>
                <div style="font-size:10px;color:var(--text-muted);margin-top:3px;">Weight: 20%</div>
            </div>

            <div class="risk-breakdown-item">
                <div class="risk-breakdown-header">
                    <span style="color:var(--text-secondary);">💱 Currency Risk</span>
                    <span style="color:var(--text-primary);font-weight:700;">{{ number_format($riskScore?->currency_score ?? 0, 1) }} / 100</span>
                </div>
                <div class="risk-breakdown-bar">
                    <div class="risk-breakdown-fill"
                         style="width:0%;background:var(--risk-medium);"
                         data-width="{{ $riskScore?->currency_score ?? 0 }}"></div>
                </div>
                <div style="font-size:10px;color:var(--text-muted);margin-top:3px;">Weight: 10%</div>
            </div>

            {{-- Radar Chart --}}
            <div class="chart-container" style="height:220px;margin-top:20px;">
                <canvas id="radarChart"></canvas>
            </div>
        </div>

        {{-- AI Recommendation --}}
        <div class="recommendation-card {{ strtolower($recommendation['level'] ?? 'low') }}">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="font-size:32px;">
                    {{ match($recommendation['level']) {
                        'Low'      => '✅',
                        'Medium'   => '⚠️',
                        'High'     => '🔶',
                        'Critical' => '🚨',
                        default    => '❓'
                    } }}
                </div>
                <div>
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.7px;color:var(--text-muted);margin-bottom:2px;">Decision Recommendation</div>
                    <div style="font-size:16px;font-weight:800;color:{{ $recommendation['color'] }};">{{ $recommendation['action'] }}</div>
                </div>
            </div>

            @if(!empty($recommendation['positive']))
            <div style="margin-bottom:12px;">
                <div style="font-size:11px;font-weight:700;color:var(--risk-low);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">✓ Positive Factors</div>
                @foreach($recommendation['positive'] as $p)
                <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-secondary);margin-bottom:3px;">
                    <i class="bi bi-check-circle-fill" style="color:var(--risk-low);font-size:11px;"></i> {{ $p }}
                </div>
                @endforeach
            </div>
            @endif

            @if(!empty($recommendation['negative']))
            <div>
                <div style="font-size:11px;font-weight:700;color:var(--risk-high);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">⚠ Risk Factors</div>
                @foreach($recommendation['negative'] as $n)
                <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-secondary);margin-bottom:3px;">
                    <i class="bi bi-exclamation-triangle-fill" style="color:var(--risk-high);font-size:11px;"></i> {{ $n }}
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
const COUNTRY_ID = {{ $country->id }};
let isWatched = {{ $isWatched ? 'true' : 'false' }};

// =========================================
// GAUGE ANIMATION
// =========================================
window.addEventListener('load', () => {
    const score   = {{ $riskScore?->total_score ?? 0 }};
    const total   = 220;
    const offset  = total - (score / 100) * total;
    const fill    = document.getElementById('gaugeFill');
    if (fill) {
        setTimeout(() => fill.style.strokeDashoffset = offset, 200);
    }

    // Animate bars
    document.querySelectorAll('.risk-breakdown-fill').forEach(bar => {
        const w = bar.getAttribute('data-width');
        setTimeout(() => bar.style.width = w + '%', 400);
    });
});

// =========================================
// RADAR CHART
// =========================================
const radarCtx = document.getElementById('radarChart');
if (radarCtx) {
    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: ['News', 'Weather', 'Inflation', 'Currency'],
            datasets: [{
                label: '{{ $country->country_name }}',
                data: [
                    {{ $riskScore?->news_score ?? 0 }},
                    {{ $riskScore?->weather_score ?? 0 }},
                    {{ $riskScore?->inflation_score ?? 0 }},
                    {{ $riskScore?->currency_score ?? 0 }},
                ],
                borderColor: '#8B5CF6',
                backgroundColor: 'rgba(139,92,246,0.15)',
                pointBackgroundColor: '#8B5CF6',
                pointBorderColor: '#fff',
                pointHoverRadius: 5,
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                r: {
                    min: 0, max: 100,
                    ticks: { color: '#64748B', font: { size: 10 }, stepSize: 25, backdropColor: 'transparent' },
                    grid:  { color: 'rgba(148,163,184,0.1)' },
                    pointLabels: { color: '#94A3B8', font: { size: 11, weight: '600' } },
                    angleLines: { color: 'rgba(148,163,184,0.15)' },
                }
            }
        }
    });
}

// =========================================
// ECONOMIC CHART
// =========================================
const ecoCtx = document.getElementById('economicChart');
if (ecoCtx) {
    const ecoData = @json($economicHistory);
    new Chart(ecoCtx, {
        type: 'line',
        data: {
            labels: ecoData.map(d => d.year),
            datasets: [
                {
                    label: 'GDP (B USD)',
                    data: ecoData.map(d => d.gdp ? (d.gdp / 1e9).toFixed(2) : null),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139,92,246,0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y',
                },
                {
                    label: 'Inflation %',
                    data: ecoData.map(d => d.inflation),
                    borderColor: '#F59E0B',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    yAxisID: 'y1',
                    borderDash: [5,5],
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index' },
            plugins: {
                legend: { labels: { color: '#94A3B8', font: { size: 11 }, boxWidth: 12 } },
            },
            scales: {
                x:  { ticks: { color: '#64748B' }, grid: { color: 'rgba(148,163,184,0.1)' } },
                y:  { ticks: { color: '#64748B' }, grid: { color: 'rgba(148,163,184,0.1)' }, position: 'left' },
                y1: { ticks: { color: '#F59E0B' }, grid: { display: false }, position: 'right' },
            }
        }
    });
}

// =========================================
// WATCHLIST TOGGLE
// =========================================
function toggleWatchlist() {
    fetch('/watchlist/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ country_id: COUNTRY_ID }),
    })
    .then(r => r.json())
    .then(data => {
        isWatched = data.action === 'added';
        const btn  = document.getElementById('watchBtn');
        const icon = document.getElementById('watchIcon');
        if (isWatched) {
            btn.style.cssText  = 'border-color:rgba(245,158,11,0.5);color:#F59E0B;';
            icon.className     = 'bi bi-star-fill';
            btn.innerHTML      = `<i class="bi bi-star-fill" id="watchIcon"></i> Watching`;
        } else {
            btn.style.cssText  = '';
            btn.innerHTML      = `<i class="bi bi-star" id="watchIcon"></i> Watch`;
        }
        showToast(data.message, 'success');
    })
    .catch(() => showToast('Request failed', 'error'));
}
</script>
@endpush
