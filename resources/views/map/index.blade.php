@extends('layouts.dashboard')

@section('title', 'Global Interactive Map')

@push('styles')
<style>
/* ===================================================
   GLOBAL INTERACTIVE MAP — Page Styles (Green Theme)
   =================================================== */

/* Full-height map layout */
.map-page-wrap {
    display: flex;
    flex-direction: column;
    gap: 0;
    height: calc(100vh - var(--navbar-height) - 48px);
    min-height: 600px;
}

/* Page header */
.map-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 0 16px 0;
    flex-shrink: 0;
}

.map-header-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.map-header-title h1 {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.map-header-title .map-icon-wrap {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #0F766E, #065F46);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 15px rgba(15,118,110,0.30);
}

/* Filter bar */
.map-filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,0.95);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 8px 16px;
    backdrop-filter: blur(12px);
    box-shadow: 0 2px 8px rgba(15,118,110,0.06);
}

.map-filter-bar .filter-label {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-right: 4px;
    white-space: nowrap;
}

.map-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 6px 14px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: #F5F7F4;
    transition: all 0.2s;
    user-select: none;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary);
    white-space: nowrap;
}

.map-toggle input[type="checkbox"] {
    width: 15px;
    height: 15px;
    accent-color: var(--accent);
    cursor: pointer;
}

.map-toggle:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-subtle);
}

.map-toggle.active {
    border-color: rgba(15,118,110,0.4);
    background: #D1FAE5;
    color: #0F766E;
}

.map-toggle .toggle-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* Stats pill */
.map-stat-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
}

.map-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #FFFFFF;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    white-space: nowrap;
    box-shadow: 0 1px 4px rgba(15,118,110,0.06);
}

.map-pill .pill-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
}

/* Map container */
.map-canvas-wrap {
    flex: 1;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: 0 8px 40px rgba(15,118,110,0.12);
    position: relative;
    background: #FFFFFF;
}

#globalMap {
    width: 100%;
    height: 100%;
    background: #F9FAFB !important;
}

.legend-toggle-btn {
    position: absolute;
    bottom: 24px;
    right: 12px;
    z-index: 900;
    background: #FFFFFF;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    box-shadow: 0 4px 15px rgba(15,118,110,0.1);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.legend-toggle-btn:hover {
    color: var(--text-primary);
    border-color: var(--accent);
}

/* Loading overlay */
.map-loading {
    position: absolute;
    inset: 0;
    background: rgba(245,247,244,0.88);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: 20px;
    gap: 16px;
    transition: opacity 0.4s;
}

.map-loading.hidden {
    opacity: 0;
    pointer-events: none;
}

.map-loading-text {
    font-size: 14px;
    color: var(--text-secondary);
    font-weight: 500;
}

.map-loading-bar {
    width: 220px;
    height: 3px;
    background: rgba(15,118,110,0.15);
    border-radius: 99px;
    overflow: hidden;
}

.map-loading-bar-inner {
    height: 100%;
    background: linear-gradient(90deg, #0F766E, #16A34A, #0F766E);
    background-size: 200%;
    animation: shimmer 1.5s infinite;
    border-radius: 99px;
}

@keyframes shimmer {
    0%   { background-position: 200% center; }
    100% { background-position: -200% center; }
}

/* ─── Leaflet popup override ─── */
.leaflet-popup-content-wrapper {
    background: #FFFFFF !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 14px !important;
    box-shadow: 0 10px 40px rgba(15,118,110,0.12) !important;
    padding: 0 !important;
    overflow: hidden;
    color: #1F2937;
}

.leaflet-popup-tip {
    background: #FFFFFF !important;
}

.leaflet-popup-content {
    margin: 0 !important;
    width: auto !important;
    min-width: 280px;
}

/* Country popup */
.cpop {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    min-width: 280px;
    max-width: 320px;
}

.cpop-header {
    padding: 14px 16px 12px;
    background: linear-gradient(135deg, #F0FDF4, #DCFCE7);
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cpop-flag {
    width: 36px;
    height: 26px;
    border-radius: 4px;
    object-fit: cover;
    border: 1px solid rgba(0,0,0,0.08);
}

.cpop-name {
    font-size: 15px;
    font-weight: 700;
    color: #1F2937;
    flex: 1;
}

.cpop-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 99px;
    letter-spacing: 0.3px;
}

.cpop-body {
    padding: 12px 16px;
    background: #FFFFFF;
}

.cpop-weather-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #F3F4F6;
    margin-bottom: 10px;
}

.cpop-weather-icon {
    width: 42px;
    height: 42px;
    object-fit: contain;
    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.15));
}

.cpop-weather-main {
    flex: 1;
}

.cpop-weather-cond {
    font-size: 14px;
    font-weight: 600;
    color: #1F2937;
}

.cpop-weather-temp {
    font-size: 20px;
    font-weight: 700;
    color: #D97706;
    line-height: 1;
    margin-top: 2px;
}

.cpop-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 10px;
}

.cpop-item {
    background: #F5F7F4;
    border-radius: 8px;
    padding: 8px 10px;
    border: 1px solid #E5E7EB;
}

.cpop-item-label {
    font-size: 10px;
    color: #9CA3AF;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 2px;
}

.cpop-item-value {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
}

.cpop-score-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #F0FDF4;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 10px;
    border: 1px solid #D1FAE5;
}

.cpop-score-label {
    font-size: 11px;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.cpop-score-val {
    font-size: 22px;
    font-weight: 800;
    color: #1F2937;
    line-height: 1;
}

.cpop-score-bar-wrap {
    height: 4px;
    background: rgba(15,118,110,0.12);
    border-radius: 99px;
    margin-top: 6px;
    overflow: hidden;
    width: 80px;
}

.cpop-score-bar {
    height: 100%;
    border-radius: 99px;
    transition: width 0.4s ease;
}

.cpop-actions {
    display: flex;
    gap: 8px;
}

.cpop-btn {
    flex: 1;
    padding: 9px 0;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.cpop-btn-primary {
    background: linear-gradient(135deg, #0F766E, #065F46);
    color: white;
    box-shadow: 0 4px 12px rgba(15,118,110,0.25);
}

.cpop-btn-primary:hover {
    background: linear-gradient(135deg, #0D9488, #0F766E);
    color: white;
    transform: translateY(-1px);
}

/* Port popup */
.ppop {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    min-width: 270px;
    max-width: 310px;
}

.ppop-header {
    padding: 12px 16px 10px;
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.ppop-anchor-icon {
    width: 32px;
    height: 32px;
    object-fit: contain;
    flex-shrink: 0;
    margin-top: 2px;
}

.ppop-title {
    font-size: 14px;
    font-weight: 700;
    color: #1E3A5F;
    line-height: 1.3;
}

.ppop-subtitle {
    font-size: 12px;
    color: #2563EB;
    margin-top: 2px;
    font-weight: 500;
}

.ppop-body {
    padding: 12px 16px;
    background: #FFFFFF;
}

.ppop-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(37,99,235,0.08);
    border: 1px solid rgba(37,99,235,0.20);
    color: #2563EB;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 99px;
    margin-bottom: 10px;
}

.ppop-coords {
    font-size: 11px;
    color: #9CA3AF;
    margin-bottom: 10px;
    font-family: 'Courier New', monospace;
}

.ppop-weather-section {
    background: #F5F7F4;
    border-radius: 10px;
    padding: 10px 12px;
    border: 1px solid #E5E7EB;
}

.ppop-weather-title {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #9CA3AF;
    font-weight: 600;
    margin-bottom: 8px;
}

.ppop-weather-main-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}

.ppop-weather-img {
    width: 36px;
    height: 36px;
    object-fit: contain;
}

.ppop-weather-stats {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 6px;
}

.ppop-stat {
    text-align: center;
}

.ppop-stat-val {
    font-size: 13px;
    font-weight: 700;
    color: #1F2937;
    display: block;
}

.ppop-stat-lbl {
    font-size: 10px;
    color: #9CA3AF;
    display: block;
    margin-top: 1px;
}

.ppop-loading {
    text-align: center;
    color: #9CA3AF;
    font-size: 13px;
    padding: 10px 0;
}

/* Weather icon marker custom */
.weather-marker-icon {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}

.weather-marker-inner {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    transition: transform 0.15s ease;
    cursor: pointer;
}

.weather-marker-inner:hover {
    transform: scale(1.2);
}

/* Legend */
.map-legend {
    position: absolute;
    bottom: 24px;
    right: 12px;
    z-index: 900;
    background: rgba(255,255,255,0.97);
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 12px 14px;
    backdrop-filter: blur(12px);
    font-size: 12px;
    color: var(--text-secondary);
    min-width: 150px;
    box-shadow: 0 4px 16px rgba(15,118,110,0.10);
}

.map-legend-title {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 8px;
}

.legend-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 5px;
    font-size: 11px;
    font-weight: 500;
    color: #374151;
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.legend-divider {
    height: 1px;
    background: #E5E7EB;
    margin: 8px 0;
}

/* Responsive */
@media (max-width: 768px) {
    .map-filter-bar { flex-wrap: wrap; gap: 8px; }
    .map-stat-pills { margin-left: 0; }
    .map-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    .leaflet-popup-content { min-width: 260px !important; }
}
</style>
@endpush

@section('content')

<div class="map-page-wrap fade-in-up">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="map-header">

        <div class="map-header-title">
            <div class="map-icon-wrap">
                <i class="bi bi-globe-americas" style="color:white;"></i>
            </div>
            <div>
                <h1>Global Interactive Map</h1>
                <p style="font-size:13px;color:var(--text-muted);margin:0;">Real-time weather, risk scores & port monitoring</p>
            </div>
        </div>

        {{-- ═══ FILTER BAR ═══ --}}
        <div class="map-filter-bar">

            <span class="filter-label"><i class="bi bi-funnel" style="margin-right:4px;"></i>Layers</span>

            <label class="map-toggle active" id="toggleWeatherLabel">
                <input type="checkbox" id="toggleWeather" checked onchange="toggleLayer('weather')">
                <span class="toggle-dot" style="background:#F59E0B;"></span>
                Country Weather
            </label>

            <label class="map-toggle active" id="togglePortsLabel">
                <input type="checkbox" id="togglePorts" checked onchange="toggleLayer('ports')">
                <span class="toggle-dot" style="background:#3B82F6;"></span>
                Ports
            </label>

            <div class="map-stat-pills">
                <span class="map-pill">
                    <span class="pill-dot" style="background:#F59E0B;"></span>
                    <span id="pillCountryCount">—</span> Countries
                </span>
                <span class="map-pill">
                    <span class="pill-dot" style="background:#3B82F6;"></span>
                    {{ number_format($portCount) }} Ports
                </span>
            </div>

        </div>
    </div>

    {{-- ═══ MAP CANVAS ═══ --}}
    <div class="map-canvas-wrap">

        {{-- Loading overlay --}}
        <div class="map-loading" id="mapLoading">
            <div class="intel-spinner" style="width:32px;height:32px;border-width:3px;"></div>
            <div class="map-loading-bar"><div class="map-loading-bar-inner"></div></div>
            <div class="map-loading-text" id="loadingText">Loading global map data…</div>
        </div>

        {{-- Leaflet map --}}
        <div id="globalMap"></div>

        {{-- Legend Toggle Button --}}
        <button class="legend-toggle-btn" id="legendToggleBtn" onclick="toggleLegend()" style="display:none;">
            <i class="bi bi-info-circle"></i> Legend
        </button>

        {{-- Risk Legend --}}
        <div class="map-legend" id="mapLegend" style="display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <div class="map-legend-title" style="margin-bottom:0;">Legend</div>
                <button onclick="toggleLegend()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0;display:flex;"><i class="bi bi-x-lg" style="font-size:14px;"></i></button>
            </div>
            <div class="map-legend-title">Risk Level</div>
            <div class="legend-row"><span class="legend-dot" style="background:#10B981;"></span> Low</div>
            <div class="legend-row"><span class="legend-dot" style="background:#F59E0B;"></span> Medium</div>
            <div class="legend-row"><span class="legend-dot" style="background:#F97316;"></span> High</div>
            <div class="legend-row"><span class="legend-dot" style="background:#EF4444;"></span> Critical</div>
            <div class="legend-divider"></div>
            <div class="map-legend-title">Markers</div>
            <div class="legend-row" style="gap:6px;">
                <span style="font-size:16px;">☀️</span> Weather
            </div>
            <div class="legend-row" style="gap:6px;">
                <span style="font-size:16px;">⚓</span> Port
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
// ============================================================
//  GLOBAL INTERACTIVE MAP — Main Script
// ============================================================

const WEATHER_ICON_BASE = '/images/weather/';
const API_COUNTRIES     = '{{ route("api.map.countries") }}';
const API_PORTS         = '{{ route("api.map.ports") }}';
const API_PORT_WEATHER  = (id) => `/api/map/port/${id}/weather`;
const API_COUNTRY_POPUP = (code) => `/api/map/country/${code}`;

// ── Layer groups (so we can show/hide without re-fetching) ──
let weatherLayer = L.layerGroup();
let portLayer    = L.layerGroup();

// ── Map init ──
const map = L.map('globalMap', {
    center: [20, 15],
    zoom: 2,
    minZoom: 1,
    maxZoom: 16,
    zoomControl: false,
    attributionControl: true,
});

// Light tile layer
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '© <a href="https://carto.com/">CartoDB</a> © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

// Zoom control — top-right
L.control.zoom({ position: 'topright' }).addTo(map);

// ── Layers on map ──
weatherLayer.addTo(map);
portLayer.addTo(map);

// ============================================================
//  WEATHER ICON HELPER
// ============================================================

const WEATHER_EMOJI_MAP = {
    'clear': '☀️',
    'partly-cloudy': '🌤️',
    'cloudy': '☁️',
    'rain': '🌧️',
    'drizzle': '🌧️',
    'thunderstorm': '⛈️',
    'fog': '🌫️',
    'snow': '❄️',
    'wind': '💨'
};

function makeWeatherIcon(iconName, size = 22) {
    const emoji = WEATHER_EMOJI_MAP[iconName] || '🌤️';
    return L.divIcon({
        html: `<div class="weather-marker-inner" style="font-size:${size}px; line-height:1;">${emoji}</div>`,
        className: 'weather-marker-icon',
        iconSize: [size, size],
        iconAnchor: [size / 2, size / 2],
        popupAnchor: [0, -(size / 2)]
    });
}

function makePortIcon() {
    return L.divIcon({
        html: `<div class="weather-marker-inner" style="font-size:20px; line-height:1;">⚓</div>`,
        className: 'weather-marker-icon',
        iconSize: [20, 20],
        iconAnchor: [10, 10],
        popupAnchor: [0, -10]
    });
}

// ── Risk badge helper ──
function riskBadgeStyle(level) {
    const map = {
        Low:      { bg:'rgba(16,185,129,0.15)', border:'rgba(16,185,129,0.4)',  color:'#10B981' },
        Medium:   { bg:'rgba(245,158,11,0.15)', border:'rgba(245,158,11,0.4)',  color:'#F59E0B' },
        High:     { bg:'rgba(249,115,22,0.15)', border:'rgba(249,115,22,0.4)',  color:'#F97316' },
        Critical: { bg:'rgba(239,68,68,0.15)',  border:'rgba(239,68,68,0.4)',   color:'#EF4444' },
    };
    return map[level] || { bg:'rgba(107,114,128,0.15)', border:'rgba(107,114,128,0.4)', color:'#6B7280' };
}

// ============================================================
//  COUNTRY WEATHER MARKERS
// ============================================================

function buildCountryPopupSkeleton(code) {
    return `
    <div class="cpop">
      <div class="cpop-header">
        <img class="cpop-flag" id="cpop-flag-${code}" src="" alt="">
        <div class="cpop-name" id="cpop-name-${code}">Loading…</div>
        <span class="cpop-badge" id="cpop-badge-${code}">—</span>
      </div>
      <div class="cpop-body" id="cpop-body-${code}">
        <div style="text-align:center;padding:20px 0;color:#64748B;">
          <div class="intel-spinner" style="margin:0 auto 8px;"></div>
          <div style="font-size:12px;">Fetching data…</div>
        </div>
      </div>
    </div>`;
}

function fillCountryPopup(code, data) {
    const s = riskBadgeStyle(data.risk_level);
    // Header
    const flagEl  = document.getElementById('cpop-flag-' + code);
    const nameEl  = document.getElementById('cpop-name-' + code);
    const badgeEl = document.getElementById('cpop-badge-' + code);
    const bodyEl  = document.getElementById('cpop-body-' + code);

    if (flagEl)  flagEl.src = data.flag;
    if (nameEl)  nameEl.textContent = data.name;
    if (badgeEl) {
        badgeEl.textContent = data.risk_level;
        badgeEl.style.cssText = `background:${s.bg};border:1px solid ${s.border};color:${s.color};`;
    }

    if (bodyEl) {
        bodyEl.innerHTML = `
        <div class="cpop-weather-row">
            <img class="cpop-weather-icon"
                 src="${WEATHER_ICON_BASE}${data.weather_icon}.svg"
                 alt="${data.weather_condition}"
                 onerror="this.src='${WEATHER_ICON_BASE}partly-cloudy.svg'">
            <div class="cpop-weather-main">
                <div class="cpop-weather-cond">${data.weather_condition}</div>
                <div class="cpop-weather-temp">${data.temperature}</div>
            </div>
        </div>
        <div class="cpop-grid">
            <div class="cpop-item">
                <div class="cpop-item-label">Wind</div>
                <div class="cpop-item-value">${data.wind_speed}</div>
            </div>
            <div class="cpop-item">
                <div class="cpop-item-label">Rainfall</div>
                <div class="cpop-item-value">${data.rainfall}</div>
            </div>
            <div class="cpop-item">
                <div class="cpop-item-label">Ports</div>
                <div class="cpop-item-value">${data.port_count ?? '—'}</div>
            </div>
            <div class="cpop-item">
                <div class="cpop-item-label">Region</div>
                <div class="cpop-item-value" style="font-size:11px;">${data.region || '—'}</div>
            </div>
        </div>
        <div class="cpop-score-row">
            <div>
                <div class="cpop-score-label">Risk Score</div>
                <div class="cpop-score-val" style="color:${s.color};">${data.risk_score}</div>
                <div class="cpop-score-bar-wrap">
                    <div class="cpop-score-bar" style="width:${Math.min(data.risk_score, 100)}%;background:${s.color};"></div>
                </div>
            </div>
            <div style="text-align:right;">
                <div class="cpop-score-label">Risk Level</div>
                <span class="cpop-badge" style="background:${s.bg};border:1px solid ${s.border};color:${s.color};font-size:13px;padding:5px 14px;border-radius:99px;font-weight:700;">
                    ${data.risk_level}
                </span>
            </div>
        </div>
        <div class="cpop-actions">
            <a href="${data.detail_url}" class="cpop-btn cpop-btn-primary">
                <i class="bi bi-arrow-up-right-square"></i> View Detail
            </a>
        </div>`;
    }
}

function loadCountryMarkers(countries) {
    weatherLayer.clearLayers();
    document.getElementById('pillCountryCount').textContent = countries.length;

    countries.forEach(c => {
        const icon   = makeWeatherIcon(c.weather_icon || 'partly-cloudy');
        const marker = L.marker([c.lat, c.lng], { icon });

        // Bind a skeleton popup first
        const popup = L.popup({
            maxWidth: 320,
            className: 'map-popup-anim',
        }).setContent(buildCountryPopupSkeleton(c.code));

        marker.bindPopup(popup);

        // On popup open, fetch full data
        marker.on('popupopen', () => {
            fetch(API_COUNTRY_POPUP(c.code))
                .then(r => r.json())
                .then(data => fillCountryPopup(c.code, data))
                .catch(() => {
                    const bodyEl = document.getElementById('cpop-body-' + c.code);
                    if (bodyEl) bodyEl.innerHTML = `<div style="padding:16px;color:#EF4444;font-size:13px;">Failed to load data.</div>`;
                });
        });

        weatherLayer.addLayer(marker);
    });
}

// ============================================================
//  PORT MARKERS
// ============================================================

function buildPortPopupSkeleton(port) {
    return `
    <div class="ppop">
      <div class="ppop-header">
        <img class="ppop-anchor-icon" src="${WEATHER_ICON_BASE}port.svg" alt="Port">
        <div>
          <div class="ppop-title">${port.name}</div>
          <div class="ppop-subtitle">${port.country}</div>
        </div>
      </div>
      <div class="ppop-body">
        <span class="ppop-type-badge"><i class="bi bi-anchor"></i>${port.type}</span>
        <div class="ppop-coords">${port.lat.toFixed(4)}°, ${port.lng.toFixed(4)}°</div>
        <div class="ppop-weather-section" id="ppop-weather-${port.id}">
          <div class="ppop-loading">
            <div class="intel-spinner" style="margin:0 auto 6px;width:16px;height:16px;border-width:2px;"></div>
            <div style="font-size:11px;">Loading weather…</div>
          </div>
        </div>
      </div>
    </div>`;
}

function fillPortWeather(portId, data) {
    const el = document.getElementById('ppop-weather-' + portId);
    if (!el) return;
    el.innerHTML = `
      <div class="ppop-weather-title">Current Weather at Port</div>
      <div class="ppop-weather-main-row">
        <img class="ppop-weather-img"
             src="${WEATHER_ICON_BASE}${data.icon}.svg"
             alt="${data.condition}"
             onerror="this.src='${WEATHER_ICON_BASE}partly-cloudy.svg'">
        <div>
          <div style="font-size:14px;font-weight:700;color:#E2E8F0;">${data.condition}</div>
          <div style="font-size:18px;font-weight:800;color:#FDE68A;">${data.temperature}</div>
        </div>
      </div>
      <div class="ppop-weather-stats">
        <div class="ppop-stat">
          <span class="ppop-stat-val">${data.wind_speed}</span>
          <span class="ppop-stat-lbl">Wind</span>
        </div>
        <div class="ppop-stat">
          <span class="ppop-stat-val">${data.rainfall}</span>
          <span class="ppop-stat-lbl">Rain</span>
        </div>
        <div class="ppop-stat">
          <span class="ppop-stat-val">${data.humidity}</span>
          <span class="ppop-stat-lbl">Humidity</span>
        </div>
      </div>`;
}

function loadPortMarkers(ports) {
    portLayer.clearLayers();
    const portIcon = makePortIcon();

    ports.forEach(port => {
        const marker = L.marker([port.lat, port.lng], {
            icon: portIcon,
            riseOnHover: true,
        });

        const popup = L.popup({ maxWidth: 310 })
            .setContent(buildPortPopupSkeleton(port));

        marker.bindPopup(popup);

        marker.on('popupopen', () => {
            fetch(API_PORT_WEATHER(port.id))
                .then(r => r.json())
                .then(data => fillPortWeather(port.id, data))
                .catch(() => {
                    const el = document.getElementById('ppop-weather-' + port.id);
                    if (el) el.innerHTML = `<div style="color:#64748B;font-size:12px;text-align:center;padding:8px;">Weather unavailable</div>`;
                });
        });

        portLayer.addLayer(marker);
    });
}

// ============================================================
//  LAYER TOGGLE
// ============================================================

function toggleLegend() {
    const legend = document.getElementById('mapLegend');
    const btn = document.getElementById('legendToggleBtn');
    if (legend.style.display === 'none') {
        legend.style.display = 'block';
        btn.style.display = 'none';
    } else {
        legend.style.display = 'none';
        btn.style.display = 'flex';
    }
}

function toggleLayer(type) {
    if (type === 'weather') {
        const cb    = document.getElementById('toggleWeather');
        const label = document.getElementById('toggleWeatherLabel');
        if (cb.checked) {
            map.addLayer(weatherLayer);
            label.classList.add('active');
        } else {
            map.removeLayer(weatherLayer);
            label.classList.remove('active');
        }
    }
    if (type === 'ports') {
        const cb    = document.getElementById('togglePorts');
        const label = document.getElementById('togglePortsLabel');
        if (cb.checked) {
            map.addLayer(portLayer);
            label.classList.add('active');
        } else {
            map.removeLayer(portLayer);
            label.classList.remove('active');
        }
    }
}

// ============================================================
//  INIT — load all data
// ============================================================

function setLoadingText(text) {
    const el = document.getElementById('loadingText');
    if (el) el.textContent = text;
}

async function initMap() {
    const loadingEl = document.getElementById('mapLoading');
    const legendEl  = document.getElementById('mapLegend');

    try {
        // 1. Load country weather markers
        setLoadingText('Loading country weather data…');
        const countriesRes = await fetch(API_COUNTRIES);
        const countries    = await countriesRes.json();
        loadCountryMarkers(countries);

        // 2. Load port markers
        setLoadingText('Loading port markers…');
        const portsRes = await fetch(API_PORTS);
        const ports    = await portsRes.json();
        loadPortMarkers(ports);

        // 3. Hide loading, show legend toggle
        setLoadingText('Ready!');
        setTimeout(() => {
            loadingEl.classList.add('hidden');
            document.getElementById('legendToggleBtn').style.display = 'flex';
            showToast(`Map loaded — ${countries.length} countries, ${ports.length} ports`, 'success');
        }, 400);

    } catch (err) {
        console.error('Map init error:', err);
        setLoadingText('Failed to load map data. Please refresh.');
        showToast('Map data load failed', 'error');
    }
}

// Fire init after DOM ready
document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush