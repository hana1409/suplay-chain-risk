<nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">

    <div class="container">

        <a class="navbar-brand fw-bold"
            href="{{ route('landing') }}">

            <span class="logo-white">Hand</span><span class="logo-purple">World</span>

        </a>

        <button class="navbar-toggler"
            data-bs-toggle="collapse"
            data-bs-target="#menu">

            <i class="bi bi-list text-white"></i>

        </button>

        <div class="collapse navbar-collapse"
            id="menu">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">

                    <a href="{{ route('landing') }}"
                        class="nav-link {{ request()->routeIs('landing') ? 'active' : '' }}">

                        Home

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ route('landing.features') }}"
                        class="nav-link {{ request()->routeIs('landing.features') ? 'active' : '' }}">

                        Features

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ route('landing.countries') }}"
                        class="nav-link {{ request()->routeIs('landing.countries') ? 'active' : '' }}">

                        Countries

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ route('landing.about') }}"
                        class="nav-link {{ request()->routeIs('landing.about') ? 'active' : '' }}">

                        About

                    </a>

                </li>

                <li class="nav-item">

                    <a href="{{ route('landing.contact') }}"
                        class="nav-link {{ request()->routeIs('landing.contact') ? 'active' : '' }}">

                        Contact

                    </a>

                </li>

            </ul>

            <a href="/login" class="btn-login-navbar">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </a>

        </div>

    </div>

</nav>

<style>
/* Navbar scroll effect */
.navbar {
    transition: all 0.3s ease;
    padding: 16px 0;
}

.navbar.scrolled {
    background: rgba(15, 118, 110, 0.98) !important;
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    padding: 12px 0;
}

.nav-link.active {
    color: #D1FAE5 !important;
    font-weight: 600;
}

/* Modern Login Button */
.btn-login-navbar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 28px;
    background: linear-gradient(135deg, #0F766E, #065F46);
    color: white;
    border-radius: 11px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(15, 118, 110, 0.2);
    border: none;
    white-space: nowrap;
    margin-left: 20px;
}

.btn-login-navbar i {
    font-size: 16px;
    transition: transform 0.3s ease;
}

.btn-login-navbar:hover {
    background: linear-gradient(135deg, #0D9488, #047857);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(15, 118, 110, 0.35);
}

.btn-login-navbar:hover i {
    transform: translateX(2px);
}

.btn-login-navbar:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(15, 118, 110, 0.25);
}

/* Navbar spacing adjustment */
.navbar-nav {
    gap: 8px;
}

.navbar-collapse {
    align-items: center;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .btn-login-navbar {
        margin-left: 0;
        margin-top: 16px;
        width: 100%;
        justify-content: center;
    }
}

@media (min-width: 992px) {
    .navbar-nav {
        margin-right: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('mainNavbar');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});
</script>