@extends('pasajero.layouts.app')
@section('title', 'Mapa de paradas y Rutas')

@section('content')
<div class="container-fluid py-3 px-4">

    <div class="pas-header mb-3">
        <div>
            <h1><span class="material-symbols-rounded">map</span> Mapa de Rutas</h1>
            <p>Visualiza el recorrido de nuestras rutas y paradas principales.</p>
        </div>
    </div>

    @if(!$ciudadObj || (!$ciudadObj->latitud ?? true))
    <div class="pas-alert warn mb-3">
        <span class="material-symbols-rounded" style="font-size:1.1rem;flex-shrink:0">info</span>
        Las coordenadas exactas de las paradas no están disponibles aún.
        El mapa muestra una vista aproximada basada en los barrios de las rutas activas.
    </div>
    @endif

    <div class="mapa-wrap">

        {{-- ── Sidebar ─────────────────── --}}
        <div class="mapa-sidebar">

            {{-- Buscador General --}}
            <div class="pas-card" style="flex-shrink:0">
                <div class="pas-card-body" style="padding:.75rem 1rem">
                    <input type="text" id="buscarRutaBarrio"
                           class="form-control form-control-sm"
                           placeholder="Buscar ruta o barrio..."
                           oninput="filtrarSidebar(this.value)"
                           style="border-color:var(--border);border-radius:var(--r-sm)">
                </div>
            </div>

            {{-- Lista de Rutas --}}
            <div class="pas-card" style="flex:1;overflow:hidden;display:flex;flex-direction:column; margin-bottom: 1rem;">
                <div class="pas-card-head" style="flex-shrink:0; padding: .75rem 1rem;">
                    <h3>
                        <span class="material-symbols-rounded">route</span>
                        Trazar Rutas ({{ $rutas->count() }})
                    </h3>
                </div>
                <div class="mapa-sidebar-inner" id="listaRutas">
                    @forelse($rutas as $ruta)
                        @php
                            $latO = $ruta->barrioOrigen->latitud ?? null;
                            $lngO = $ruta->barrioOrigen->longitud ?? null;
                            $latD = $ruta->barrioDestino->latitud ?? null;
                            $lngD = $ruta->barrioDestino->longitud ?? null;
                        @endphp
                        
                        <div class="mapa-parada-item sidebar-item"
                             data-nombre="ruta {{ strtolower($ruta->codigo_ruta) }}"
                             onclick="trazarRutaGoogle('{{ $latO }}', '{{ $lngO }}', '{{ $latD }}', '{{ $lngD }}', '{{ $ruta->codigo_ruta }}', this)">
                            <div class="mapa-parada-dot" style="background:#2563EB"></div>
                            <div>
                                <div class="mapa-parada-nombre">Ruta {{ $ruta->codigo_ruta }}</div>
                                <div class="mapa-parada-meta" style="font-size:.75rem">
                                    {{ $ruta->barrioOrigen->nombre }} <span class="material-symbols-rounded" style="font-size:12px;vertical-align:middle;">arrow_forward</span> {{ $ruta->barrioDestino->nombre }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="pas-empty">
                            <span class="material-symbols-rounded">block</span>
                            <p>No hay rutas registradas.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Lista de paradas --}}
            <div class="pas-card" style="flex:1;max-height: 250px; overflow:hidden;display:flex;flex-direction:column">
                <div class="pas-card-head" style="flex-shrink:0; padding: .75rem 1rem;">
                    <h3>
                        <span class="material-symbols-rounded">location_on</span>
                        Paradas Principales ({{ $barrios->count() }})
                    </h3>
                </div>
                <div class="mapa-sidebar-inner" id="listaBarrios">
                    @forelse($barrios as $barrio)
                    <div class="mapa-parada-item sidebar-item"
                         data-nombre="{{ strtolower($barrio->nombre) }}"
                         onclick="centrarMapaMarcador('{{ $barrio->id_barrio }}')">
                        <div class="mapa-parada-dot" style="background:#6B7280"></div>
                        <div>
                            <div class="mapa-parada-nombre">{{ $barrio->nombre }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="pas-empty">
                        <span class="material-symbols-rounded">location_off</span>
                        <p>No hay barrios registrados.</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ── Mapa (Google Maps) ───────────────────────────────── --}}
        <div class="mapa-container" style="position: relative;">
            <div id="map" style="width: 100%; height: 100%; border-radius: var(--r-md); overflow: hidden;"></div>
            
            <button class="btn btn-sm btn-light" id="btnRestablecer" onclick="restablecerMapa()" 
                    style="position: absolute; top: 1rem; right: 1rem; z-index: 10; display: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: var(--r-full); padding: 0.5rem 1rem;">
                <span class="material-symbols-rounded" style="font-size: 1rem; vertical-align: middle; margin-right: 0.25rem;">visibility_off</span>
                Limpiar Ruta
            </button>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .sigu-main { padding-bottom: 0 !important; }
</style>
@endpush

@push('scripts')
{{-- Asegúrate de establecer tu API Key de Google Maps en el archivo .env --}}
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
<script>
let map;
let directionsService;
let directionsRenderer;
let markers = {};

// Ciudad por defecto
const ciudadLat = {{ $ciudadObj->latitud  ?? 4.4389 }};
const ciudadLng = {{ $ciudadObj->longitud ?? -75.2322 }};

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: ciudadLat, lng: ciudadLng },
        zoom: 13,
        mapTypeId: "roadmap",
        mapTypeControl: false,
        streetViewControl: false,
        styles: [
            {
                "featureType": "poi",
                "elementType": "labels",
                "stylers": [{ "visibility": "off" }]
            }
        ]
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: false,
        polylineOptions: {
            strokeColor: '#2563EB', // Color azul vibrante de la app
            strokeOpacity: 0.8,
            strokeWeight: 5
        }
    });

    generarMarcadoresParadas();
}

function generarMarcadoresParadas() {
    const barrios = @json($barrios);
    const rutas = @json($rutas);

    // Identificamos que barrios son orígenes o destinos para pintarlos diferente
    const origenes  = new Set(rutas.map(r => r.id_barrio_origen));
    const destinos  = new Set(rutas.map(r => r.id_barrio_destino));

    barrios.forEach((b, i) => {
        let lat = parseFloat(b.latitud);
        let lng = parseFloat(b.longitud);

        if (!lat || !lng) {
            const fila = Math.floor(i / 5);
            const col  = i % 5;
            lat = ciudadLat + (fila - 2) * 0.005;
            lng = ciudadLng + (col - 2) * 0.005;
        }

        const position = { lat: lat, lng: lng };
        const esOrigen  = origenes.has(b.id_barrio);
        const esDestino = destinos.has(b.id_barrio);
        
        // Colores de Google Maps markers genéricos según si es origen, destino o parada normal
        let pinColor = esOrigen && esDestino ? "4285F4" : esOrigen ? "34A853" : esDestino ? "EA4335" : "9AA0A6";
        
        const pinImage = new google.maps.MarkerImage(
            "https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
            new google.maps.Size(21, 34),
            new google.maps.Point(0,0),
            new google.maps.Point(10, 34)
        );

        const marker = new google.maps.Marker({
            position: position,
            map: map,
            title: b.nombre,
            icon: pinImage
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `
            <div style="font-family:'Inter Tight',sans-serif; min-width:120px; padding:2px;">
                <h6 style="margin:0 0 5px 0;font-weight:700;">${b.nombre}</h6>
                <span style="font-size:11px;color:#6B7280;">Parada principal</span>
            </div>`
        });

        marker.addListener("click", () => {
            infoWindow.open({ anchor: marker, map, shouldFocus: false });
        });

        markers[b.id_barrio] = { marker, lat, lng };
    });
}

function trazarRutaGoogle(latO, lngO, latD, lngD, codigoRuta, el) {
    if (!latO || !lngO || !latD || !lngD) {
        alert('Esta ruta no tiene coordenadas válidas de origen o destino.');
        return;
    }

    const request = {
        origin: { lat: parseFloat(latO), lng: parseFloat(lngO) },
        destination: { lat: parseFloat(latD), lng: parseFloat(lngD) },
        travelMode: google.maps.TravelMode.DRIVING 
    };

    directionsService.route(request, function(result, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsRenderer.setDirections(result);
            document.getElementById('btnRestablecer').style.display = 'block';

            // Resaltar item
            document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
            if(el) el.classList.add('active');
        } else {
            console.error('Error trazando ruta: ' + status);
            alert('No se pudo encontrar una ruta conduciendo válida en Google Maps para estos puntos.');
        }
    });
}

function restablecerMapa() {
    directionsRenderer.setDirections({routes: []}); // Limpia la ruta
    map.setCenter({ lat: ciudadLat, lng: ciudadLng });
    map.setZoom(13);
    document.getElementById('btnRestablecer').style.display = 'none';
    document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
}

function centrarMapaMarcador(idBarrio) {
    if(markers[idBarrio]) {
        map.setCenter({ lat: markers[idBarrio].lat, lng: markers[idBarrio].lng });
        map.setZoom(16);
        
        // Simular click para abrir infoWindow (Google maps no tiene una función .openPopup() automática como Leaflet sin instanciar infoWindow acá)
        google.maps.event.trigger(markers[idBarrio].marker, 'click');
        
        // Resaltar item
        document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }
}

function filtrarSidebar(query) {
    const items = document.querySelectorAll('.sidebar-item');
    const q = query.toLowerCase();
    items.forEach(item => {
        const match = item.dataset.nombre.includes(q);
        item.style.display = match ? '' : 'none';
    });
}
</script>
@endpush
