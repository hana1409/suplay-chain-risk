@extends('layouts.dashboard')
@section('title', 'Admin — News Cache')
@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb"><a href="{{ route('admin.dashboard') }}">Admin</a><i class="bi bi-chevron-right"></i><span style="color:var(--accent)">News Cache</span></div>
        <h1>News Cache</h1><p>Cached news articles with sentiment analysis</p>
    </div>
</div>
<div class="glass-card" style="overflow:hidden;">
    <table class="intel-table">
        <thead><tr><th>Title</th><th>Source</th><th>Sentiment</th><th>Country</th><th>Published</th></tr></thead>
        <tbody>
            @foreach($news as $n)
            <tr>
                <td style="max-width:300px;">
                    <a href="{{ $n->url }}" target="_blank" style="font-size:12px;font-weight:600;color:var(--text-primary);text-decoration:none;">{{ Str::limit($n->title, 70) }}</a>
                </td>
                <td style="color:var(--text-muted);font-size:12px;">{{ $n->source ?? '—' }}</td>
                <td><span class="sentiment-{{ strtolower($n->sentiment ?? 'neutral') }}" style="font-size:10px;padding:1px 7px;">{{ $n->sentiment ?? 'Neutral' }}</span></td>
                <td style="font-size:12px;color:var(--text-muted);">{{ $n->country?->country_name ?? 'Global' }}</td>
                <td style="font-size:11px;color:var(--text-muted);">{{ $n->published_at?->diffForHumans() ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:16px;border-top:1px solid var(--border);">{{ $news->links() }}</div>
</div>
@endsection
