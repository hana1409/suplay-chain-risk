<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="RiskChain Intelligence — Admin Panel">
    <title>@yield('title', 'Admin Panel') — RiskChain Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    /* ============================================
       ADMIN PANEL — SCOPED STYLES
       Dark modern theme using existing CSS vars
    ============================================ */

    :root {
        --admin-sidebar-w: 260px;
        --admin-topbar-h: 64px;
        --admin-accent: #0F766E;
        --admin-accent2: #065F46;
        --admin-bg: #F5F7F4;
        --admin-surface: #FFFFFF;
        --admin-surface2: #F0FDF4;
        --admin-border: #E5E7EB;
        --admin-text: #1F2937;
        --admin-muted: #6B7280;
        --admin-radius: 12px;
        --admin-shadow: 0 4px 24px rgba(15,118,110,0.10);
        --risk-low: #16A34A;
        --risk-medium: #F59E0B;
        --risk-high: #F97316;
        --risk-critical: #EF4444;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--admin-bg);
        color: var(--admin-text);
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* ---- LAYOUT ---- */
    .admin-layout {
        display: flex;
        min-height: 100vh;
    }

    /* ---- SIDEBAR ---- */
    .admin-sidebar {
        width: var(--admin-sidebar-w);
        min-height: 100vh;
        background: var(--admin-surface);
        border-right: 1px solid var(--admin-border);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0; left: 0;
        z-index: 1000;
        transition: transform 0.3s ease;
    }

    .admin-sidebar-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 20px 16px;
        text-decoration: none;
        border-bottom: 1px solid var(--admin-border);
    }

    .admin-sidebar-logo-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent2));
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(15,118,110,0.35);
    }

    .admin-sidebar-logo h2 {
        font-size: 15px; font-weight: 700;
        color: var(--admin-text);
        line-height: 1.2;
    }

    .admin-sidebar-logo p {
        font-size: 10px;
        color: var(--admin-accent);
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .admin-nav {
        flex: 1;
        overflow-y: auto;
        padding: 12px 0;
    }

    .admin-nav::-webkit-scrollbar { width: 4px; }
    .admin-nav::-webkit-scrollbar-track { background: transparent; }
    .admin-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

    .admin-nav-label {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--admin-muted);
        padding: 12px 20px 4px;
    }

    .admin-nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        color: var(--admin-muted);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
    }

    .admin-nav-item:hover {
        background: #D1FAE5;
        color: var(--admin-text);
        border-left-color: transparent;
    }

    .admin-nav-item.active {
        background: #D1FAE5;
        color: var(--admin-accent);
        border-left-color: var(--admin-accent);
        font-weight: 600;
    }

    .admin-nav-item i { font-size: 16px; width: 20px; text-align: center; }

    .admin-sidebar-footer {
        padding: 16px 20px;
        border-top: 1px solid var(--admin-border);
    }

    .admin-user-card {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .admin-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0F766E, #065F46);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700;
        color: white; flex-shrink: 0;
    }

    .admin-user-name { font-size: 13px; font-weight: 600; color: var(--admin-text); }
    .admin-user-role { font-size: 11px; color: var(--admin-accent); }

    /* ---- CONTENT AREA ---- */
    .admin-content {
        margin-left: var(--admin-sidebar-w);
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* ---- TOPBAR ---- */
    .admin-topbar {
        height: var(--admin-topbar-h);
        background: var(--admin-surface);
        border-bottom: 1px solid var(--admin-border);
        display: flex;
        align-items: center;
        padding: 0 28px;
        gap: 16px;
        position: sticky;
        top: 0;
        z-index: 900;
    }

    .admin-topbar-title { font-size: 16px; font-weight: 700; color: var(--admin-text); }
    .admin-topbar-breadcrumb {
        font-size: 12px; color: var(--admin-muted);
        display: flex; align-items: center; gap: 6px;
    }
    .admin-topbar-breadcrumb a { color: var(--admin-muted); text-decoration: none; }
    .admin-topbar-breadcrumb a:hover { color: var(--admin-accent); }
    .admin-topbar-breadcrumb .current { color: var(--admin-accent); }

    /* ---- MAIN CONTENT ---- */
    .admin-main {
        padding: 28px;
        flex: 1;
    }

    /* ---- PAGE HEADER ---- */
    .admin-page-header {
        margin-bottom: 24px;
    }

    .admin-page-header h1 {
        font-size: 22px;
        font-weight: 800;
        color: var(--admin-text);
        line-height: 1.2;
    }

    .admin-page-header p {
        font-size: 13px;
        color: var(--admin-muted);
        margin-top: 4px;
    }

    /* ---- CARDS ---- */
    .admin-card {
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius);
        padding: 24px;
        transition: border-color 0.2s;
    }

    .admin-card:hover { border-color: rgba(15,118,110,0.3); }

    .admin-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--admin-border);
    }

    .admin-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--admin-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .admin-card-title i { color: var(--admin-accent); }

    /* ---- STAT CARDS ---- */
    .admin-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .admin-stat-card {
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius);
        padding: 20px;
        text-align: center;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .admin-stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #0F766E, #16A34A);
        border-radius: 12px 12px 0 0;
    }

    .admin-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--admin-shadow);
        border-color: rgba(15,118,110,0.3);
    }

    .admin-stat-icon {
        font-size: 28px;
        margin-bottom: 8px;
        display: block;
    }

    .admin-stat-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--admin-text);
        line-height: 1;
    }

    .admin-stat-label {
        font-size: 11px;
        color: var(--admin-muted);
        margin-top: 6px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    /* ---- TABLE ---- */
    .admin-table-wrap {
        overflow-x: auto;
        border-radius: var(--admin-radius);
        border: 1px solid var(--admin-border);
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .admin-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--admin-muted);
        background: #F0FDF4;
        border-bottom: 1px solid var(--admin-border);
        white-space: nowrap;
    }

    .admin-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #E5E7EB;
        color: var(--admin-text);
        vertical-align: middle;
    }

    .admin-table tbody tr:hover {
        background: #F0FDF4;
    }

    .admin-table tbody tr:last-child td { border-bottom: none; }

    /* ---- BADGES ---- */
    .admin-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-admin   { background: #D1FAE5; color: #065F46; }
    .badge-user    { background: rgba(107,114,128,0.12); color: #6B7280; }
    .badge-active  { background: rgba(22,163,74,0.12); color: #16A34A; }
    .badge-inactive{ background: rgba(239,68,68,0.12); color: #DC2626; }
    .badge-draft   { background: rgba(245,158,11,0.12); color: #D97706; }
    .badge-pub     { background: rgba(22,163,74,0.12); color: #16A34A; }
    .badge-pos     { background: rgba(22,163,74,0.12); color: #16A34A; }
    .badge-neg     { background: rgba(239,68,68,0.12); color: #DC2626; }
    .badge-neu     { background: rgba(107,114,128,0.12); color: #6B7280; }
    .badge-low     { background: rgba(22,163,74,0.12);   color: #16A34A; }
    .badge-medium  { background: rgba(245,158,11,0.12);  color: #D97706; }
    .badge-high    { background: rgba(249,115,22,0.12);  color: #EA580C; }
    .badge-critical{ background: rgba(239,68,68,0.12);   color: #DC2626; }
    .badge-ok      { background: rgba(22,163,74,0.12);   color: #16A34A; }
    .badge-fail    { background: rgba(239,68,68,0.12);   color: #DC2626; }

    /* ---- BUTTONS ---- */
    .btn-admin {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-admin-primary {
        background: linear-gradient(135deg, #0F766E, #065F46);
        color: white;
    }
    .btn-admin-primary:hover { opacity: 0.9; transform: translateY(-1px); color: white; }

    .btn-admin-ghost {
        background: #F0FDF4;
        color: var(--admin-text);
        border: 1px solid var(--admin-border);
    }
    .btn-admin-ghost:hover { background: #D1FAE5; color: var(--admin-text); }

    .btn-admin-danger {
        background: rgba(239,68,68,0.15);
        color: #FC8181;
        border: 1px solid rgba(239,68,68,0.2);
    }
    .btn-admin-danger:hover { background: rgba(239,68,68,0.25); }

    .btn-admin-success {
        background: rgba(16,185,129,0.15);
        color: #34D399;
        border: 1px solid rgba(16,185,129,0.2);
    }
    .btn-admin-success:hover { background: rgba(16,185,129,0.25); }

    .btn-admin-sm { padding: 5px 10px; font-size: 11px; }
    .btn-admin-icon { width: 32px; height: 32px; padding: 0; justify-content: center; }

    /* ---- FORMS ---- */
    .admin-form-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--admin-muted);
        margin-bottom: 6px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .admin-input {
        width: 100%;
        background: var(--admin-surface2);
        border: 1px solid var(--admin-border);
        border-radius: 8px;
        color: var(--admin-text);
        padding: 10px 14px;
        font-size: 13px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: border-color 0.2s;
    }

    .admin-input:focus {
        outline: none;
        border-color: var(--admin-accent);
        box-shadow: 0 0 0 3px rgba(15,118,110,0.15);
    }

    .admin-input::placeholder { color: var(--admin-muted); }

    select.admin-input option { background: var(--admin-surface); }

    textarea.admin-input { resize: vertical; min-height: 120px; }

    /* ---- MODALS ---- */
    .admin-modal .modal-content {
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: 16px;
        color: var(--admin-text);
    }

    .admin-modal .modal-header {
        border-bottom: 1px solid var(--admin-border);
        padding: 20px 24px;
    }

    .admin-modal .modal-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--admin-text);
    }

    .admin-modal .modal-body { padding: 24px; }
    .admin-modal .modal-footer {
        border-top: 1px solid var(--admin-border);
        padding: 16px 24px;
        gap: 10px;
    }

    .admin-modal .btn-close {
        filter: invert(1);
        opacity: 0.5;
    }

    /* ---- ALERTS ---- */
    .admin-alert {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .admin-alert-success {
        background: rgba(16,185,129,0.1);
        border: 1px solid rgba(16,185,129,0.3);
        color: #34D399;
    }

    .admin-alert-danger {
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.3);
        color: #FC8181;
    }

    /* ---- SEARCH ---- */
    .admin-search-wrap {
        position: relative;
    }

    .admin-search-wrap .admin-input {
        padding-left: 36px;
    }

    .admin-search-icon {
        position: absolute;
        left: 12px; top: 50%;
        transform: translateY(-50%);
        color: var(--admin-muted);
        font-size: 14px;
        pointer-events: none;
    }

    /* ---- CHART CONTAINER ---- */
    .admin-chart-container {
        position: relative;
        height: 220px;
    }

    /* ---- PAGINATION ---- */
    .pagination .page-link {
        background: var(--admin-surface2);
        border-color: var(--admin-border);
        color: var(--admin-muted);
        font-size: 13px;
    }
    .pagination .page-link:hover { background: #D1FAE5; color: var(--admin-accent); }
    .pagination .page-item.active .page-link {
        background: var(--admin-accent);
        border-color: var(--admin-accent);
        color: white;
    }
    .pagination .page-item.disabled .page-link { opacity: 0.4; }

    /* ---- TOAST ---- */
    .admin-toast-wrap {
        position: fixed;
        bottom: 24px; right: 24px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .admin-toast {
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13px;
        color: var(--admin-text);
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: var(--admin-shadow);
        min-width: 280px;
        animation: toastIn 0.3s ease;
    }

    .admin-toast.success { border-left: 3px solid #16A34A; }
    .admin-toast.error   { border-left: 3px solid var(--risk-critical); }
    .admin-toast.info    { border-left: 3px solid var(--admin-accent); }
    .admin-toast.warning { border-left: 3px solid var(--risk-medium); }

    @keyframes toastIn {
        from { opacity: 0; transform: translateX(20px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* ---- RESPONSIVE ---- */
    @media (max-width: 992px) {
        .admin-sidebar {
            transform: translateX(-100%);
        }
        .admin-sidebar.open {
            transform: translateX(0);
        }
        .admin-content {
            margin-left: 0;
        }
    }
    </style>

    @stack('styles')
</head>
<body>

<div class="admin-layout">

    {{-- ===== SIDEBAR ===== --}}
    @include('partials.admin-sidebar')

    {{-- ===== CONTENT ===== --}}
    <div class="admin-content">

        {{-- TOPBAR --}}
        <div class="admin-topbar">
            <button class="btn-admin btn-admin-ghost btn-admin-icon d-lg-none" id="adminSidebarToggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="flex-grow-1">
                <div class="admin-topbar-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                    <i class="bi bi-chevron-right" style="font-size:10px;"></i>
                    <span class="current">@yield('breadcrumb', 'Dashboard')</span>
                </div>
                <div class="admin-topbar-title">@yield('title', 'Dashboard')</div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn-admin btn-admin-ghost btn-admin-sm" title="Go to User Dashboard">
                    <i class="bi bi-grid-1x2"></i>
                    <span class="d-none d-md-inline">User View</span>
                </a>
                <div style="width:1px;height:24px;background:var(--admin-border);"></div>
                <div style="font-size:13px;color:var(--admin-muted);">
                    <i class="bi bi-shield-check" style="color:var(--admin-accent);margin-right:4px;"></i>
                    {{ auth()->user()->name }}
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <main class="admin-main">
            @if(session('success'))
            <div class="admin-alert admin-alert-success">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="admin-alert admin-alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </main>

    </div>

</div>

{{-- Toast Container --}}
<div class="admin-toast-wrap" id="adminToastContainer"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

<script>
// =====================================
// ADMIN GLOBAL UTILITIES
// =====================================

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// Active nav item
(function() {
    const path = window.location.pathname;
    document.querySelectorAll('.admin-nav-item').forEach(item => {
        const href = item.getAttribute('href');
        if (href && path.startsWith(href) && href !== '/admin') {
            item.classList.add('active');
        } else if (href === '/admin' && path === '/admin') {
            item.classList.add('active');
        }
    });
})();

// Sidebar toggle (mobile)
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('adminSidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    if (btn && sidebar) {
        btn.addEventListener('click', () => sidebar.classList.toggle('open'));
    }
});

// Toast utility
function adminToast(message, type = 'info') {
    const container = document.getElementById('adminToastContainer');
    const toast = document.createElement('div');
    toast.className = `admin-toast ${type}`;
    const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', info: 'bi-info-circle-fill', warning: 'bi-exclamation-triangle-fill' };
    toast.innerHTML = `<i class="bi ${icons[type] || icons.info}" style="color:${type==='success'?'#34D399':type==='error'?'#FC8181':type==='warning'?'#FCD34D':'var(--admin-accent)'}"></i> ${message}`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        toast.style.transition = 'opacity 0.3s, transform 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// Generic AJAX delete confirm
function adminDelete(url, confirmMsg, onSuccess) {
    if (!confirm(confirmMsg || 'Are you sure?')) return;
    fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            adminToast(data.message || 'Deleted successfully.', 'success');
            if (onSuccess) onSuccess(data);
        } else {
            adminToast(data.message || 'Delete failed.', 'error');
        }
    })
    .catch(() => adminToast('Request failed.', 'error'));
}
</script>

@stack('scripts')

</body>
</html>
