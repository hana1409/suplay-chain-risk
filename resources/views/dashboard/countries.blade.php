@extends('layouts.dashboard')
@section('title', 'Countries — Global Risk Intelligence')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">Countries</span>
        </div>
        <h1>Global Countries</h1>
        <p>{{ $countries->total() }} countries monitored worldwide</p>
    </div>
    <a href="{{ route('compare') }}" class="btn-primary-custom">
        <i class="bi bi-bar-chart-steps"></i> Compare Countries
    </a>
</div>

{{-- SEARCH --}}
<div class="glass-card" style="padding:20px;margin-bottom:var(--space-xl);">
    <form method="GET" action="{{ route('countries') }}" style="display:flex;gap:12px;align-items:center;">
        <div class="search-input-wrap" style="flex:1;max-width:400px;">
            <i class="bi bi-search"></i>
            <input name="search" class="intel-input" placeholder="Search countries..." value="{{ request('search') }}" autocomplete="off">
        </div>
        <select name="region" class="intel-input" style="width:auto;padding:10px 14px;" onchange="this.form.submit()">
            <option value="">All Regions</option>
            @foreach(['Africa','Americas','Asia','Europe','Oceania','Antarctic'] as $r)
            <option value="{{ $r }}" {{ request('region') == $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary-custom">Search</button>
        @if(request('search') || request('region'))
        <a href="{{ route('countries') }}" class="btn-ghost">Clear</a>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div class="glass-card" style="overflow:hidden;">
    <table class="intel-table">
        <thead>
            <tr>
                <th>Country</th>
                <th>Region</th>
                <th>Population</th>
                <th>Currency</th>
                <th>Risk Score</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($countries as $country)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <img src="https://flagcdn.com/w20/{{ strtolower($country->country_code) }}.png"
                             width="24" height="16"
                             style="border-radius:2px;border:1px solid var(--border);object-fit:cover;"
                             alt="">
                        <div>
                            <div style="font-weight:600;color:var(--text-primary);font-size:13px;">{{ $country->country_name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $country->capital }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span style="font-size:12px;color:var(--text-muted);background:var(--bg-base);padding:3px 8px;border-radius:999px;">
                        {{ $country->region ?? '—' }}
                    </span>
                </td>
                <td style="font-size:13px;">{{ $country->formatted_population }}</td>
                <td style="font-size:13px;color:var(--text-muted);">{{ $country->currency_code ?? '—' }}</td>
                <td>
                    @if($country->riskScore)
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="height:4px;width:60px;background:var(--bg-base);border-radius:2px;overflow:hidden;">
                            <div style="height:100%;width:{{ $country->riskScore->total_score }}%;background:{{ $country->riskScore->risk_color }};"></div>
                        </div>
                        <span style="font-size:12px;font-weight:700;color:{{ $country->riskScore->risk_color }};">
                            {{ number_format($country->riskScore->total_score, 1) }}
                        </span>
                        <span class="risk-badge risk-{{ strtolower($country->riskScore->risk_level) }}" style="font-size:10px;padding:2px 7px;">
                            {{ $country->riskScore->risk_level }}
                        </span>
                    </div>
                    @else
                    <span style="font-size:12px;color:var(--text-muted);">No Data</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:6px;">
                        <a href="{{ route('countries.show', $country->country_code) }}" class="btn-primary-custom" style="padding:6px 12px;font-size:11px;">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="{{ route('compare') }}?a={{ $country->country_code }}" class="btn-ghost" style="padding:6px 10px;font-size:11px;">
                            <i class="bi bi-bar-chart-steps"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:48px;color:var(--text-muted);">
                    <i class="bi bi-globe" style="font-size:36px;display:block;margin-bottom:12px;"></i>
                    No countries found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding:20px;border-top:1px solid var(--border);">
        {{ $countries->links() }}
    </div>
</div>

@endsection