@extends('layouts.admin')
@section('title', 'Articles')
@section('breadcrumb', 'Articles')

@section('content')

<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="bi bi-file-text-fill me-2" style="color:var(--admin-accent);"></i>Article Management</h1>
        <p>{{ $articles->total() }} articles total</p>
    </div>
    <a href="{{ route('admin.articles.create') }}" class="btn-admin btn-admin-primary">
        <i class="bi bi-plus-lg"></i> New Article
    </a>
</div>

{{-- FILTERS --}}
<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.articles') }}" class="d-flex gap-3 align-items-center flex-wrap">
        <div class="admin-search-wrap" style="flex:1;min-width:200px;">
            <i class="bi bi-search admin-search-icon"></i>
            <input type="text" name="search" class="admin-input" placeholder="Search title..."
                   value="{{ $search ?? '' }}">
        </div>
        <select name="status" class="admin-input" style="width:auto;min-width:140px;">
            <option value="">All Status</option>
            <option value="Published" {{ ($status ?? '') === 'Published' ? 'selected' : '' }}>Published</option>
            <option value="Draft" {{ ($status ?? '') === 'Draft' ? 'selected' : '' }}>Draft</option>
        </select>
        <button type="submit" class="btn-admin btn-admin-primary"><i class="bi bi-funnel"></i> Filter</button>
        @if($search || $status)
        <a href="{{ route('admin.articles') }}" class="btn-admin btn-admin-ghost"><i class="bi bi-x"></i> Clear</a>
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
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $a)
                <tr id="article-row-{{ $a->id }}">
                    <td style="color:var(--admin-muted);font-size:12px;">{{ $a->id }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;max-width:280px;">{{ Str::limit($a->title, 60) }}</div>
                        <div style="font-size:11px;color:var(--admin-muted);">/{{ $a->slug }}</div>
                    </td>
                    <td style="font-size:12px;color:var(--admin-muted);">{{ $a->category ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $a->user?->name ?? '—' }}</td>
                    <td>
                        <span class="admin-badge {{ $a->status === 'Published' ? 'badge-pub' : 'badge-draft' }}">
                            {{ $a->status }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--admin-muted);">{{ $a->created_at?->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.articles.edit', $a) }}"
                               class="btn-admin btn-admin-ghost btn-admin-sm btn-admin-icon" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn-admin btn-admin-danger btn-admin-sm btn-admin-icon"
                                    title="Delete"
                                    onclick="deleteArticle({{ $a->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--admin-muted);">
                        <i class="bi bi-file-text" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No articles found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($articles->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $articles->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function deleteArticle(articleId) {
    adminDelete(`/admin/articles/${articleId}`, 'Delete this article permanently?', () => {
        const row = document.getElementById(`article-row-${articleId}`);
        if (row) {
            row.style.opacity = '0';
            row.style.transition = 'opacity 0.3s';
            setTimeout(() => row.remove(), 300);
        }
    });
}
</script>
@endpush
