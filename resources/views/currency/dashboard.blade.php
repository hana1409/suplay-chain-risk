@extends('layouts.dashboard')
@section('title', 'Currency Dashboard — Risk Intelligence')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">Currency Dashboard</span>
        </div>
        <h1>Currency Impact Dashboard</h1>
        <p>Real-time exchange rate analysis and comparison</p>
    </div>
</div>

{{-- CURRENCY COMPARISON SELECTOR --}}
<div class="glass-card" style="padding:24px;margin-bottom:var(--space-xl);">
    <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;color:var(--text-primary);">
        <i class="bi bi-currency-exchange" style="color:var(--accent);margin-right:8px;"></i>
        Currency Comparison
    </h3>
    
    <form method="GET" action="{{ route('currency.dashboard') }}" id="comparisonForm" style="display:grid;grid-template-columns:1fr 1fr auto;gap:16px;align-items:end;">
        <div>
            <label class="form-label-custom">Country A</label>
            <select name="country_a" class="intel-input" required onchange="this.form.submit()">
                <option value="">Select Country</option>
                @foreach($countries as $c)
                <option value="{{ $c->country_code }}" 
                    {{ $countryA == $c->country_code ? 'selected' : '' }}>
                    {{ $c->country_name }} ({{ $c->currency_code }})
                </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="form-label-custom">Country B</label>
            <select name="country_b" class="intel-input" required onchange="this.form.submit()">
                <option value="">Select Country</option>
                @foreach($countries as $c)
                <option value="{{ $c->country_code }}" 
                    {{ $countryB == $c->country_code ? 'selected' : '' }}>
                    {{ $c->country_name }} ({{ $c->currency_code }})
                </option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn-primary-custom">
            <i class="bi bi-arrow-repeat"></i> Compare
        </button>
    </form>
</div>

@if($comparison)

{{-- EXCHANGE RATE CARDS --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:var(--space-lg);margin-bottom:var(--space-xl);">
    {{-- Country A Card --}}
    <div class="glass-card" style="padding:24px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <img src="https://flagcdn.com/w40/{{ strtolower($comparison['country_a']['code']) }}.png" 
                 width="32" height="24" 
                 style="border-radius:4px;border:1px solid var(--border);object-fit:cover;">
            <div>
                <h4 style="font-size:15px;font-weight:700;margin:0;">{{ $comparison['country_a']['name'] }}</h4>
                <p style="font-size:12px;color:var(--text-muted);margin:0;">{{ $comparison['country_a']['currency_name'] }}</p>
            </div>
        </div>
        <div style="background:var(--bg-base);padding:16px;border-radius:8px;">
            <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;">Currency Code</div>
            <div style="font-size:20px;font-weight:800;color:var(--accent);">{{ $comparison['country_a']['currency_code'] }}</div>
        </div>
    </div>

    {{-- Exchange Rate Card - HIGHLIGHT CARD --}}
    <div class="exchange-rate-highlight-card">
        <div class="exchange-rate-header">
            <i class="bi bi-arrow-left-right"></i>
            <span>Exchange Rate</span>
        </div>
        
        <div class="exchange-rate-divider"></div>
        
        <div class="exchange-rate-primary">
            <span class="rate-value">1 {{ $comparison['country_a']['currency_code'] }}</span>
            <span class="rate-equals">=</span>
            <span class="rate-value">{{ number_format($comparison['cross_rate'], 4) }} {{ $comparison['country_b']['currency_code'] }}</span>
        </div>
        
        <div class="exchange-rate-secondary">
            <span class="rate-value-small">1 {{ $comparison['country_b']['currency_code'] }}</span>
            <span class="rate-equals-small">=</span>
            <span class="rate-value-small">{{ number_format($comparison['inverse_cross_rate'], 6) }} {{ $comparison['country_a']['currency_code'] }}</span>
        </div>
        
        <div class="exchange-rate-footer">
            <div class="exchange-rate-updated">
                <i class="bi bi-clock-history"></i>
                <span>Updated {{ \Carbon\Carbon::parse($comparison['last_updated'])->format('M d, Y') }} • {{ \Carbon\Carbon::parse($comparison['last_updated'])->format('H:i') }} UTC</span>
            </div>
            @if($chartData && isset($chartData['stats']))
            <div class="exchange-rate-change {{ $chartData['stats']['daily_change'] >= 0 ? 'positive' : 'negative' }}">
                <i class="bi bi-{{ $chartData['stats']['daily_change'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                <span>{{ $chartData['stats']['daily_change'] >= 0 ? '+' : '' }}{{ number_format($chartData['stats']['daily_change'], 2) }}%</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Country B Card --}}
    <div class="glass-card" style="padding:24px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <img src="https://flagcdn.com/w40/{{ strtolower($comparison['country_b']['code']) }}.png" 
                 width="32" height="24" 
                 style="border-radius:4px;border:1px solid var(--border);object-fit:cover;">
            <div>
                <h4 style="font-size:15px;font-weight:700;margin:0;">{{ $comparison['country_b']['name'] }}</h4>
                <p style="font-size:12px;color:var(--text-muted);margin:0;">{{ $comparison['country_b']['currency_name'] }}</p>
            </div>
        </div>
        <div style="background:var(--bg-base);padding:16px;border-radius:8px;">
            <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;">Currency Code</div>
            <div style="font-size:20px;font-weight:800;color:var(--accent);">{{ $comparison['country_b']['currency_code'] }}</div>
        </div>
    </div>
</div>

{{-- EXCHANGE RATE ANALYTICS --}}
<div class="glass-card" style="padding:24px;margin-bottom:var(--space-xl);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="font-size:16px;font-weight:700;margin:0;color:var(--text-primary);">
            <i class="bi bi-graph-up" style="color:var(--accent);margin-right:8px;"></i>
            Exchange Rate Analytics
        </h3>
        <div class="btn-group-custom" role="group">
            <button type="button" class="btn-period active" data-period="7">7 Days</button>
            <button type="button" class="btn-period" data-period="30">30 Days</button>
            <button type="button" class="btn-period" data-period="90">90 Days</button>
        </div>
    </div>
    
    <div style="position:relative;height:350px;">
        <canvas id="currencyChart"></canvas>
    </div>
</div>

{{-- CURRENCY STATISTICS --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:var(--space-lg);margin-bottom:var(--space-xl);">
    @if($chartData && isset($chartData['stats']))
    <div class="glass-card" style="padding:20px;text-align:center;">
        <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">Current Rate</div>
        <div style="font-size:20px;font-weight:800;color:var(--accent);">{{ number_format($chartData['stats']['current'], 4) }}</div>
    </div>
    
    <div class="glass-card" style="padding:20px;text-align:center;">
        <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">Highest Rate</div>
        <div style="font-size:20px;font-weight:800;color:var(--risk-high);">{{ number_format($chartData['stats']['high'], 4) }}</div>
    </div>
    
    <div class="glass-card" style="padding:20px;text-align:center;">
        <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">Lowest Rate</div>
        <div style="font-size:20px;font-weight:800;color:var(--risk-low);">{{ number_format($chartData['stats']['low'], 4) }}</div>
    </div>
    
    <div class="glass-card" style="padding:20px;text-align:center;">
        <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">Average Rate</div>
        <div style="font-size:20px;font-weight:800;color:var(--text-primary);">{{ number_format($chartData['stats']['average'], 4) }}</div>
    </div>
    
    <div class="glass-card" style="padding:20px;text-align:center;">
        <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">Daily Change</div>
        <div style="font-size:20px;font-weight:800;color:{{ $chartData['stats']['daily_change'] >= 0 ? 'var(--risk-low)' : 'var(--risk-critical)' }};">
            {{ $chartData['stats']['daily_change'] >= 0 ? '+' : '' }}{{ number_format($chartData['stats']['daily_change'], 2) }}%
        </div>
    </div>
    
    <div class="glass-card" style="padding:20px;text-align:center;">
        <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">Weekly Change</div>
        <div style="font-size:20px;font-weight:800;color:{{ $chartData['stats']['weekly_change'] >= 0 ? 'var(--risk-low)' : 'var(--risk-critical)' }};">
            {{ $chartData['stats']['weekly_change'] >= 0 ? '+' : '' }}{{ number_format($chartData['stats']['weekly_change'], 2) }}%
        </div>
    </div>
    
    <div class="glass-card" style="padding:20px;text-align:center;">
        <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">Monthly Change</div>
        <div style="font-size:20px;font-weight:800;color:{{ $chartData['stats']['monthly_change'] >= 0 ? 'var(--risk-low)' : 'var(--risk-critical)' }};">
            {{ $chartData['stats']['monthly_change'] >= 0 ? '+' : '' }}{{ number_format($chartData['stats']['monthly_change'], 2) }}%
        </div>
    </div>
    @endif
</div>

{{-- CURRENCY INSIGHT --}}
<div class="glass-card" style="padding:24px;">
    <h3 style="font-size:16px;font-weight:700;margin-bottom:16px;color:var(--text-primary);">
        <i class="bi bi-lightbulb-fill" style="color:var(--accent);margin-right:8px;"></i>
        Currency Insight
    </h3>
    
    @if($chartData && isset($chartData['stats']))
    <div style="display:flex;flex-direction:column;gap:12px;">
        @php
            $monthlyChange = $chartData['stats']['monthly_change'];
            $trend = $monthlyChange >= 0 ? 'strengthening' : 'weakening';
            $color = $monthlyChange >= 0 ? 'var(--risk-low)' : 'var(--risk-critical)';
        @endphp
        
        <div style="padding:12px;background:var(--bg-base);border-left:3px solid {{ $color }};border-radius:6px;">
            <strong>{{ $comparison['country_a']['currency_code'] }}</strong> is currently <strong>{{ $trend }}</strong> against <strong>{{ $comparison['country_b']['currency_code'] }}</strong>.
        </div>
        
        <div style="padding:12px;background:var(--bg-base);border-radius:6px;">
            Over the past 30 days, {{ $comparison['country_a']['currency_code'] }} has {{ $monthlyChange >= 0 ? 'appreciated' : 'depreciated' }} by <strong style="color:{{ $color }}">{{ abs($monthlyChange) }}%</strong>.
        </div>
        
        <div style="padding:12px;background:var(--bg-base);border-radius:6px;">
            The exchange rate is currently <strong>{{ $chartData['stats']['current'] > $chartData['stats']['average'] ? 'above' : 'below' }}</strong> the 30-day average of {{ number_format($chartData['stats']['average'], 4) }}.
        </div>
        
        @if(abs($monthlyChange) > 5)
        <div style="padding:12px;background:rgba(239,68,68,0.1);border-left:3px solid var(--risk-critical);border-radius:6px;color:var(--risk-critical);">
            <i class="bi bi-exclamation-triangle-fill"></i> <strong>High Volatility Alert:</strong> The currency has experienced significant fluctuation ({{ abs($monthlyChange) }}%) in the past month.
        </div>
        @endif
    </div>
    @endif
</div>

@else

{{-- NO SELECTION STATE --}}
<div class="glass-card" style="padding:60px 24px;text-align:center;">
    <i class="bi bi-currency-exchange" style="font-size:64px;color:var(--text-muted);opacity:0.3;display:block;margin-bottom:20px;"></i>
    <h3 style="font-size:18px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">Select Countries to Compare</h3>
    <p style="color:var(--text-muted);font-size:14px;">Choose two countries from the dropdowns above to analyze their currency exchange rates.</p>
</div>

@endif

@endsection

@push('scripts')
<script>
// Chart.js Configuration
@if($comparison && $chartData)
const chartData = {
    labels: @json($chartData['labels']),
    values: @json($chartData['values'])
};

let currencyChart = null;

function initChart() {
    const ctx = document.getElementById('currencyChart');
    if (!ctx) return;

    if (currencyChart) {
        currencyChart.destroy();
    }

    currencyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: '{{ $comparison['country_a']['currency_code'] }} / {{ $comparison['country_b']['currency_code'] }}',
                data: chartData.values,
                borderColor: 'rgb(15, 118, 110)',
                backgroundColor: 'rgba(15, 118, 110, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: 'rgb(15, 118, 110)',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#1F2937',
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(31, 41, 55, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgb(15, 118, 110)',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rate: ' + context.parsed.y.toFixed(6);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Period filter buttons
document.querySelectorAll('.btn-period').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-period').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const period = this.getAttribute('data-period');
        const url = new URL(window.location.href);
        url.searchParams.set('period', period);
        window.location.href = url.toString();
    });
});

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', initChart);
@endif
</script>

<style>
/* Exchange Rate Highlight Card */
.exchange-rate-highlight-card {
    padding: 28px;
    background: linear-gradient(135deg, #065F46 0%, #0F766E 50%, #14B8A6 100%);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(15, 118, 110, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.exchange-rate-highlight-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(15, 118, 110, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.15) inset;
}

.exchange-rate-highlight-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.exchange-rate-header {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 16px;
}

.exchange-rate-header i {
    font-size: 16px;
    color: #fff;
}

.exchange-rate-divider {
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    margin-bottom: 20px;
}

.exchange-rate-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.exchange-rate-primary .rate-value {
    color: #ffffff;
    font-size: 24px;
    font-weight: 900;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    letter-spacing: -0.02em;
}

.exchange-rate-primary .rate-equals {
    color: rgba(255, 255, 255, 0.8);
    font-size: 20px;
    font-weight: 600;
}

.exchange-rate-secondary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.exchange-rate-secondary .rate-value-small {
    color: rgba(255, 255, 255, 0.95);
    font-size: 15px;
    font-weight: 700;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
}

.exchange-rate-secondary .rate-equals-small {
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    font-weight: 600;
}

.exchange-rate-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.15);
    flex-wrap: wrap;
}

.exchange-rate-updated {
    display: flex;
    align-items: center;
    gap: 6px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 11px;
    font-weight: 600;
}

.exchange-rate-updated i {
    font-size: 12px;
    opacity: 0.9;
}

.exchange-rate-change {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
}

.exchange-rate-change.positive {
    background: rgba(34, 197, 94, 0.25);
    color: #D1FAE5;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.exchange-rate-change.negative {
    background: rgba(239, 68, 68, 0.25);
    color: #FEE2E2;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.exchange-rate-change i {
    font-size: 11px;
}

/* Button Group */
.btn-group-custom {
    display: inline-flex;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--border);
}

.btn-period {
    padding: 8px 16px;
    background: var(--bg-surface);
    border: none;
    color: var(--text-muted);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border-right: 1px solid var(--border);
}

.btn-period:last-child {
    border-right: none;
}

.btn-period:hover {
    background: var(--bg-base);
    color: var(--text-primary);
}

.btn-period.active {
    background: var(--accent);
    color: white;
}

/* Form Label */
.form-label-custom {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Responsive Exchange Rate Card */
@media (max-width: 768px) {
    .exchange-rate-highlight-card {
        padding: 20px;
    }
    
    .exchange-rate-primary .rate-value {
        font-size: 20px;
    }
    
    .exchange-rate-primary .rate-equals {
        font-size: 16px;
    }
    
    .exchange-rate-secondary .rate-value-small {
        font-size: 13px;
    }
    
    .exchange-rate-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}
</style>
@endpush
