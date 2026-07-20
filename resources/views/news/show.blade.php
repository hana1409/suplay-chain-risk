@extends('layouts.dashboard')
@section('title', $article->title . ' — Supply Chain')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('news') }}">News</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">Article</span>
        </div>
        <h1>{{ $article->title }}</h1>
        @if($article->category)
        <p>Category: <span style="color:var(--accent);">{{ $article->category }}</span></p>
        @endif
    </div>
    <a href="{{ route('news') }}" class="btn-primary-custom">
        <i class="bi bi-arrow-left"></i> Back to News
    </a>
</div>

<div class="glass-card" style="max-width:900px;margin:0 auto;">
    @if($article->image)
    <img src="{{ $article->image }}" alt="{{ $article->title }}"
         style="width:100%;max-height:400px;object-fit:cover;border-radius:8px;margin-bottom:24px;"
         onerror="this.style.display='none'">
    @endif

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--border);">
        <div style="font-size:12px;color:var(--text-muted);">
            <i class="bi bi-person-circle"></i> {{ $article->user->name ?? 'Admin' }}
        </div>
        <div style="font-size:12px;color:var(--text-muted);">
            <i class="bi bi-calendar3"></i> {{ $article->created_at->format('M d, Y') }}
        </div>
        @if($article->category)
        <div style="font-size:12px;padding:4px 12px;background:var(--accent-subtle);color:var(--accent);border-radius:999px;">
            {{ $article->category }}
        </div>
        @endif
    </div>

    <div style="line-height:1.8;font-size:15px;color:var(--text-primary);">
        {!! nl2br(e($article->content)) !!}
    </div>
</div>

@endsection
