@extends('layouts.admin')
@section('title', 'News Intelligence')
@section('breadcrumb', 'News Intelligence')

@section('content')

<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="bi bi-newspaper me-2" style="color:var(--admin-accent);"></i>News Intelligence</h1>
        <p>{{ $news->total() }} cached news articles</p>
    </div>
    <button class="btn-admin btn-admin-primary" onclick="refreshNews()" id="btn-refresh-news">
        <i class="bi bi-arrow-clockwise"></i> Refresh News
    </button>
</div>

{{-- FILTERS --}}
<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.news-cache') }}" class="d-flex gap-3 align-items-center flex-wrap">
        <div class="admin-search-wrap" style="flex:1;min-width:200px;">
            <i class="bi bi-search admin-search-icon"></i>
            <input type="text" name="search" class="admin-input" placeholder="Search title..."
                   value="{{ $search ?? '' }}">
        </div>
        <select name="country_id" class="admin-input" style="width:auto;min-width:180px;">
            <option value="">All Countries</option>
            @foreach($countries as $c)
            <option value="{{ $c->id }}" {{ ($countryId ?? '') == $c->id ? 'selected' : '' }}>
                {{ $c->country_name }}
            </option>
            @endforeach
        </select>
        <select name="sentiment" class="admin-input" style="width:auto;min-width:140px;">
            <option value="">All Sentiments</option>
            <option value="Positive" {{ ($sentiment ?? '') === 'Positive' ? 'selected' : '' }}>Positive</option>
            <option value="Negative" {{ ($sentiment ?? '') === 'Negative' ? 'selected' : '' }}>Negative</option>
            <option value="Neutral"  {{ ($sentiment ?? '') === 'Neutral' ? 'selected' : '' }}>Neutral</option>
        </select>
        <button type="submit" class="btn-admin btn-admin-primary"><i class="bi bi-funnel"></i> Filter</button>
        @if($search || $countryId || $sentiment)
        <a href="{{ route('admin.news-cache') }}" class="btn-admin btn-admin-ghost"><i class="bi bi-x"></i> Clear</a>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div class="admin-card" style="padding:0;">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Country</th>
                    <th>Sentiment</th>
                    <th>Score</th>
                    <th>Source</th>
                    <th>Published</th>
                </tr>
            </thead>
            <tbody>
                @forelse($news as $n)
                <tr>
                    <td style="color:var(--admin-muted);font-size:12px;">{{ $n->id }}</td>
                    <td style="max-width:320px;">
                        <div style="font-weight:600;font-size:13px;line-height:1.4;">
                            {{ Str::limit($n->title, 65) }}
                        </div>
                        @if($n->url && $n->url !== '#')
                        <a href="{{ $n->url }}" target="_blank" style="font-size:11px;color:var(--admin-accent);text-decoration:none;">
                            <i class="bi bi-box-arrow-up-right"></i> View
                        </a>
                        @endif
                    </td>
                    <td>
                        @if($n->country)
                        <div style="display:flex;align-items:center;gap:6px;">
                            <img src="https://flagcdn.com/w20/{{ strtolower($n->country->country_code) }}.png"
                                 width="16" height="11" style="border-radius:2px;" alt="">
                            <span style="font-size:12px;">{{ $n->country->country_name }}</span>
                        </div>
                        @else
                        <span style="color:var(--admin-muted);font-size:11px;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="admin-badge badge-{{ strtolower($n->sentiment ?? 'neu') }}">
                            {{ $n->sentiment_icon ?? '→' }} {{ $n->sentiment ?? 'Neutral' }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--admin-muted);">
                        {{ $n->sentiment_score !== null ? number_format($n->sentiment_score, 2) : '—' }}
                    </td>
                    <td style="font-size:12px;color:var(--admin-muted);">{{ $n->source ?? '—' }}</td>
                    <td style="font-size:11px;color:var(--admin-muted);">
                        {{ $n->published_at?->format('d M Y') ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--admin-muted);">
                        <i class="bi bi-newspaper" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No news cached yet. Click Refresh to fetch news.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($news->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $news->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function refreshNews() {
    const btn = document.getElementById('btn-refresh-news');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Fetching...';

    fetch('{{ route("admin.news-cache.refresh") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        adminToast(data.message, data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 1000);
    })
    .catch(() => adminToast('Refresh failed', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh News';
    });
}
</script>
@endpush
