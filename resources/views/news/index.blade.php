@extends('layouts.dashboard')
@section('title', 'News Intelligence — Supply Chain')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">News Intelligence</span>
        </div>
        <h1>News Intelligence</h1>
        <p>Real-time global trade and logistics news with sentiment analysis</p>
    </div>
    <button onclick="refreshNews()" class="btn-primary-custom">
        <i class="bi bi-arrow-clockwise"></i> Refresh News
    </button>
</div>

{{-- SENTIMENT SUMMARY --}}
<div class="stats-grid" style="margin-bottom:var(--space-xl);">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.2);">✅</div>
        <div class="stat-value" style="color:var(--risk-low);">{{ $sentimentCounts['Positive'] ?? 0 }}</div>
        <div class="stat-label">Positive Articles</div>
        <div class="stat-change up">{{ $totalNews > 0 ? round(($sentimentCounts['Positive'] ?? 0) / $totalNews * 100) : 0 }}% of total</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">➡️</div>
        <div class="stat-value" style="color:var(--text-muted);">{{ $sentimentCounts['Neutral'] ?? 0 }}</div>
        <div class="stat-label">Neutral Articles</div>
        <div class="stat-change">{{ $totalNews > 0 ? round(($sentimentCounts['Neutral'] ?? 0) / $totalNews * 100) : 0 }}% of total</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2);">❌</div>
        <div class="stat-value" style="color:var(--risk-critical);">{{ $sentimentCounts['Negative'] ?? 0 }}</div>
        <div class="stat-label">Negative Articles</div>
        <div class="stat-change down">{{ $totalNews > 0 ? round(($sentimentCounts['Negative'] ?? 0) / $totalNews * 100) : 0 }}% of total</div>
    </div>
    <div class="stat-card" style="align-items:center;display:flex;">
        <div style="width:100%;height:120px;">
            <canvas id="sentimentPie"></canvas>
        </div>
    </div>
</div>

{{-- CATEGORY TABS --}}
<div style="display:flex;gap:8px;margin-bottom:var(--space-lg);flex-wrap:wrap;">
    @foreach(['all'=>'All News','shipping'=>'Shipping','trade'=>'Trade','economy'=>'Economy','logistics'=>'Logistics','political'=>'Political'] as $key => $label)
    <a href="{{ route('news') }}?category={{ $key }}"
       style="padding:8px 16px;border-radius:999px;font-size:13px;font-weight:500;text-decoration:none;transition:all 0.2s;
              border:1px solid {{ $category === $key ? 'var(--accent)' : 'var(--border)' }};
              background:{{ $category === $key ? 'var(--accent-subtle)' : 'transparent' }};
              color:{{ $category === $key ? 'var(--accent)' : 'var(--text-muted)' }};">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- NEWS GRID --}}
@if($news->isEmpty())
<div class="glass-card" style="padding:64px;text-align:center;">
    <div style="font-size:48px;margin-bottom:12px;">📰</div>
    <div style="font-size:16px;font-weight:600;color:var(--text-primary);margin-bottom:8px;">No news available</div>
    <div style="font-size:13px;color:var(--text-muted);margin-bottom:20px;">Click "Refresh News" to fetch the latest articles.</div>
    <button onclick="refreshNews()" class="btn-primary-custom">
        <i class="bi bi-arrow-clockwise"></i> Fetch News Now
    </button>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px;margin-bottom:var(--space-xl);">
    @foreach($news as $item)
    <a href="{{ $item['url'] }}" target="_blank"
       class="glass-card"
       style="padding:0;text-decoration:none;color:inherit;overflow:hidden;display:flex;flex-direction:column;">

        @if($item['image'])
        <img src="{{ $item['image'] }}" alt=""
             style="width:100%;height:160px;object-fit:cover;"
             onerror="this.style.display='none'">
        @else
        <div style="width:100%;height:100px;background:linear-gradient(135deg,var(--bg-card),var(--bg-base));display:flex;align-items:center;justify-content:center;font-size:32px;">📰</div>
        @endif

        <div style="padding:16px;flex:1;display:flex;flex-direction:column;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                @if($item['sentiment'])
                <span class="sentiment-{{ strtolower($item['sentiment']) }}" style="font-size:10px;padding:2px 8px;">
                    {{ $item['sentiment'] === 'Positive' ? '✅' : ($item['sentiment'] === 'Negative' ? '❌' : '→') }} {{ $item['sentiment'] }}
                </span>
                @endif
                @if($item['source'])
                <span style="font-size:10px;color:var(--text-muted);">{{ $item['source'] }}</span>
                @endif
                <span style="font-size:10px;color:var(--text-muted);margin-left:auto;">
                    {{ \Carbon\Carbon::parse($item['published_at'])->diffForHumans() }}
                </span>
            </div>

            <div style="font-size:14px;font-weight:600;color:var(--text-primary);line-height:1.4;flex:1;">
                {{ Str::limit($item['title'], 90) }}
            </div>

            @if($item['description'])
            <div style="font-size:12px;color:var(--text-muted);margin-top:8px;line-height:1.5;">
                {{ Str::limit($item['description'], 100) }}
            </div>
            @endif
        </div>

    </a>
    @endforeach
</div>

{{-- PAGINATION --}}
<div style="display:flex;justify-content:center;">
    {{ $news->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
// Sentiment Pie Chart
const pieCtx = document.getElementById('sentimentPie');
if (pieCtx) {
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Positive', 'Neutral', 'Negative'],
            datasets: [{
                data: [{{ $sentimentCounts['Positive'] ?? 0 }}, {{ $sentimentCounts['Neutral'] ?? 0 }}, {{ $sentimentCounts['Negative'] ?? 0 }}],
                backgroundColor: ['rgba(16,185,129,0.8)', 'rgba(100,116,139,0.6)', 'rgba(239,68,68,0.8)'],
                borderColor: 'transparent',
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false }
            }
        }
    });
}

function refreshNews() {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<div class="intel-spinner" style="width:14px;height:14px;"></div> Fetching...';

    fetch('/api/news/refresh', { headers: { 'X-CSRF-TOKEN': CSRF_TOKEN } })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        })
        .catch(() => showToast('Failed to fetch news', 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh News';
        });
}
</script>
@endpush
