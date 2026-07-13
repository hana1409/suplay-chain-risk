@extends('layouts.dashboard')
@section('title', 'Admin — Articles')
@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb"><a href="{{ route('admin.dashboard') }}">Admin</a><i class="bi bi-chevron-right"></i><span style="color:var(--accent)">Articles</span></div>
        <h1>Articles</h1>
    </div>
</div>
<div class="glass-card" style="overflow:hidden;">
    <table class="intel-table">
        <thead><tr><th>Title</th><th>Author</th><th>Published</th></tr></thead>
        <tbody>
            @forelse($articles as $a)
            <tr>
                <td style="font-weight:600;font-size:13px;">{{ Str::limit($a->title ?? 'Untitled', 60) }}</td>
                <td style="color:var(--text-muted);">{{ $a->user?->name ?? '—' }}</td>
                <td style="color:var(--text-muted);font-size:12px;">{{ $a->created_at?->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;padding:40px;color:var(--text-muted);">No articles found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px;border-top:1px solid var(--border);">{{ $articles->links() }}</div>
</div>
@endsection
