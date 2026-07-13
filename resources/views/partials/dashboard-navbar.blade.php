<nav class="dashboard-navbar">

    <div class="nav-logo">

        <img src="{{ asset('images/avatar.png') }}" class="logo-img" alt="Avatar">

        <div class="logo-text">
            <h2>Hand<span>World</span></h2>
            <p>Global Supply Chain Risk</p>
        </div>

    </div>

    <ul class="nav-menu">

        <li>
            <a class="active" href="{{ route('dashboard') }}">
                Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('countries') }}">
                Countries
            </a>
        </li>

        <li>
            <a href="#">
                Comparison
            </a>
        </li>

        <li>
            <a href="#">
                Articles
            </a>
        </li>

    </ul>

    <div class="nav-search">

        <i class="bi bi-search"></i>

        <input type="text" placeholder="Search country...">

    </div>

    <div class="nav-right">

        <a href="#" class="favorite">

            <i class="bi bi-star"></i>

            Favorites

        </a>

        <div class="notification">

            <i class="bi bi-bell"></i>

            <span>3</span>

        </div>

        <div class="profile">

            <img src="{{ asset('images/avatar.png') }}">

            <span>Hana</span>

            <i class="bi bi-chevron-down"></i>

        </div>

    </div>

</nav>