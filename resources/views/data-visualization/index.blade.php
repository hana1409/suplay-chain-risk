@extends('layouts.dashboard')
@section('title', 'Data Visualization Dashboard — Risk Intelligence')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">Data Visualization</span>
        </div>
        <h1>Data Visualization Dashboard</h1>
        <p>Advanced economic and risk analytics with trend visualization</p>
    </div>
</div>

{{-- FILTERS --}}
<div class="glass-card" style="padding:20px;margin-bottom:var(--space-xl);">
    <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:16px;align-items:end;">
        <div>
            <label class="form-label-viz">Country</label>
            <select id="countryFilter" class="intel-input" onchange="updateAllCharts()">
                @foreach($countries as $c)
                <option value="{{ $c->country_code }}" {{ $countryCode == $c->country_code ? 'selected' : '' }}>
                    {{ $c->country_name }}
                </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="form-label-viz">Time Period</label>
            <select id="periodFilter" class="intel-input" onchange="updateAllCharts()">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>7 Days</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 Days</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 Days</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>1 Year</option>
                <option value="1825" {{ $period == 1825 ? 'selected' : '' }}>5 Years</option>
            </select>
        </div>
        
        <button onclick="updateAllCharts()" class="btn-primary-custom">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>

{{-- SUMMARY ANALYTICS --}}
<div class="summary-analytics" id="summaryAnalytics">
    <div class="glass-card summary-card">
        <div class="summary-icon" style="background:linear-gradient(135deg,#10B981,#059669);">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="summary-content">
            <div class="summary-label">GDP Trend</div>
            <div class="summary-value" id="summaryGdp">Loading...</div>
        </div>
    </div>
    
    <div class="glass-card summary-card">
        <div class="summary-icon" style="background:linear-gradient(135deg,#F59E0B,#D97706);">
            <i class="bi bi-percent"></i>
        </div>
        <div class="summary-content">
            <div class="summary-label">Inflation Status</div>
            <div class="summary-value" id="summaryInflation">Loading...</div>
        </div>
    </div>
    
    <div class="glass-card summary-card">
        <div class="summary-icon" style="background:linear-gradient(135deg,#3B82F6,#2563EB);">
            <i class="bi bi-currency-exchange"></i>
        </div>
        <div class="summary-content">
            <div class="summary-label">Currency Status</div>
            <div class="summary-value" id="summaryCurrency">Loading...</div>
        </div>
    </div>
    
    <div class="glass-card summary-card">
        <div class="summary-icon" style="background:linear-gradient(135deg,#EF4444,#DC2626);">
            <i class="bi bi-shield-exclamation"></i>
        </div>
        <div class="summary-content">
            <div class="summary-label">Risk Trend</div>
            <div class="summary-value" id="summaryRisk">Loading...</div>
        </div>
    </div>
</div>

{{-- GDP TREND CHART --}}
<div class="glass-card" style="padding:24px;margin-bottom:var(--space-xl);">
    <div class="chart-header">
        <h3><i class="bi bi-graph-up"></i> GDP Trend</h3>
        <div class="chart-loading" id="gdpLoading">
            <div class="intel-spinner"></div>
        </div>
    </div>
    <div style="position:relative;height:300px;">
        <canvas id="gdpChart"></canvas>
    </div>
    <div class="chart-stats" id="gdpStats">
        <!-- Stats will be inserted by JavaScript -->
    </div>
</div>

{{-- INFLATION TREND CHART --}}
<div class="glass-card" style="padding:24px;margin-bottom:var(--space-xl);">
    <div class="chart-header">
        <h3><i class="bi bi-percent"></i> Inflation Trend</h3>
        <div class="chart-loading" id="inflationLoading">
            <div class="intel-spinner"></div>
        </div>
    </div>
    <div style="position:relative;height:300px;">
        <canvas id="inflationChart"></canvas>
    </div>
    <div class="chart-stats" id="inflationStats">
        <!-- Stats will be inserted by JavaScript -->
    </div>
</div>

{{-- CURRENCY TREND CHART --}}
<div class="glass-card" style="padding:24px;margin-bottom:var(--space-xl);">
    <div class="chart-header">
        <h3><i class="bi bi-currency-dollar"></i> Currency Trend (vs USD)</h3>
        <div class="chart-loading" id="currencyLoading">
            <div class="intel-spinner"></div>
        </div>
    </div>
    <div style="position:relative;height:300px;">
        <canvas id="currencyChart"></canvas>
    </div>
    <div class="chart-stats" id="currencyStats">
        <!-- Stats will be inserted by JavaScript -->
    </div>
</div>

{{-- RISK TREND CHART --}}
<div class="glass-card" style="padding:24px;margin-bottom:var(--space-xl);">
    <div class="chart-header">
        <h3><i class="bi bi-shield-check"></i> Risk Score Trend</h3>
        <div class="chart-loading" id="riskLoading">
            <div class="intel-spinner"></div>
        </div>
    </div>
    <div style="position:relative;height:300px;">
        <canvas id="riskChart"></canvas>
    </div>
    <div class="chart-stats" id="riskStats">
        <!-- Stats will be inserted by JavaScript -->
    </div>
</div>

{{-- ECONOMIC INSIGHT --}}
<div class="glass-card" style="padding:24px;">
    <h3 style="font-size:16px;font-weight:700;margin-bottom:16px;color:var(--text-primary);">
        <i class="bi bi-lightbulb-fill" style="color:var(--accent);margin-right:8px;"></i>
        Economic Insight
    </h3>
    <div id="economicInsight" style="display:flex;flex-direction:column;gap:12px;">
        <div style="text-align:center;padding:20px;color:var(--text-muted);">
            <div class="intel-spinner" style="margin:0 auto 12px;"></div>
            <p style="font-size:13px;">Analyzing economic data...</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ============================================================
//  DATA VISUALIZATION DASHBOARD — Main Script
// ============================================================

const API_GDP_TREND = '{{ route("api.visualization.gdp") }}';
const API_INFLATION_TREND = '{{ route("api.visualization.inflation") }}';
const API_CURRENCY_TREND = '{{ route("api.visualization.currency") }}';
const API_RISK_TREND = '{{ route("api.visualization.risk") }}';

// Chart instances
let gdpChart = null;
let inflationChart = null;
let currencyChart = null;
let riskChart = null;

// ============================================================
//  INITIALIZE CHARTS
// ============================================================

function initCharts() {
    // GDP Chart
    const gdpCtx = document.getElementById('gdpChart');
    if (gdpCtx) {
        gdpChart = new Chart(gdpCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'GDP',
                    data: [],
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                }]
            },
            options: getChartOptions('GDP')
        });
    }

    // Inflation Chart
    const inflationCtx = document.getElementById('inflationChart');
    if (inflationCtx) {
        inflationChart = new Chart(inflationCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Inflation Rate (%)',
                    data: [],
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                }]
            },
            options: getChartOptions('Inflation (%)')
        });
    }

    // Currency Chart
    const currencyCtx = document.getElementById('currencyChart');
    if (currencyCtx) {
        currencyChart = new Chart(currencyCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Exchange Rate',
                    data: [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                }]
            },
            options: getChartOptions('Exchange Rate')
        });
    }

    // Risk Chart
    const riskCtx = document.getElementById('riskChart');
    if (riskCtx) {
        riskChart = new Chart(riskCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Risk Score',
                    data: [],
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                }]
            },
            options: getChartOptions('Risk Score')
        });
    }
}

function getChartOptions(label) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(31, 41, 55, 0.95)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: 'rgb(15, 118, 110)',
                borderWidth: 1,
                padding: 12,
                displayColors: false,
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                ticks: {
                    color: '#6B7280',
                    font: { size: 11 }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                ticks: {
                    color: '#6B7280',
                    font: { size: 11 },
                    maxRotation: 45,
                    minRotation: 45
                },
                grid: {
                    display: false
                }
            }
        }
    };
}

// ============================================================
//  UPDATE ALL CHARTS
// ============================================================

async function updateAllCharts() {
    const country = document.getElementById('countryFilter').value;
    const period = document.getElementById('periodFilter').value;

    // Show loading
    showLoading();

    try {
        // Fetch all data in parallel
        const [gdpData, inflationData, currencyData, riskData] = await Promise.all([
            fetch(`${API_GDP_TREND}?country=${country}&period=${period}`).then(r => r.json()),
            fetch(`${API_INFLATION_TREND}?country=${country}&period=${period}`).then(r => r.json()),
            fetch(`${API_CURRENCY_TREND}?country=${country}&period=${period}`).then(r => r.json()),
            fetch(`${API_RISK_TREND}?country=${country}&period=${period}`).then(r => r.json())
        ]);

        // Update charts
        updateGdpChart(gdpData);
        updateInflationChart(inflationData);
        updateCurrencyChart(currencyData);
        updateRiskChart(riskData);

        // Update summary analytics
        updateSummaryAnalytics(gdpData, inflationData, currencyData, riskData);

        // Generate economic insight
        generateEconomicInsight(gdpData, inflationData, currencyData, riskData);

        // Hide loading
        hideLoading();

    } catch (error) {
        console.error('Error updating charts:', error);
        hideLoading();
        showToast('Failed to load chart data', 'error');
    }
}

// ============================================================
//  UPDATE INDIVIDUAL CHARTS
// ============================================================

function updateGdpChart(data) {
    if (!gdpChart || !data.labels || !data.values) return;

    gdpChart.data.labels = data.labels;
    gdpChart.data.datasets[0].data = data.values;
    gdpChart.update('none'); // No animation for better performance

    // Update stats
    updateChartStats('gdp', data.stats, data.currency);
}

function updateInflationChart(data) {
    if (!inflationChart || !data.labels || !data.values) return;

    inflationChart.data.labels = data.labels;
    inflationChart.data.datasets[0].data = data.values;
    inflationChart.update('none');

    updateChartStats('inflation', data.stats, '%');
}

function updateCurrencyChart(data) {
    if (!currencyChart || !data.labels || !data.values) return;

    currencyChart.data.labels = data.labels;
    currencyChart.data.datasets[0].data = data.values;
    currencyChart.update('none');

    updateChartStats('currency', data.stats, data.currency + '/USD');
}

function updateRiskChart(data) {
    if (!riskChart || !data.labels || !data.values) return;

    riskChart.data.labels = data.labels;
    riskChart.data.datasets[0].data = data.values;
    riskChart.update('none');

    updateChartStats('risk', data.stats, 'pts');
}

// ============================================================
//  UPDATE CHART STATS
// ============================================================

function updateChartStats(type, stats, unit) {
    const container = document.getElementById(`${type}Stats`);
    if (!container || !stats) return;

    const formatValue = (val) => {
        if (type === 'gdp') {
            if (val >= 1_000_000_000_000) return (val / 1_000_000_000_000).toFixed(2) + 'T';
            if (val >= 1_000_000_000) return (val / 1_000_000_000).toFixed(2) + 'B';
            if (val >= 1_000_000) return (val / 1_000_000).toFixed(2) + 'M';
            return val.toFixed(0);
        }
        return val.toFixed(2);
    };

    const changeColor = stats.change >= 0 ? '#10B981' : '#EF4444';
    const changeIcon = stats.change >= 0 ? 'bi-arrow-up' : 'bi-arrow-down';

    container.innerHTML = `
        <div class="chart-stat-item">
            <div class="chart-stat-label">Current</div>
            <div class="chart-stat-value">${formatValue(stats.current)} <span style="font-size:11px;color:var(--text-muted);">${unit}</span></div>
        </div>
        <div class="chart-stat-item">
            <div class="chart-stat-label">Highest</div>
            <div class="chart-stat-value">${formatValue(stats.high)} <span style="font-size:11px;color:var(--text-muted);">${unit}</span></div>
        </div>
        <div class="chart-stat-item">
            <div class="chart-stat-label">Lowest</div>
            <div class="chart-stat-value">${formatValue(stats.low)} <span style="font-size:11px;color:var(--text-muted);">${unit}</span></div>
        </div>
        <div class="chart-stat-item">
            <div class="chart-stat-label">Average</div>
            <div class="chart-stat-value">${formatValue(stats.average)} <span style="font-size:11px;color:var(--text-muted);">${unit}</span></div>
        </div>
        <div class="chart-stat-item">
            <div class="chart-stat-label">Change</div>
            <div class="chart-stat-value" style="color:${changeColor};">
                <i class="bi ${changeIcon}"></i> ${stats.change >= 0 ? '+' : ''}${stats.change.toFixed(2)}%
            </div>
        </div>
    `;
}

// ============================================================
//  UPDATE SUMMARY ANALYTICS
// ============================================================

function updateSummaryAnalytics(gdp, inflation, currency, risk) {
    // GDP Summary
    const gdpTrend = gdp.stats.change >= 0 ? '↗ Increasing' : '↘ Decreasing';
    document.getElementById('summaryGdp').textContent = gdpTrend;
    document.getElementById('summaryGdp').style.color = gdp.stats.change >= 0 ? '#10B981' : '#EF4444';

    // Inflation Summary
    let inflationStatus = 'Stable';
    if (Math.abs(inflation.stats.change) < 5) inflationStatus = '→ Stable';
    else if (inflation.stats.change >= 5) inflationStatus = '↗ Rising';
    else inflationStatus = '↘ Declining';
    document.getElementById('summaryInflation').textContent = inflationStatus;
    document.getElementById('summaryInflation').style.color = inflation.stats.change >= 5 ? '#EF4444' : (Math.abs(inflation.stats.change) < 5 ? '#10B981' : '#F59E0B');

    // Currency Summary
    const currencyTrend = currency.stats.change >= 0 ? '↗ Strengthening' : '↘ Weakening';
    document.getElementById('summaryCurrency').textContent = currencyTrend;
    document.getElementById('summaryCurrency').style.color = currency.stats.change >= 0 ? '#10B981' : '#EF4444';

    // Risk Summary
    const riskTrend = risk.stats.change <= 0 ? '↘ Improving' : '↗ Worsening';
    document.getElementById('summaryRisk').textContent = riskTrend;
    document.getElementById('summaryRisk').style.color = risk.stats.change <= 0 ? '#10B981' : '#EF4444';
}

// ============================================================
//  GENERATE ECONOMIC INSIGHT
// ============================================================

function generateEconomicInsight(gdp, inflation, currency, risk) {
    const container = document.getElementById('economicInsight');
    if (!container) return;

    const insights = [];

    // GDP Insight
    if (gdp.stats.change >= 5) {
        insights.push({
            icon: 'bi-graph-up-arrow',
            color: '#10B981',
            text: `GDP has grown significantly by ${gdp.stats.change.toFixed(2)}% during this period, indicating strong economic performance.`
        });
    } else if (gdp.stats.change <= -5) {
        insights.push({
            icon: 'bi-graph-down-arrow',
            color: '#EF4444',
            text: `GDP has declined by ${Math.abs(gdp.stats.change).toFixed(2)}%, suggesting economic challenges that require attention.`
        });
    } else {
        insights.push({
            icon: 'bi-dash-circle',
            color: '#F59E0B',
            text: `GDP remains relatively stable with a ${gdp.stats.change >= 0 ? '+' : ''}${gdp.stats.change.toFixed(2)}% change.`
        });
    }

    // Inflation Insight
    if (inflation.stats.current > 5) {
        insights.push({
            icon: 'bi-exclamation-triangle',
            color: '#EF4444',
            text: `Inflation is elevated at ${inflation.stats.current.toFixed(2)}%, which may impact purchasing power and economic stability.`
        });
    } else if (inflation.stats.current < 2) {
        insights.push({
            icon: 'bi-check-circle',
            color: '#10B981',
            text: `Inflation is well-controlled at ${inflation.stats.current.toFixed(2)}%, supporting price stability.`
        });
    } else {
        insights.push({
            icon: 'bi-info-circle',
            color: '#3B82F6',
            text: `Inflation is moderate at ${inflation.stats.current.toFixed(2)}%, within acceptable range.`
        });
    }

    // Currency Insight
    if (Math.abs(currency.stats.change) > 5) {
        insights.push({
            icon: 'bi-currency-exchange',
            color: currency.stats.change >= 0 ? '#10B981' : '#EF4444',
            text: `Currency has ${currency.stats.change >= 0 ? 'strengthened' : 'weakened'} by ${Math.abs(currency.stats.change).toFixed(2)}%, affecting trade competitiveness.`
        });
    }

    // Risk Insight
    if (risk.stats.change > 10) {
        insights.push({
            icon: 'bi-shield-exclamation',
            color: '#EF4444',
            text: `Risk score has increased by ${risk.stats.change.toFixed(2)}%, indicating heightened risks that need monitoring.`
        });
    } else if (risk.stats.change < -10) {
        insights.push({
            icon: 'bi-shield-check',
            color: '#10B981',
            text: `Risk score has improved by ${Math.abs(risk.stats.change).toFixed(2)}%, showing positive risk management.`
        });
    }

    // Render insights
    container.innerHTML = insights.map(insight => `
        <div class="insight-item" style="padding:12px;background:var(--bg-base);border-left:3px solid ${insight.color};border-radius:6px;">
            <i class="bi ${insight.icon}" style="color:${insight.color};margin-right:8px;font-size:16px;"></i>
            <span style="font-size:13px;color:var(--text-secondary);">${insight.text}</span>
        </div>
    `).join('');
}

// ============================================================
//  LOADING HELPERS
// ============================================================

function showLoading() {
    ['gdpLoading', 'inflationLoading', 'currencyLoading', 'riskLoading'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'flex';
    });
}

function hideLoading() {
    ['gdpLoading', 'inflationLoading', 'currencyLoading', 'riskLoading'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}

// ============================================================
//  INIT
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
    initCharts();
    updateAllCharts();
});
</script>

<style>
/* Form Labels */
.form-label-viz {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Summary Analytics */
.summary-analytics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: var(--space-lg);
    margin-bottom: var(--space-xl);
}

.summary-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px !important;
}

.summary-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
}

.summary-content {
    flex: 1;
}

.summary-label {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: 4px;
}

.summary-value {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-primary);
}

/* Chart Header */
.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.chart-header h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-header h3 i {
    color: var(--accent);
}

.chart-loading {
    display: none;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text-muted);
}

/* Chart Stats */
.chart-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
}

.chart-stat-item {
    text-align: center;
    padding: 10px;
    background: var(--bg-base);
    border-radius: 8px;
}

.chart-stat-label {
    font-size: 10px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 4px;
}

.chart-stat-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
}

/* Insight Items */
.insight-item {
    display: flex;
    align-items: flex-start;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 768px) {
    .summary-analytics {
        grid-template-columns: 1fr;
    }
    
    .chart-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endpush
