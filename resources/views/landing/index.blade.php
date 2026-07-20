@extends('layouts.app')

@section('title','HandWorld - Global Supply Chain Risk Intelligence')

@section('content')

@include('partials.navbar')

@include('partials.hero')

{{-- Statistics Section --}}
<section class="py-5" style="background: white;">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-md-6" data-aos="fade-up">
                <div class="stat-box">
                    <div class="stat-number">250+</div>
                    <div class="stat-label">Countries Monitored</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-box">
                    <div class="stat-number">150+</div>
                    <div class="stat-label">Major Ports Tracked</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-box">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Real-time Updates</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-box">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">Uptime Guarantee</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Dashboard Preview Section --}}
<section class="py-5" style="background: linear-gradient(180deg, #FFFFFF 0%, #F0FDF4 100%);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-light text-success px-4 py-2 rounded-pill mb-3">
                <i class="bi bi-display"></i> Platform Overview
            </span>
            <h2 class="display-5 fw-bold mb-3" style="color: #1F2937;">
                Powerful Dashboard at Your Fingertips
            </h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                Monitor global supply chain risks with our intuitive, real-time dashboard
            </p>
        </div>

        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="dashboard-preview-card">
                    <img src="{{ asset('images/hand.png') }}" 
                         alt="HandWorld Dashboard Preview" 
                         class="dashboard-image">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="features-list">
                    <div class="feature-item">
                        <div class="feature-icon-small" style="background: linear-gradient(135deg, #0F766E, #065F46);">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">Real-time Monitoring</h5>
                            <p class="text-muted mb-0">Get instant updates on weather, risk levels, and supply chain disruptions</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-small" style="background: linear-gradient(135deg, #3B82F6, #1E40AF);">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">Advanced Analytics</h5>
                            <p class="text-muted mb-0">Visualize trends and patterns with interactive charts and graphs</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-small" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">Smart Alerts</h5>
                            <p class="text-muted mb-0">Receive notifications about critical changes affecting your supply chain</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Features Quick Overview --}}
<section class="py-5" style="background: white;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold mb-3">Why Choose HandWorld?</h2>
            <p class="lead text-muted">Comprehensive supply chain intelligence in one platform</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="zoom-in">
                <div class="feature-highlight">
                    <i class="bi bi-shield-fill-check"></i>
                    <h4 class="fw-bold">Reliable Data</h4>
                    <p class="text-muted">Sourced from trusted global APIs and verified sources</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="feature-highlight">
                    <i class="bi bi-speedometer2"></i>
                    <h4 class="fw-bold">Fast Performance</h4>
                    <p class="text-muted">Optimized for speed with real-time data processing</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="feature-highlight">
                    <i class="bi bi-puzzle-fill"></i>
                    <h4 class="fw-bold">Easy Integration</h4>
                    <p class="text-muted">RESTful API for seamless system integration</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-5" style="background: linear-gradient(135deg, #0F766E, #065F46);">
    <div class="container text-center text-white" data-aos="zoom-in">
        <h2 class="display-5 fw-bold mb-3">Ready to Transform Your Supply Chain?</h2>
        <p class="lead mb-4 opacity-75">Join thousands of professionals using HandWorld</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5 rounded-pill fw-bold">
                Login
            </a>
            <a href="{{ route('landing.features') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill fw-bold">
                Explore Dashboard
            </a>
        </div>
    </div>
</section>

<style>
.stat-box {
    padding: 32px;
    background: white;
    border-radius: 16px;
    border: 1px solid #E5E7EB;
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(15, 118, 110, 0.12);
    border-color: #0F766E;
}

.stat-number {
    font-size: 48px;
    font-weight: 800;
    color: #0F766E;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    color: #6B7280;
    font-weight: 500;
}

/* Dashboard Preview Card - Modern & Professional */
.dashboard-preview-card {
    position: relative;
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 24px;
    padding: 16px;
    box-shadow: 0 4px 20px rgba(15, 118, 110, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.dashboard-preview-card::before {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 220px;
    height: 220px;
    background: linear-gradient(135deg, rgba(15, 118, 110, 0.08), rgba(6, 95, 70, 0.12));
    border-radius: 50%;
    z-index: 0;
    transition: all 0.4s ease;
}

.dashboard-preview-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(15, 118, 110, 0.15);
    border-color: rgba(15, 118, 110, 0.3);
}

.dashboard-preview-card:hover::before {
    transform: scale(1.3);
    opacity: 0.8;
}

.dashboard-image {
    position: relative;
    width: 100%;
    height: auto;
    border-radius: 20px;
    object-fit: cover;
    object-position: center;
    display: block;
    z-index: 1;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.dashboard-preview-card:hover .dashboard-image {
    transform: scale(1.02);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Responsive Dashboard Image */
@media (max-width: 1200px) {
    .dashboard-preview-card {
        padding: 14px;
        border-radius: 20px;
    }
    
    .dashboard-image {
        border-radius: 16px;
    }
}

@media (max-width: 768px) {
    .dashboard-preview-card {
        padding: 12px;
        border-radius: 18px;
        margin-bottom: 32px;
    }
    
    .dashboard-image {
        border-radius: 14px;
    }
    
    .dashboard-preview-card::before {
        width: 150px;
        height: 150px;
        top: -20px;
        right: -20px;
    }
}

.features-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.feature-item {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.feature-icon-small {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon-small i {
    font-size: 24px;
    color: white;
}

.feature-highlight {
    text-align: center;
    padding: 40px 24px;
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    transition: all 0.3s ease;
}

.feature-highlight:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(15, 118, 110, 0.12);
    border-color: #0F766E;
}

.feature-highlight i {
    font-size: 48px;
    color: #0F766E;
    margin-bottom: 16px;
}

.feature-highlight h4 {
    font-size: 20px;
    margin-bottom: 12px;
}

.feature-highlight p {
    font-size: 14px;
    margin: 0;
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