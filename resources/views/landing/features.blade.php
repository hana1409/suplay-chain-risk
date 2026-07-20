@extends('layouts.app')

@section('title', 'Features - HandWorld')

@section('content')

@include('partials.navbar')

<div style="padding-top: 90px; min-height: 100vh; background: linear-gradient(180deg, #F0FDF4 0%, #FFFFFF 100%);">
    
    {{-- Hero Section --}}
    <section class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-light text-success px-4 py-2 rounded-pill mb-3" style="font-size: 14px;">
                <i class="bi bi-lightning-charge-fill"></i> Platform Features
            </span>
            <h1 class="display-4 fw-bold mb-3" style="color: #1F2937;">
                Powerful Features for<br>
                Global Supply Chain Intelligence
            </h1>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                Everything you need to monitor and analyze global supply chain risks in real-time
            </p>
        </div>
    </section>

    {{-- Features Grid --}}
    <section class="container pb-5">
        <div class="row g-4">
            
            {{-- Weather Monitoring --}}
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="bi bi-cloud-sun-fill"></i>
                    </div>
                    <h3 class="feature-title">Weather Monitoring</h3>
                    <p class="feature-desc">
                        Real-time weather tracking for 250+ countries and major ports worldwide. Get instant alerts on severe weather conditions affecting logistics.
                    </p>
                    <ul class="feature-list">
                        <li><i class="bi bi-check-circle-fill text-success"></i> Real-time weather data</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Port-specific forecasts</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Severe weather alerts</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Historical weather analysis</li>
                    </ul>
                </div>
            </div>

            {{-- Global Port Monitoring --}}
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #3B82F6, #1E40AF);">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <h3 class="feature-title">Global Port Monitoring</h3>
                    <p class="feature-desc">
                        Monitor 150+ major seaports worldwide with real-time status updates, congestion levels, and operational risks.
                    </p>
                    <ul class="feature-list">
                        <li><i class="bi bi-check-circle-fill text-success"></i> Port status tracking</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Congestion indicators</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Weather at ports</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Interactive port map</li>
                    </ul>
                </div>
            </div>

            {{-- Exchange Rate --}}
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <h3 class="feature-title">Exchange Rate Monitoring</h3>
                    <p class="feature-desc">
                        Track currency exchange rates for global trade. Real-time updates and historical trends for informed decision-making.
                    </p>
                    <ul class="feature-list">
                        <li><i class="bi bi-check-circle-fill text-success"></i> Multi-currency support</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Real-time rate updates</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Historical trends</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Rate change alerts</li>
                    </ul>
                </div>
            </div>

            {{-- Global News --}}
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #EF4444, #DC2626);">
                        <i class="bi bi-newspaper"></i>
                    </div>
                    <h3 class="feature-title">Global News Intelligence</h3>
                    <p class="feature-desc">
                        AI-powered sentiment analysis on global trade news. Stay informed about events affecting supply chains.
                    </p>
                    <ul class="feature-list">
                        <li><i class="bi bi-check-circle-fill text-success"></i> Sentiment analysis</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Trade-focused filtering</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Real-time updates</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Multilingual support</li>
                    </ul>
                </div>
            </div>

            {{-- Risk Dashboard --}}
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED);">
                        <i class="bi bi-shield-fill-exclamation"></i>
                    </div>
                    <h3 class="feature-title">Risk Dashboard</h3>
                    <p class="feature-desc">
                        Comprehensive risk scoring system combining weather, economic, currency, and news data for each country.
                    </p>
                    <ul class="feature-list">
                        <li><i class="bi bi-check-circle-fill text-success"></i> Multi-factor risk scoring</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Visual risk indicators</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Country comparisons</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Risk trend analysis</li>
                    </ul>
                </div>
            </div>

            {{-- Interactive Map --}}
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card h-100">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #10B981, #059669);">
                        <i class="bi bi-globe-americas"></i>
                    </div>
                    <h3 class="feature-title">Interactive Global Map</h3>
                    <p class="feature-desc">
                        Visualize global data on an interactive map. Explore weather, ports, and risk levels with a single click.
                    </p>
                    <ul class="feature-list">
                        <li><i class="bi bi-check-circle-fill text-success"></i> Real-time data visualization</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Layer-based filtering</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Zoom & explore countries</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Port location markers</li>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    {{-- CTA Section --}}
    <section class="container py-5 my-5">
        <div class="text-center p-5 rounded-4" style="background: linear-gradient(135deg, #0F766E, #065F46);" data-aos="zoom-in">
            <h2 class="text-white fw-bold mb-3">Ready to Get Started?</h2>
            <p class="text-white mb-4 opacity-75">Join thousands of supply chain professionals using HandWorld</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('login') }}" class="btn btn-light px-5 py-3 rounded-pill fw-bold">
                    Login
                </a>
                <a href="{{ route('landing.contact') }}" class="btn btn-outline-light px-5 py-3 rounded-pill fw-bold">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

</div>

<style>
.feature-card {
    background: white;
    border-radius: 20px;
    padding: 32px;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #E5E7EB;
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0F766E, #10B981);
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(15, 118, 110, 0.15);
    border-color: #0F766E;
}

.feature-card:hover::before {
    transform: scaleX(1);
}

.feature-icon {
    width: 70px;
    height: 70px;
    border-radius: 16px;
    background: linear-gradient(135deg, #0F766E, #065F46);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    transition: all 0.4s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
}

.feature-icon i {
    font-size: 32px;
    color: white;
}

.feature-title {
    font-size: 22px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 12px;
}

.feature-desc {
    color: #6B7280;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 20px;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    padding: 8px 0;
    font-size: 14px;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 10px;
}

.feature-list li i {
    font-size: 16px;
}
</style>

{{-- AOS Animation --}}
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
    offset: 100
  });
</script>

{{-- Simple Footer --}}
<footer style="background: white; padding: 24px 0; text-align: center; border-top: 1px solid #E5E7EB;">
    <div class="container">
        <p style="margin: 0; font-size: 15px; color: #6B7280;">© 2026 Hana Marmella</p>
    </div>
</footer>

@endsection
