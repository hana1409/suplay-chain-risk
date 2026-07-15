@extends('layouts.admin')
@section('title', $article ? 'Edit Article' : 'Create Article')
@section('breadcrumb', $article ? 'Edit Article' : 'New Article')

@section('content')

<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1>
            <i class="bi bi-file-text-fill me-2" style="color:var(--admin-accent);"></i>
            {{ $article ? 'Edit Article' : 'Create New Article' }}
        </h1>
        <p>{{ $article ? 'Update article — ' . $article->title : 'Write and publish a new article' }}</p>
    </div>
    <a href="{{ route('admin.articles') }}" class="btn-admin btn-admin-ghost">
        <i class="bi bi-arrow-left"></i> Back to Articles
    </a>
</div>

<form method="POST"
      action="{{ $article ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
      id="articleForm">
    @csrf
    @if($article) @method('PUT') @endif

    <div class="row g-4">
        {{-- MAIN CONTENT --}}
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <label class="admin-form-label">Title *</label>
                <input type="text" name="title" id="titleInput" class="admin-input @error('title') is-invalid @enderror"
                       value="{{ old('title', $article?->title) }}" required
                       placeholder="Article title..." oninput="generateSlug()">
                @error('title')
                <div style="color:#FC8181;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="admin-card mb-4">
                <label class="admin-form-label">Content *</label>
                <textarea name="content" class="admin-input @error('content') is-invalid @enderror"
                          rows="18" required
                          placeholder="Write your article content here...">{{ old('content', $article?->content) }}</textarea>
                @error('content')
                <div style="color:#FC8181;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="col-lg-4">
            <div class="admin-card mb-4">
                <div class="admin-card-header" style="margin-bottom:16px;">
                    <div class="admin-card-title"><i class="bi bi-gear"></i> Publish Settings</div>
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">Status</label>
                    <select name="status" class="admin-input">
                        <option value="Draft" {{ old('status', $article?->status) === 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Published" {{ old('status', $article?->status) === 'Published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">Category</label>
                    <input type="text" name="category" class="admin-input"
                           value="{{ old('category', $article?->category) }}"
                           placeholder="e.g. Trade Risk, Market Analysis">
                </div>

                <div class="mb-4">
                    <label class="admin-form-label">Slug (URL)</label>
                    <input type="text" name="slug" id="slugInput" class="admin-input"
                           value="{{ old('slug', $article?->slug) }}"
                           placeholder="auto-generated-from-title">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary" style="flex:1;">
                        <i class="bi bi-check-lg"></i>
                        {{ $article ? 'Update' : 'Publish' }}
                    </button>
                    <a href="{{ route('admin.articles') }}" class="btn-admin btn-admin-ghost">
                        Cancel
                    </a>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header" style="margin-bottom:16px;">
                    <div class="admin-card-title"><i class="bi bi-image"></i> Thumbnail</div>
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">Image URL</label>
                    <input type="url" name="image" id="imageUrlInput" class="admin-input"
                           value="{{ old('image', $article?->image) }}"
                           placeholder="https://..." oninput="previewImage()">
                </div>

                <div id="imagePreview" style="{{ ($article?->image || old('image')) ? '' : 'display:none;' }}margin-top:8px;">
                    <img id="previewImg"
                         src="{{ old('image', $article?->image ?? '') }}"
                         alt="Preview"
                         style="width:100%;border-radius:8px;border:1px solid var(--admin-border);object-fit:cover;max-height:160px;">
                </div>

                @error('image')
                <div style="color:#FC8181;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
function generateSlug() {
    const title = document.getElementById('titleInput').value;
    const slug  = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('slugInput').value = slug;
}

function previewImage() {
    const url     = document.getElementById('imageUrlInput').value;
    const preview = document.getElementById('imagePreview');
    const img     = document.getElementById('previewImg');
    if (url) {
        img.src = url;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush
