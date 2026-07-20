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

/* ─── Leaflet popup override (for Port popups only) ─── */
.leaflet-popup-content-wrapper {
    background: #FFFFFF !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 12px !important;
    box-shadow: 0 8px 30px rgba(15,118,110,0.15) !important;
    padding: 0 !important;
    overflow: hidden;
    color: #1F2937;
    max-width: 300px !important;
}

.leaflet-popup-tip {
    background: #FFFFFF !important;
}

.leaflet-popup-content {
    margin: 0 !important;
    width: auto !important;
    min-width: 250px;
    max-width: 300px;
}

.leaflet-popup {
    pointer-events: auto !important;
}

.leaflet-popup-close-button {
    color: #6B7280 !important;
    font-size: 18px !important;
    padding: 4px 8px !important;
}

.leaflet-popup-close-button:hover {
    color: #1F2937 !important;
}

/* Port popup */
.ppop {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    min-width: 250px;
    max-width: 290px;
}

.ppop-header {
    padding: 10px 14px 8px;
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    border-bottom: 1px solid #E5E7EB;
}

.ppop-title {
    font-size: 13px;
    font-weight: 700;
    color: #1E3A5F;
    line-height: 1.3;
}

.ppop-subtitle {
    font-size: 11px;
    color: #2563EB;
    margin-top: 2px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.ppop-body {
    padding: 10px 14px;
    background: #FFFFFF;
}

.ppop-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(37,99,235,0.08);
    border: 1px solid rgba(37,99,235,0.20);
    color: #2563EB;
    font-size: 10px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 99px;
    margin-bottom: 8px;
}

.ppop-coords {
    font-size: 10px;
    color: #9CA3AF;
    margin-bottom: 8px;
    font-family: 'Courier New', monospace;
}

.ppop-weather-section {
    background: #F5F7F4;
    border-radius: 8px;
    padding: 8px 10px;
    border: 1px solid #E5E7EB;
}

.ppop-weather-title {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #9CA3AF;
    font-weight: 600;
    margin-bottom: 6px;
}

.ppop-weather-main-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.ppop-weather-img {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.ppop-weather-stats {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 5px;
}

.ppop-stat {
    text-align: center;
}

.ppop-stat-val {
    font-size: 12px;
    font-weight: 700;
    color: #1F2937;
    display: block;
}

.ppop-stat-lbl {
    font-size: 9px;
    color: #9CA3AF;
    display: block;
    margin-top: 1px;
}

.ppop-loading {
    text-align: center;
    color: #9CA3AF;
    font-size: 12px;
    padding: 8px 0;
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
    transition: transform 0.2s ease;
    cursor: pointer;
}

.weather-marker-inner:hover {
    transform: scale(1.3);
}

/* Port circle marker hover */
.leaflet-interactive:hover {
    cursor: pointer;
}

/* Legend */
.map-legend {
    position: absolute;
    bottom: 24px;
    right: 12px;
    z-index: 900;
    background: rgba(255,255,255,0.98);
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 10px 12px;
    backdrop-filter: blur(12px);
    font-size: 12px;
    color: var(--text-secondary);
    min-width: 180px;
    max-width: 220px;
    max-height: calc(100% - 48px);
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(15,118,110,0.12);
}

.map-legend-title {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 6px;
}

.legend-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
    font-size: 10px;
    font-weight: 500;
    color: #374151;
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.legend-divider {
    height: 1px;
    background: #E5E7EB;
    margin: 6px 0;
}

/* Responsive */
@media (max-width: 768px) {
    .map-filter-bar { flex-wrap: wrap; gap: 8px; }
    .map-stat-pills { margin-left: 0; }
    .map-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    .leaflet-popup-content { min-width: 240px !important; max-width: 280px !important; }
    .weather-summary-panel { width: 100% !important; right: 0 !important; }
}

/* Weather Summary Panel */
.weather-summary-panel {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 320px;
    max-height: calc(100% - 24px);
    background: rgba(255, 255, 255, 0.98);
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(15, 118, 110, 0.15);
    backdrop-filter: blur(12px);
    z-index: 900;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.weather-summary-header {
    padding: 16px 18px;
    background: linear-gradient(135deg, #F0FDF4, #DCFCE7);
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.weather-summary-header h3 {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.weather-summary-header h3 i {
    color: var(--accent);
    font-size: 16px;
}

.weather-summary-body {
    padding: 16px;
    overflow-y: auto;
    flex: 1;
}

.weather-summary-country {
    margin-bottom: 16px;
}

.weather-summary-country-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.weather-summary-flag {
    width: 32px;
    height: 24px;
    border-radius: 4px;
    object-fit: cover;
    border: 1px solid rgba(0,0,0,0.08);
}

.weather-summary-country-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    flex: 1;
}

.weather-summary-main {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--bg-base);
    border-radius: 10px;
    margin-bottom: 12px;
}

.weather-summary-icon {
    width: 48px;
    height: 48px;
    object-fit: contain;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.15));
}

.weather-summary-temp {
    flex: 1;
}

.weather-summary-condition {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 4px;
}

.weather-summary-temperature {
    font-size: 28px;
    font-weight: 800;
    color: #D97706;
    line-height: 1;
}

.weather-summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 12px;
}

.weather-summary-item {
    background: #FFFFFF;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 10px;
    text-align: center;
}

.weather-summary-item-icon {
    font-size: 14px;
    color: var(--accent);
    display: block;
    margin-bottom: 4px;
}

.weather-summary-item-value {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    display: block;
    margin-bottom: 2px;
}

.weather-summary-item-label {
    font-size: 10px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.weather-alerts {
    background: #FEF3C7;
    border: 1px solid #FCD34D;
    border-radius: 10px;
    padding: 12px;
}

.weather-alerts-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #92400E;
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.weather-alert-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    background: #FFFFFF;
    border-radius: 6px;
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #92400E;
}

.weather-alert-item:last-child {
    margin-bottom: 0;
}

.weather-alert-icon {
    font-size: 14px;
}

.weather-alert-critical {
    background: #FEE2E2;
    border: 1px solid #FCA5A5;
    color: #991B1B;
}

.weather-alert-high {
    background: #FED7AA;
    border: 1px solid #FDBA74;
    color: #9A3412;
}

.weather-alert-medium {
    background: #FEF3C7;
    border: 1px solid #FDE047;
    color: #713F12;
}

.weather-no-alerts {
    text-align: center;
    padding: 12px;
    color: var(--text-muted);
    font-size: 12px;
}

.weather-no-alerts i {
    font-size: 24px;
    display: block;
    margin-bottom: 8px;
    opacity: 0.3;
}

.weather-summary-risk {
    background: var(--bg-base);
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 12px;
}

.weather-summary-risk-header {
    margin-bottom: 10px;
}

.weather-summary-risk-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.weather-summary-actions {
    margin-top: 4px;
}

.weather-summary-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.weather-summary-btn-primary {
    background: linear-gradient(135deg, #0F766E, #065F46);
    color: white;
    box-shadow: 0 3px 12px rgba(15, 118, 110, 0.25);
}

.weather-summary-btn-primary:hover {
    background: linear-gradient(135deg, #0D9488, #0F766E);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(15, 118, 110, 0.35);
}

/* Active marker styling */
.weather-marker-inner {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Responsive */
@media (max-width: 768px) {
    .map-filter-bar { flex-wrap: wrap; gap: 8px; }
    .map-stat-pills { margin-left: 0; }
    .map-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    .leaflet-popup-content { min-width: 240px !important; max-width: 280px !important; }
    .weather-summary-panel { 
        width: 100% !important; 
        right: 0 !important; 
        border-radius: 0;
        top: 0;
        max-height: 100%;
    }
}

/* Leaflet container smooth drag */
.leaflet-container {
    cursor: grab;
}

.leaflet-container:active {
    cursor: grabbing;
}

.leaflet-dragging .leaflet-container {
    cursor: grabbing !important;
}

/* Smooth popup animation */
@keyframes popupFadeIn {
    from {
        opacity: 0;
        transform: translateY(10px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.leaflet-popup {
    animation: popupFadeIn 0.2s ease-out;
}

/* Marker smooth transitions */
.leaflet-marker-icon {
    transition: all 0.2s ease;
}

/* Circle marker pulse effect on click */
@keyframes circlePulse {
    0% {
        box-shadow: 0 0 0 0 rgba(15, 118, 110, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(15, 118, 110, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(15, 118, 110, 0);
    }
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

    {{-- ═══ WEATHER SUMMARY PANEL ═══ --}}
    <div class="weather-summary-panel" id="weatherSummaryPanel" style="display:none;">
        <div class="weather-summary-header">
            <h3><i class="bi bi-cloud-sun"></i> Weather Summary</h3>
            <button onclick="closeWeatherSummary()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:4px;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="weather-summary-body" id="weatherSummaryBody">
            <div style="text-align:center;padding:40px 20px;color:var(--text-muted);">
                <i class="bi bi-cursor" style="font-size:32px;display:block;margin-bottom:12px;opacity:0.3;"></i>
                <p style="font-size:13px;">Click on a country marker to view detailed weather information</p>
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
                <div class="map-legend-title" style="margin-bottom:0;">Map Legend</div>
                <button onclick="toggleLegend()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0;display:flex;"><i class="bi bi-x-lg" style="font-size:14px;"></i></button>
            </div>
            
            <div class="map-legend-title">Weather Conditions</div>
            <div class="legend-row" style="gap:8px;"><span style="font-size:16px;">☀️</span> Clear Sky</div>
            <div class="legend-row" style="gap:8px;"><span style="font-size:16px;">🌤️</span> Partly Cloudy</div>
            <div class="legend-row" style="gap:8px;"><span style="font-size:16px;">☁️</span> Cloudy</div>
            <div class="legend-row" style="gap:8px;"><span style="font-size:16px;">🌧️</span> Rain / Drizzle</div>
            <div class="legend-row" style="gap:8px;"><span style="font-size:16px;">⛈️</span> Thunderstorm</div>
            <div class="legend-row" style="gap:8px;"><span style="font-size:16px;">❄️</span> Snow</div>
            <div class="legend-row" style="gap:8px;"><span style="font-size:16px;">💨</span> Strong Wind</div>
            <div class="legend-row" style="gap:8px;"><span style="font-size:16px;">🌫️</span> Fog / Mist</div>
            
            <div class="legend-divider"></div>
            
            <div class="map-legend-title">Risk Level</div>
            <div class="legend-row"><span class="legend-dot" style="background:#10B981;"></span> Low</div>
            <div class="legend-row"><span class="legend-dot" style="background:#F59E0B;"></span> Medium</div>
            <div class="legend-row"><span class="legend-dot" style="background:#F97316;"></span> High</div>
            <div class="legend-row"><span class="legend-dot" style="background:#EF4444;"></span> Critical</div>
            
            <div class="legend-divider"></div>
            
            <div class="map-legend-title">Port Status</div>
            <div class="legend-row"><span class="legend-dot" style="background:#10B981;border:2px solid #FFF;"></span> Normal</div>
            <div class="legend-row"><span class="legend-dot" style="background:#F59E0B;border:2px solid #FFF;"></span> Busy</div>
            <div class="legend-row"><span class="legend-dot" style="background:#F97316;border:2px solid #FFF;"></span> Congested</div>
            <div class="legend-row"><span class="legend-dot" style="background:#EF4444;border:2px solid #FFF;"></span> High Risk</div>
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
    dragging: true,
    touchZoom: true,
    scrollWheelZoom: true,
    doubleClickZoom: true,
    boxZoom: true,
    keyboard: true,
    tap: true,
    zoomAnimation: true,
    zoomAnimationThreshold: 4,
    fadeAnimation: true,
    markerZoomAnimation: true
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

function makeWeatherIcon(iconName, size = 14) {
    const emoji = WEATHER_EMOJI_MAP[iconName] || '🌤️';
    return L.divIcon({
        html: `<div class="weather-marker-inner" style="font-size:${size}px; line-height:1;">${emoji}</div>`,
        className: 'weather-marker-icon',
        iconSize: [size, size],
        iconAnchor: [size / 2, size / 2],
        popupAnchor: [0, -(size / 2)]
    });
}

function makePortCircle(riskLevel = 'Normal') {
    const colorMap = {
        'Normal': '#10B981',      // Hijau
        'Busy': '#F59E0B',        // Kuning
        'Congested': '#F97316',   // Oranye
        'High Risk': '#EF4444'    // Merah
    };
    return colorMap[riskLevel] || '#10B981';
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
//  WEATHER SUMMARY PANEL
// ============================================================

function updateWeatherSummary(data) {
    const panel = document.getElementById('weatherSummaryPanel');
    const body = document.getElementById('weatherSummaryBody');
    
    if (!panel || !body) return;
    
    // Show panel
    panel.style.display = 'flex';
    
    // Generate weather alerts
    const alerts = generateWeatherAlerts(data);
    
    // Parse numeric values for better display
    const tempValue = parseFloat(data.temperature);
    const windValue = parseFloat(data.wind_speed);
    const rainValue = parseFloat(data.rainfall);
    
    // Build humidity and pressure from additional data (if available)
    const humidity = data.humidity || 'N/A';
    const pressure = data.pressure || 'N/A';
    
    // Risk score styling
    const riskStyle = riskBadgeStyle(data.risk_level);
    
    body.innerHTML = `
        <div class="weather-summary-country">
            <div class="weather-summary-country-header">
                <img src="${data.flag}" alt="${data.name}" class="weather-summary-flag">
                <div class="weather-summary-country-name">${data.name}</div>
            </div>
            
            <div class="weather-summary-main">
                <img src="${WEATHER_ICON_BASE}${data.weather_icon}.svg" 
                     alt="${data.weather_condition}" 
                     class="weather-summary-icon"
                     onerror="this.src='${WEATHER_ICON_BASE}partly-cloudy.svg'">
                <div class="weather-summary-temp">
                    <div class="weather-summary-condition">${data.weather_condition}</div>
                    <div class="weather-summary-temperature">${data.temperature}</div>
                </div>
            </div>
            
            <div class="weather-summary-grid">
                <div class="weather-summary-item">
                    <i class="bi bi-wind weather-summary-item-icon"></i>
                    <span class="weather-summary-item-value">${data.wind_speed}</span>
                    <span class="weather-summary-item-label">Wind Speed</span>
                </div>
                <div class="weather-summary-item">
                    <i class="bi bi-droplet-fill weather-summary-item-icon"></i>
                    <span class="weather-summary-item-value">${data.rainfall}</span>
                    <span class="weather-summary-item-label">Rainfall</span>
                </div>
                <div class="weather-summary-item">
                    <i class="bi bi-moisture weather-summary-item-icon"></i>
                    <span class="weather-summary-item-value">${humidity}</span>
                    <span class="weather-summary-item-label">Humidity</span>
                </div>
                <div class="weather-summary-item">
                    <i class="bi bi-speedometer weather-summary-item-icon"></i>
                    <span class="weather-summary-item-value">${pressure}</span>
                    <span class="weather-summary-item-label">Pressure</span>
                </div>
            </div>
            
            <div class="weather-summary-risk">
                <div class="weather-summary-risk-header">
                    <span style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Risk Assessment</span>
                </div>
                <div class="weather-summary-risk-content">
                    <div>
                        <div style="font-size:10px;color:var(--text-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Risk Score</div>
                        <div style="font-size:24px;font-weight:800;color:${riskStyle.color};line-height:1;">${data.risk_score}</div>
                        <div style="height:4px;background:rgba(0,0,0,0.05);border-radius:99px;margin-top:6px;overflow:hidden;width:80px;">
                            <div style="height:100%;width:${Math.min(data.risk_score, 100)}%;background:${riskStyle.color};border-radius:99px;transition:width 0.4s;"></div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:10px;color:var(--text-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Risk Level</div>
                        <span style="display:inline-block;background:${riskStyle.bg};border:1px solid ${riskStyle.border};color:${riskStyle.color};font-size:14px;padding:6px 16px;border-radius:99px;font-weight:700;">
                            ${data.risk_level}
                        </span>
                    </div>
                </div>
            </div>
            
            ${alerts.length > 0 ? `
                <div class="weather-alerts">
                    <div class="weather-alerts-title">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Weather Alerts
                    </div>
                    ${alerts.map(alert => `
                        <div class="weather-alert-item ${alert.class}">
                            <span class="weather-alert-icon">${alert.icon}</span>
                            <span>${alert.message}</span>
                        </div>
                    `).join('')}
                </div>
            ` : `
                <div class="weather-no-alerts">
                    <i class="bi bi-check-circle-fill"></i>
                    <p>No weather alerts at this time</p>
                </div>
            `}
            
            <div class="weather-summary-actions">
                <a href="${data.detail_url}" class="weather-summary-btn weather-summary-btn-primary">
                    <i class="bi bi-arrow-up-right-square"></i> View Full Details
                </a>
            </div>
        </div>
    `;
}

function generateWeatherAlerts(data) {
    const alerts = [];
    
    // Parse values
    const windValue = parseFloat(data.wind_speed);
    const rainValue = parseFloat(data.rainfall);
    const condition = (data.weather_condition || '').toLowerCase();
    
    // Storm/Thunderstorm alert
    if (condition.includes('storm') || condition.includes('thunder')) {
        alerts.push({
            icon: '⛈️',
            message: 'Thunderstorm detected',
            class: 'weather-alert-critical'
        });
    }
    
    // Heavy rain alert
    if (rainValue > 50) {
        alerts.push({
            icon: '🌧️',
            message: 'Heavy rainfall: ' + rainValue.toFixed(1) + ' mm',
            class: 'weather-alert-critical'
        });
    } else if (rainValue > 20) {
        alerts.push({
            icon: '🌧️',
            message: 'Moderate rainfall: ' + rainValue.toFixed(1) + ' mm',
            class: 'weather-alert-high'
        });
    } else if (rainValue > 5) {
        alerts.push({
            icon: '🌧️',
            message: 'Light rain: ' + rainValue.toFixed(1) + ' mm',
            class: 'weather-alert-medium'
        });
    }
    
    // Strong wind alert
    if (windValue > 75) {
        alerts.push({
            icon: '💨',
            message: 'Very strong winds: ' + windValue.toFixed(1) + ' km/h',
            class: 'weather-alert-critical'
        });
    } else if (windValue > 50) {
        alerts.push({
            icon: '💨',
            message: 'Strong winds: ' + windValue.toFixed(1) + ' km/h',
            class: 'weather-alert-high'
        });
    } else if (windValue > 30) {
        alerts.push({
            icon: '💨',
            message: 'Moderate winds: ' + windValue.toFixed(1) + ' km/h',
            class: 'weather-alert-medium'
        });
    }
    
    // Snow alert
    if (condition.includes('snow') || condition.includes('blizzard')) {
        alerts.push({
            icon: '❄️',
            message: 'Snow conditions detected',
            class: 'weather-alert-high'
        });
    }
    
    // Fog alert
    if (condition.includes('fog') || condition.includes('mist')) {
        alerts.push({
            icon: '🌫️',
            message: 'Low visibility due to fog',
            class: 'weather-alert-medium'
        });
    }
    
    return alerts;
}

function closeWeatherSummary() {
    const panel = document.getElementById('weatherSummaryPanel');
    if (panel) panel.style.display = 'none';
}

// ============================================================
//  COUNTRY WEATHER MARKERS
// ============================================================

function loadCountryMarkers(countries) {
    weatherLayer.clearLayers();
    document.getElementById('pillCountryCount').textContent = countries.length;

    countries.forEach(c => {
        // Validasi koordinat
        if (!c.lat || !c.lng || isNaN(c.lat) || isNaN(c.lng)) {
            console.warn(`Invalid coordinates for ${c.name}:`, c.lat, c.lng);
            return;
        }

        // Ensure lat/lng are numbers
        const lat = parseFloat(c.lat);
        const lng = parseFloat(c.lng);

        // Validate coordinate range
        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            console.warn(`Out of range coordinates for ${c.name}:`, lat, lng);
            return;
        }

        const icon   = makeWeatherIcon(c.weather_icon || 'partly-cloudy');
        const marker = L.marker([lat, lng], { 
            icon,
            riseOnHover: true,
            title: c.name // Tooltip saat hover
        });

        // Store country code for fetching full data
        marker.countryCode = c.code;

        // On click, fetch data and show in Weather Summary Panel (no popup)
        marker.on('click', function() {
            // Remove previous active marker styling
            document.querySelectorAll('.weather-marker-inner').forEach(el => {
                el.style.transform = '';
                el.style.filter = '';
            });

            // Add active styling to clicked marker
            const markerElement = this.getElement();
            if (markerElement) {
                const innerElement = markerElement.querySelector('.weather-marker-inner');
                if (innerElement) {
                    innerElement.style.transform = 'scale(1.5)';
                    innerElement.style.filter = 'drop-shadow(0 0 8px rgba(15, 118, 110, 0.6))';
                }
            }

            // Fetch full country data
            fetch(API_COUNTRY_POPUP(c.code))
                .then(r => r.json())
                .then(data => {
                    updateWeatherSummary(data);
                    // Auto-show panel if hidden
                    const panel = document.getElementById('weatherSummaryPanel');
                    if (panel) panel.style.display = 'flex';
                })
                .catch(err => {
                    console.error('Failed to fetch country data:', err);
                    showToast('Failed to load country data', 'error');
                });
        });

        weatherLayer.addLayer(marker);
    });

    console.log(`✓ Loaded ${countries.length} country weather markers`);
}

// ============================================================
//  PORT MARKERS
// ============================================================

function buildPortPopupSkeleton(port) {
    return `
    <div class="ppop">
      <div class="ppop-header">
        <div style="width:100%;">
          <div class="ppop-title">${port.name}</div>
          <div class="ppop-subtitle"><i class="bi bi-geo-alt-fill"></i> ${port.country}</div>
        </div>
      </div>
      <div class="ppop-body">
        <span class="ppop-type-badge"><i class="bi bi-diagram-3"></i> ${port.type}</span>
        <div class="ppop-coords"><i class="bi bi-pin-map"></i> ${port.lat.toFixed(4)}°, ${port.lng.toFixed(4)}°</div>
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
      <div class="ppop-weather-title"><i class="bi bi-cloud-sun"></i> Current Weather at Port</div>
      <div class="ppop-weather-main-row">
        <img class="ppop-weather-img"
             src="${WEATHER_ICON_BASE}${data.icon}.svg"
             alt="${data.condition}"
             onerror="this.src='${WEATHER_ICON_BASE}partly-cloudy.svg'">
        <div>
          <div style="font-size:12px;font-weight:700;color:#1F2937;">${data.condition}</div>
          <div style="font-size:16px;font-weight:800;color:#F59E0B;">${data.temperature}</div>
        </div>
      </div>
      <div class="ppop-weather-stats">
        <div class="ppop-stat">
          <span class="ppop-stat-val"><i class="bi bi-wind" style="font-size:10px;color:#64748B;"></i> ${data.wind_speed}</span>
          <span class="ppop-stat-lbl">Wind</span>
        </div>
        <div class="ppop-stat">
          <span class="ppop-stat-val"><i class="bi bi-droplet" style="font-size:10px;color:#64748B;"></i> ${data.rainfall}</span>
          <span class="ppop-stat-lbl">Rain</span>
        </div>
        <div class="ppop-stat">
          <span class="ppop-stat-val"><i class="bi bi-moisture" style="font-size:10px;color:#64748B;"></i> ${data.humidity}</span>
          <span class="ppop-stat-lbl">Humidity</span>
        </div>
      </div>`;
}

function loadPortMarkers(ports) {
    portLayer.clearLayers();

    ports.forEach(port => {
        // Validasi koordinat
        if (!port.lat || !port.lng || isNaN(port.lat) || isNaN(port.lng)) {
            console.warn(`Invalid coordinates for ${port.name}:`, port.lat, port.lng);
            return;
        }

        // Ensure lat/lng are numbers
        const lat = parseFloat(port.lat);
        const lng = parseFloat(port.lng);

        // Validate coordinate range
        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            console.warn(`Out of range coordinates for ${port.name}:`, lat, lng);
            return;
        }

        const portColor = makePortCircle(port.status || 'Normal');
        
        // Gunakan CircleMarker dengan koordinat yang tepat
        const marker = L.circleMarker([lat, lng], {
            radius: 7,
            fillColor: portColor,
            color: '#FFFFFF',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9,
            title: port.name // Tooltip
        });

        const popup = L.popup({ 
            maxWidth: 310,
            closeButton: true,
            autoClose: true,
            closeOnClick: false
        }).setContent(buildPortPopupSkeleton(port));

        marker.bindPopup(popup);

        // Hover effect
        marker.on('mouseover', function() {
            this.setStyle({
                radius: 9,
                weight: 3
            });
        });

        marker.on('mouseout', function() {
            this.setStyle({
                radius: 7,
                weight: 2
            });
        });

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

    console.log(`✓ Loaded ${ports.length} port markers`);
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
        
        console.log('📍 Country data received:', countries.length, 'countries');
        if (countries.length > 0) {
            console.log('Sample country:', countries[0]);
        }
        
        loadCountryMarkers(countries);

        // 2. Load port markers
        setLoadingText('Loading port markers…');
        const portsRes = await fetch(API_PORTS);
        const ports    = await portsRes.json();
        
        console.log('⚓ Port data received:', ports.length, 'ports');
        if (ports.length > 0) {
            console.log('Sample port:', ports[0]);
        }
        
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