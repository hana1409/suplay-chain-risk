@extends('layouts.app')

@section('title', 'About Us - HandWorld')

@section('content')

@include('partials.navbar')

<div style="padding-top: 90px; min-height: 100vh; background: white;">

    {{-- Hero Section --}}
    <section class="py-5" style="background: linear-gradient(180deg, #F0FDF4 0%, #FFFFFF 100%);">
        <div class="container text-center" data-aos="fade-up">
            <span class="badge bg-light text-success px-4 py-2 rounded-pill mb-3">
                <i class="bi bi-info-circle"></i> About Platform
            </span>
            <h1 class="display-4 fw-bold mb-3" style="color: #1F2937;">
                About HandWorld
            </h1>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                Empowering global supply chain professionals with real-time intelligence and risk analytics
            </p>
        </div>
    </section>

    {{-- About Platform --}}
    <section class="container py-5">
        <div class="row align-items-center g-5">

            {{-- Dashboard Preview --}}
            <div class="col-lg-6" data-aos="fade-right">
                <div class="dashboard-preview-card">
                    <img src="{{ asset('images/map.png') }}"
                         alt="HandWorld Dashboard"
                         class="dashboard-preview-img">
                </div>
            </div>

            {{-- Description --}}
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="fw-bold mb-4" style="color: #1F2937;">What is HandWorld?</h2>

                <p class="text-muted mb-3" style="line-height:1.8;">
                    HandWorld is a comprehensive Supply Chain Risk Intelligence Platform that provides real-time monitoring and analysis of global factors affecting supply chain operations.
                </p>

                <p class="text-muted mb-4" style="line-height:1.8;">
                    We aggregate data from multiple sources including weather APIs, economic indicators, currency exchanges, and news feeds to provide a unified view of global supply chain risks.
                </p>

                <div class="d-flex gap-4 flex-wrap">

                    <div>
                        <h3 class="fw-bold text-success mb-0">250+</h3>
                        <small class="text-muted">Countries Covered</small>
                    </div>

                    <div>
                        <h3 class="fw-bold text-success mb-0">150+</h3>
                        <small class="text-muted">Major Ports</small>
                    </div>

                    <div>
                        <h3 class="fw-bold text-success mb-0">24/7</h3>
                        <small class="text-muted">Real-time Updates</small>
                    </div>

                </div>
            </div>

        </div>
    </section>

    {{-- Vision & Mission --}}
    <section class="py-5" style="background:#F5F7F4;">
        <div class="container">

            <div class="row g-4">

                <div class="col-lg-6" data-aos="fade-up">
                    <div class="p-5 rounded-4 bg-white h-100 border">

                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                 style="width:60px;height:60px;background:linear-gradient(135deg,#0F766E,#065F46);">
                                <i class="bi bi-eye-fill text-white" style="font-size:28px;"></i>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3">Our Vision</h3>

                        <p class="text-muted" style="line-height:1.8;">
                            To be the world's leading platform for supply chain risk intelligence,
                            enabling businesses to make informed decisions and build resilient global operations.
                        </p>

                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-5 rounded-4 bg-white h-100 border">

                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                 style="width:60px;height:60px;background:linear-gradient(135deg,#3B82F6,#1E40AF);">
                                <i class="bi bi-bullseye text-white" style="font-size:28px;"></i>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-3">Our Mission</h3>

                        <p class="text-muted" style="line-height:1.8;">
                            To provide comprehensive, real-time insights into global supply chain risks through advanced
                            data aggregation, analysis, and visualization technologies.
                        </p>

                    </div>
                </div>

            </div>

        </div>
    </section>

    {{-- Technology Stack --}}
    <section class="container py-5">

        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold mb-3">Technology Stack</h2>
            <p class="text-muted">Built with modern, reliable technologies</p>
        </div>

        <div class="row g-4">

            <div class="col-lg-3 col-md-4 col-6" data-aos="zoom-in">
                <div class="tech-card">
                    <i class="bi bi-code-slash"></i>
                    <h6>Laravel 11</h6>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-6" data-aos="zoom-in" data-aos-delay="50">
                <div class="tech-card">
                    <i class="bi bi-bootstrap-fill"></i>
                    <h6>Bootstrap 5</h6>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="tech-card">
                    <i class="bi bi-database-fill"></i>
                    <h6>MySQL / SQLite</h6>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-6" data-aos="zoom-in" data-aos-delay="150">
                <div class="tech-card">
                    <i class="bi bi-map-fill"></i>
                    <h6>Leaflet.js</h6>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="tech-card">
                    <i class="bi bi-graph-up"></i>
                    <h6>Chart.js</h6>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-6" data-aos="zoom-in" data-aos-delay="250">
                <div class="tech-card">
                    <i class="bi bi-cloud-fill"></i>
                    <h6>REST APIs</h6>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-6" data-aos="zoom-in" data-aos-delay="300">
                <div class="tech-card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h6>Secure Auth</h6>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-6" data-aos="zoom-in" data-aos-delay="350">
                <div class="tech-card">
                    <i class="bi bi-lightning-charge-fill"></i>
                    <h6>Real-time Data</h6>
                </div>
            </div>

        </div>

    </section>

</div>

<footer style="background:white;padding:24px 0;text-align:center;border-top:1px solid #E5E7EB;">
    <div class="container">
        <p style="margin:0;font-size:15px;color:#6B7280;">© 2026 Hana Marmella</p>
    </div>
</footer>

<style>

.dashboard-preview-card{
    background:#fff;
    border-radius:24px;
    border:1px solid #E5E7EB;
    padding:18px;
    box-shadow:0 18px 40px rgba(15,118,110,.12);
    transition:.35s ease;
    overflow:hidden;
}

.dashboard-preview-card:hover{
    transform:translateY(-6px);
    box-shadow:0 28px 50px rgba(15,118,110,.18);
}

.dashboard-preview-img{
    width:100%;
    height:auto;
    display:block;
    border-radius:16px;
}

@media(min-width:992px){

    .dashboard-preview-card{
        min-height:430px;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .dashboard-preview-img{
        max-height:390px;
        object-fit:contain;
    }

}

.tech-card{
    background:white;
    border:1px solid #E5E7EB;
    border-radius:12px;
    padding:24px;
    text-align:center;
    transition:.3s ease;
}

.tech-card:hover{
    transform:translateY(-4px);
    box-shadow:0 8px 20px rgba(15,118,110,.12);
    border-color:#0F766E;
}

.tech-card i{
    font-size:36px;
    color:#0F766E;
    margin-bottom:12px;
}

.tech-card h6{
    margin:0;
    font-size:14px;
    color:#374151;
}

</style>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
AOS.init({
    duration:800,
    once:true
});
</script>

@endsection