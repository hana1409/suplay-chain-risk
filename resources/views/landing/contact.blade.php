@extends('layouts.app')

@section('title', 'Contact Us - HandWorld')

@section('content')

@include('partials.navbar')

<div style="padding-top: 90px; min-height: 100vh; background: white;">
    
    {{-- Hero Section --}}
    <section class="py-5" style="background: linear-gradient(180deg, #F0FDF4 0%, #FFFFFF 100%);">
        <div class="container text-center" data-aos="fade-up">
            <span class="badge bg-light text-success px-4 py-2 rounded-pill mb-3">
                <i class="bi bi-envelope-fill"></i> Get In Touch
            </span>
            <h1 class="display-4 fw-bold mb-3" style="color: #1F2937;">
                Contact Us
            </h1>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
            </p>
        </div>
    </section>

    {{-- Contact Info & Form --}}
    <section class="container py-5">
        <div class="row g-5">
            
            {{-- Contact Information --}}
            <div class="col-lg-5" data-aos="fade-right">
                <h2 class="fw-bold mb-4">Contact Information</h2>
                <p class="text-muted mb-4">Fill out the form and our team will get back to you within 24 hours.</p>
                
                <div class="contact-info-list">
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Email</h6>
                            <a href="mailto:hanamarmella1324@gmail.com" class="text-muted">hanamarmella1324@gmail.com</a>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Phone</h6>
                            <a href="tel:+6281910439756" class="text-muted">+62 819-1043-9756</a>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Address</h6>
                            <p class="text-muted mb-0">
                                Universitas Malikussaleh<br>
                                Kampus Utama Cot Tengku Nie, Reuleut<br>
                                Aceh Utara, Aceh<br>
                                Indonesia
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Social Media --}}
                <div class="mt-5">
                    <h6 class="fw-bold mb-3">Follow Us</h6>
                    <div class="d-flex gap-3">
                        <a href="https://www.instagram.com/hanamarmela?igsh=MWxobDVmNXoydjA4ag==" class="social-link" target="_blank" rel="noopener noreferrer" title="Follow us on Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://www.facebook.com/share/1Jz8nFSw7N/" class="social-link" target="_blank" rel="noopener noreferrer" title="Follow us on Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://www.linkedin.com/in/hanamarmella-34a49a29a?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=member_desktop" class="social-link" target="_blank" rel="noopener noreferrer" title="Follow us on LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="col-lg-7" data-aos="fade-left">
                <div class="contact-form-card">
                    <h3 class="fw-bold mb-4">Send us a Message</h3>
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">First Name *</label>
                                <input type="text" name="first_name" class="form-control form-control-lg @error('first_name') is-invalid @enderror" placeholder="John" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Last Name *</label>
                                <input type="text" name="last_name" class="form-control form-control-lg @error('last_name') is-invalid @enderror" placeholder="Doe" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email *</label>
                                <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="john@example.com" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="tel" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" placeholder="+1 (234) 567-890" value="{{ old('phone') }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Subject *</label>
                                <input type="text" name="subject" class="form-control form-control-lg @error('subject') is-invalid @enderror" placeholder="How can we help you?" value="{{ old('subject') }}" required>
                                @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Message *</label>
                                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5" placeholder="Tell us more about your inquiry..." required>{{ old('message') }}</textarea>
                                @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-submit">
                                    <i class="bi bi-send-fill"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>

    {{-- Map Section --}}
    <section class="py-5" style="background: #F5F7F4;">
        <div class="container">
            <div class="text-center mb-4" data-aos="fade-up">
                <h2 class="fw-bold mb-3">Our Location</h2>
                <p class="text-muted">Visit us at our office</p>
            </div>
            <div class="map-container" data-aos="zoom-in">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d494.2586755290891!2d96.98801767434692!3d5.2341207434157895!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sid!4v1710000000000!5m2!1sen!2sid" 
                    width="100%" 
                    height="450" 
                    style="border:0; border-radius: 16px;" 
                    allowfullscreen="" 
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

</div>

<style>
.contact-info-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.contact-info-item {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.contact-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0F766E, #065F46);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.contact-icon i {
    font-size: 20px;
    color: white;
}

.contact-info-item a {
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-info-item a:hover {
    color: #0F766E !important;
}

.social-link {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: white;
    border: 2px solid #E5E7EB;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6B7280;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: #0F766E;
    border-color: #0F766E;
    color: white;
    transform: translateY(-4px);
}

.social-link i {
    font-size: 18px;
}

.contact-form-card {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.form-control {
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    padding: 12px 16px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #0F766E;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
}

.btn-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #0F766E, #065F46);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 118, 110, 0.3);
}

.map-container {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}
</style>

{{-- AOS Animation --}}
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    once: true
  });
</script>

{{-- Simple Footer --}}
<footer style="background: white; padding: 24px 0; text-align: center; border-top: 1px solid #E5E7EB;">
    <div class="container">
        <p style="margin: 0; font-size: 15px; color: #6B7280;">© 2026 Hana Marmella</p>
    </div>
</footer>

@endsection
