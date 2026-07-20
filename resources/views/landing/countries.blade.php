@extends('layouts.app')

@section('title', 'Countries - HandWorld')

@section('content')

@include('partials.navbar')

<div style="padding-top: 90px; min-height: 100vh; background: #F5F7F4;">
    
    {{-- Hero Section --}}
    <section class="container py-5">
        <div class="text-center mb-4" data-aos="fade-up">
            <span class="badge bg-light text-success px-4 py-2 rounded-pill mb-3">
                <i class="bi bi-globe2"></i> Global Coverage
            </span>
            <h1 class="display-4 fw-bold mb-3" style="color: #1F2937;">
                Explore Countries Worldwide
            </h1>
            <p class="lead text-muted">
                Monitor weather, risk levels, and key metrics for 250+ countries
            </p>
        </div>
    </section>

    {{-- Countries Grid --}}
    <section class="container pb-5">
        <div class="row g-3">
            @foreach($countries as $country)
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="country-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $country->flag_png }}" 
                             alt="{{ $country->country_name }}" 
                             class="country-flag"
                             onerror="this.src='https://flagcdn.com/w80/{{ strtolower($country->country_code) }}.png'">
                        <div class="flex-grow-1">
                            <h5 class="mb-1 fw-bold country-name">{{ $country->country_name }}</h5>
                            <small class="text-muted">{{ $country->region }}</small>
                        </div>
                    </div>

                    <div class="country-stats">
                        {{-- Weather --}}
                        <div class="stat-item">
                            <i class="bi bi-cloud-sun-fill text-warning"></i>
                            <span>
                                {{ $country->weatherCache?->temperature ? round($country->weatherCache->temperature) . '°C' : 'N/A' }}
                            </span>
                        </div>

                        {{-- Risk Level --}}
                        <div class="stat-item">
                            <i class="bi bi-shield-fill-exclamation" style="color: {{ $country->riskScore?->risk_color ?? '#6B7280' }}"></i>
                            <span class="risk-badge" style="background: {{ $country->riskScore?->risk_color ?? '#6B7280' }}20; color: {{ $country->riskScore?->risk_color ?? '#6B7280' }};">
                                {{ $country->riskScore?->risk_level ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('countries.show', $country->country_code) }}" class="btn-view-country">
                        <i class="bi bi-arrow-right-circle"></i> View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-5">
            {{ $countries->links() }}
        </div>
    </section>

</div>

<style>
.country-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s ease;
    border: 1px solid #E5E7EB;
    height: 100%;
}

.country-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(15, 118, 110, 0.12);
    border-color: #0F766E;
}

.country-flag {
    width: 48px;
    height: 36px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #E5E7EB;
}

.country-name {
    color: #1F2937;
    font-size: 18px;
}

.country-stats {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    padding-top: 16px;
    border-top: 1px solid #F3F4F6;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #374151;
}

.stat-item i {
    font-size: 18px;
}

.risk-badge {
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}

.btn-view-country {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #0F766E, #065F46);
    color: white;
    border: none;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-view-country:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 118, 110, 0.3);
}
</style>

{{-- AOS Animation --}}
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 600,
    once: true,
    offset: 50
  });
</script>

{{-- Simple Footer --}}
<footer style="background: white; padding: 24px 0; text-align: center; border-top: 1px solid #E5E7EB;">
    <div class="container">
        <p style="margin: 0; font-size: 15px; color: #6B7280;">© 2026 Hana Marmella</p>
    </div>
</footer>

@endsection
