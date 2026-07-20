<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Global Supply Chain Risk Intelligence Platform — Monitor worldwide trade risks in real-time.">
    <title>@yield('title', 'Dashboard') — RiskChain Intelligence</title>

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Pagination Icon Fix -->
    <style>
        /* Prevent large arrow icons in pagination */
        .pagination .page-link::before,
        .pagination .page-link::after {
            content: none !important;
            display: none !important;
        }
        .pagination .page-link {
            background-image: none !important;
            max-width: 60px !important;
            max-height: 40px !important;
            overflow: hidden !important;
        }
        .pagination .page-link svg,
        .pagination .page-link i {
            max-width: 16px !important;
            max-height: 16px !important;
            font-size: 16px !important;
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="app-layout">

    {{-- ========================= --}}
    {{-- SIDEBAR --}}
    {{-- ========================= --}}
    @include('partials.sidebar')

    {{-- ========================= --}}
    {{-- MAIN AREA --}}
    {{-- ========================= --}}
    <div class="content-area">

        {{-- TOPBAR --}}
        @include('partials.topbar')

        {{-- PAGE CONTENT --}}
        <main class="content-inner">
            @yield('content')
        </main>

    </div>

</div>

{{-- Toast Container --}}
<div class="intel-toast-wrap" id="toastContainer"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>

<!-- Global JS Utilities -->
<script>
// ======================================
// GLOBAL UTILITIES
// ======================================

// CSRF token for AJAX
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// Set active sidebar item
(function() {
    const path = window.location.pathname;
    document.querySelectorAll('.sidebar-item').forEach(item => {
        const href = item.getAttribute('href');
        if (href && path.startsWith(href) && href !== '/') {
            item.classList.add('active');
        } else if (href === '/dashboard' && path === '/dashboard') {
            item.classList.add('active');
        }
    });
})();

// Clock update — local + UTC
function updateClock() {
    const now = new Date();
    const local = document.getElementById('topbar-clock');
    const utc   = document.getElementById('topbar-utc');
    if (local) local.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    if (utc)   utc.textContent   = now.toUTCString().split(' ')[4] + ' UTC';
}
updateClock();
setInterval(updateClock, 1000);

// Toast notification helper
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `intel-toast ${type}`;
    const icons = { success: '✓', error: '✕', info: 'ℹ', warning: '⚠' };
    toast.innerHTML = `<span style="font-size:16px">${icons[type] || 'ℹ'}</span> ${message}`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar   = document.querySelector('.sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
    }
});

// ============================================================
//  PROFESSIONAL SIDEBAR COLLAPSE/EXPAND
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const STORAGE_KEY = 'sidebar_collapsed';
    
    // Restore sidebar state from localStorage
    const isCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }
    
    // Initialize Bootstrap tooltips (only for collapsed state)
    let tooltipList = [];
    
    function initTooltips() {
        // Destroy existing tooltips
        tooltipList.forEach(tooltip => tooltip.dispose());
        tooltipList = [];
        
        // Only init tooltips if sidebar is collapsed
        if (sidebar.classList.contains('collapsed')) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipList = tooltipTriggerList.map(tooltipTriggerEl => {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover',
                    container: 'body',
                    boundary: 'window'
                });
            });
        }
    }
    
    // Initialize tooltips based on initial state
    initTooltips();
    
    // Toggle sidebar collapse
    if (collapseBtn) {
        collapseBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            
            // Save state to localStorage
            const collapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem(STORAGE_KEY, collapsed);
            
            // Reinitialize tooltips
            setTimeout(() => {
                initTooltips();
            }, 300); // Wait for transition to complete
        });
    }
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            // Dispose tooltips on small screens
            if (window.innerWidth <= 992) {
                tooltipList.forEach(tooltip => tooltip.dispose());
                tooltipList = [];
            } else {
                initTooltips();
            }
        }, 250);
    });
});

</script>

@stack('scripts')

</body>
</html>