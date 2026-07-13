@extends('layouts.dashboard')
@section('title', 'Country Comparison — Risk Intelligence')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">Comparison</span>
        </div>
        <h1>Country Comparison</h1>
        <p>Compare supply chain risk between two countries</p>
    </div>
</div>

{{-- SELECTOR --}}
<div class="glass-card" style="padding:28px;margin-bottom:var(--space-xl);">
    <form action="{{ route('compare.process') }}" method="POST" style="display:flex;gap:20px;align-items:flex-end;flex-wrap:wrap;">
        @csrf
        <div style="flex:1;min-width:200px;">
            <label style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;display:block;">Country A</label>
            <select name="country_a" class="intel-input" required>
                <option value="">Select Country A</option>
                @foreach($countries as $c)
                <option value="{{ $c->country_code }}" {{ $preselected === $c->country_code ? 'selected' : '' }}>
                    {{ $c->country_name }}
                </option>
                @endforeach
            </select>
        </div>

        <div style="display:flex;align-items:center;padding-bottom:12px;font-size:20px;color:var(--text-muted);">
            <i class="bi bi-arrows-angle-contract"></i>
        </div>

        <div style="flex:1;min-width:200px;">
            <label style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;display:block;">Country B</label>
            <select name="country_b" class="intel-input" required>
                <option value="">Select Country B</option>
                @foreach($countries as $c)
                <option value="{{ $c->country_code }}">{{ $c->country_name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-primary-custom" style="padding:12px 28px;">
            <i class="bi bi-bar-chart-steps"></i> Compare Now
        </button>
    </form>
</div>

{{-- QUICK COMPARISON EXAMPLES --}}
<div class="section-header">
    <div class="section-title"><span></span> Quick Compare</div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:var(--space-xl);">
    @php
    $quickPairs = [
        ['US','CN','USA vs China'],
        ['DE','FR','Germany vs France'],
        ['JP','KR','Japan vs Korea'],
        ['IN','ID','India vs Indonesia'],
        ['BR','MX','Brazil vs Mexico'],
        ['AU','NZ','Australia vs New Zealand'],
    ];
    @endphp
    @foreach($quickPairs as $pair)
    <a href="{{ route('compare.show', [$pair[0], $pair[1]]) }}"
       class="glass-card"
       style="padding:14px;text-decoration:none;display:flex;align-items:center;gap:10px;transition:var(--transition);"
       onmouseover="this.style.borderColor='var(--accent)'"
       onmouseout="this.style.borderColor=''">
        <img src="https://flagcdn.com/w20/{{ strtolower($pair[0]) }}.png" width="20" height="14" style="border-radius:2px;" alt="">
        <span style="font-size:11px;color:var(--text-muted);">vs</span>
        <img src="https://flagcdn.com/w20/{{ strtolower($pair[1]) }}.png" width="20" height="14" style="border-radius:2px;" alt="">
        <span style="font-size:12px;color:var(--text-primary);font-weight:600;">{{ $pair[2] }}</span>
        <i class="bi bi-arrow-right" style="margin-left:auto;color:var(--accent);font-size:11px;"></i>
    </a>
    @endforeach
</div>

@endsection
