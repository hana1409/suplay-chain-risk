@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h3 class="mb-4">
        🌍 Global Interactive Map
    </h3>

    <div id="map"
        style="height:700px;border-radius:15px;">
    </div>

</div>

<link
rel="stylesheet"
href="https://unpkg.com/leaflet/dist/leaflet.css"
/>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>

const map = L.map('map').setView([20,0],2);

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{
    attribution:'© OpenStreetMap'
}
).addTo(map);

const ports = @json($ports);

ports.forEach(port=>{

    L.marker([port.latitude,port.longitude])

    .addTo(map)

    .bindPopup(
        `
        <b>${port.port_name}</b><br>

        Country :
        ${port.country.country_name}<br>

        Type :
        ${port.port_type}
        `
    );

});

</script>

@endsection