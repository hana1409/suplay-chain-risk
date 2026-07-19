@extends('layouts.dashboard')
@section('title', 'Port Dashboard — Global Ports')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<style>
/* Custom MarkerCluster Styles to match the green theme */
.marker-cluster-small,
.marker-cluster-medium,
.marker-cluster-large {
    background-color: rgba(22, 163, 74, 0.4);
}
.marker-cluster-small div,
.marker-cluster-medium div,
.marker-cluster-large div {
    background-color: rgba(22, 163, 74, 0.9);
    color: white;
    font-weight: bold;
    font-family: 'Inter', sans-serif;
}
.port-circle-marker {
    outline: none;
    cursor: pointer;
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--accent)">Port Dashboard</span>
        </div>
        <h1>Global Port Dashboard</h1>
        <p>{{ $totalPorts }} ports tracked across all regions</p>
    </div>
</div>

<div class="main-sidebar-grid">

    {{-- MAP --}}
    <div>
        <div class="section-header">
            <div class="section-title"><span></span> Port Map</div>
            <div style="display:flex;gap:8px;">
                <select id="portTypeFilter" class="intel-input" style="width:auto;padding:6px 12px;font-size:12px;" onchange="applyFilter()">
                    <option value="">All Types</option>
                    <option value="Sea Port">Sea Port</option>
                    <option value="River Port">River Port</option>
                    <option value="Dry Port">Dry Port</option>
                    <option value="Airport">Airport</option>
                </select>
            </div>
        </div>

        <div class="map-container" style="height:580px;">
            <div id="port-map" style="height:100%;width:100%;"></div>
        </div>
    </div>

    {{-- SIDEBAR: Search + List --}}
    <div>
        <div class="section-header">
            <div class="section-title"><span></span> Search Ports</div>
        </div>

        <div class="glass-card" style="padding:16px;margin-bottom:16px;">
            <div class="search-input-wrap" style="margin-bottom:12px;">
                <i class="bi bi-search"></i>
                <input type="text" id="portSearch" class="intel-input" placeholder="Search port name...">
            </div>
            <select id="countryFilter" class="intel-input" style="margin-bottom:8px;" onchange="applyFilter()">
                <option value="">All Countries</option>
                @foreach($countries as $c)
                <option value="{{ $c->country_code }}">{{ $c->country_name }}</option>
                @endforeach
            </select>
            <button onclick="applyFilter()" class="btn-primary-custom" style="width:100%;justify-content:center;">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>

        <div class="glass-card" style="padding:8px;max-height:420px;overflow-y:auto;" id="portList">
            <div style="text-align:center;padding:24px;color:var(--text-muted);">
                <div class="intel-spinner" style="margin:0 auto 8px;"></div>
                Loading ports...
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script>
// Port Map
const map = L.map('port-map', { center: [20, 0], zoom: 2, zoomControl: true });

L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '© OpenStreetMap © CARTO',
    subdomains: 'abcd',
}).addTo(map);

let markerGroup = L.markerClusterGroup({
    chunkedLoading: true,
    maxClusterRadius: 40,
}).addTo(map);
let allPorts    = [];

function loadPorts(params = {}) {
    const url = '/api/ports?' + new URLSearchParams(params).toString();
    fetch(url)
        .then(r => r.json())
        .then(ports => {
            allPorts = ports;
            renderMarkers(ports);
            renderList(ports);
        })
        .catch(() => {});
}

function renderMarkers(ports) {
    markerGroup.clearLayers();
    const markers = [];
    ports.forEach(p => {
        const m = L.circleMarker([p.lat, p.lng], {
            radius: 5,
            fillColor: '#16A34A',
            color: '#FFFFFF',
            weight: 2,
            fillOpacity: 0.9,
            className: 'port-circle-marker'
        });

        m.on('mouseover', function () { this.setRadius(6.5); });
        m.on('mouseout', function () { this.setRadius(5); });

        m.bindPopup(`
            <div style="font-family:Inter,sans-serif;min-width:180px;">
                <b style="font-size:13px;color:#F1F5F9;">${p.name}</b><br>
                <span style="font-size:11px;color:#94A3B8;">${p.country_name}</span><br>
                <hr style="border-color:rgba(148,163,184,0.2);margin:6px 0;">
                <span style="font-size:11px;color:#64748B;">Type: ${p.type}</span><br>
                <span style="font-size:11px;color:#64748B;">Status: <span style="color:${p.status==='Active'?'#10B981':'#64748B'}">${p.status}</span></span><br>
                <a href="${p.country_url}" style="font-size:11px;color:#8B5CF6;text-decoration:none;display:block;margin-top:6px;">View Country →</a>
            </div>
        `);
        markers.push(m);
    });
    markerGroup.addLayers(markers);
}

function renderList(ports) {
    const list = document.getElementById('portList');
    if (!ports.length) {
        list.innerHTML = '<div style="padding:20px;text-align:center;color:var(--text-muted);">No ports found.</div>';
        return;
    }
    list.innerHTML = ports.slice(0, 50).map(p => `
        <div onclick="flyTo(${p.lat},${p.lng})"
             style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;cursor:pointer;transition:background 0.2s;border-bottom:1px solid rgba(148,163,184,0.08);"
             onmouseover="this.style.background='var(--accent-subtle)'"
             onmouseout="this.style.background='transparent'">
            <i class="bi bi-anchor" style="color:var(--accent);font-size:14px;flex-shrink:0;"></i>
            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${p.name}</div>
                <div style="font-size:10px;color:var(--text-muted);">${p.country_name} · ${p.type}</div>
            </div>
            <span style="font-size:10px;color:${p.status==='Active'?'var(--risk-low)':'var(--text-muted)'};flex-shrink:0;">●</span>
        </div>
    `).join('');
}

function flyTo(lat, lng) {
    map.flyTo([lat, lng], 8, { duration: 1 });
}

function applyFilter() {
    const country = document.getElementById('countryFilter').value;
    const type    = document.getElementById('portTypeFilter').value;
    const search  = document.getElementById('portSearch').value;
    loadPorts({ country, type, search });
}

// Search debounce
let searchTimeout;
document.getElementById('portSearch').addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilter, 400);
});

// Initial load
loadPorts();
</script>
@endpush
