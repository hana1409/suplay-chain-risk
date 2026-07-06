@extends('layouts.app')

@section('title','Dashboard')

@section('content')

<div class="dashboard-wrapper">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Content --}}
    <div class="main-content">

        {{-- Topbar --}}
        @include('partials.topbar')

        <div class="container-fluid mt-4">

            {{-- Summary Cards --}}
            <div class="dashboard-content">

                <div class="row g-4">

                    @include('components.dashboard.stat-card',[
                        'title'=>'Average Risk',
                        'value'=>'32',
                        'icon'=>'bi-shield-fill-check',
                        'footer'=>'Low Risk'
                    ])

                    @include('components.dashboard.stat-card',[
                        'title'=>'Countries',
                        'value'=>'195',
                        'icon'=>'bi-globe2',
                        'footer'=>'Monitored'
                    ])

                    @include('components.dashboard.stat-card',[
                        'title'=>'Ports',
                        'value'=>'1240',
                        'icon'=>'bi-geo-alt-fill',
                        'footer'=>'Active'
                    ])

                    @include('components.dashboard.stat-card',[
                        'title'=>'News',
                        'value'=>'128',
                        'icon'=>'bi-newspaper',
                        'footer'=>'Updated Today'
                    ])

                </div>

            </div>

            {{-- Map & Right Information --}}
            <div class="row mt-4">

                {{-- Global Risk Map --}}
                <div class="col-lg-8">

                    <div class="dashboard-card">

                        <div class="card-header-custom">

                            <h5>🌍 Global Risk Map</h5>

                            <button class="btn btn-outline-light btn-sm">
                                View Full Map
                            </button>

                        </div>

                        <div id="world-map">

                            <div class="map-placeholder">

                                Leaflet.js Map will appear here

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Right Side --}}
                <div class="col-lg-4">

                    @include('components.dashboard.info-card',[
                        'title'=>'Weather',
                        'value'=>'29°C',
                        'icon'=>'bi-cloud-sun-fill',
                        'footer'=>'Sunny'
                    ])

                    <div class="mt-3"></div>

                    @include('components.dashboard.info-card',[
                        'title'=>'Currency',
                        'value'=>'USD / IDR',
                        'icon'=>'bi-currency-exchange',
                        'footer'=>'16,250'
                    ])

                    <div class="mt-3"></div>

                    @include('components.dashboard.info-card',[
                        'title'=>'News',
                        'value'=>'128 Articles',
                        'icon'=>'bi-newspaper',
                        'footer'=>'Updated Today'
                    ])

                </div>

            </div>

        </div>

    </div>

</div>

@endsection