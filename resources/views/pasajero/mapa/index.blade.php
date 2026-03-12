@extends('pasajero.layouts.app')
@section('title', 'Mapa de paradas')

@section('content')
<div class="container-fluid py-3 px-4">

    <div class="pas-header mb-3">
        <div>
            <h1><span class="material-symbols-rounded">map</span> Mapa de paradas</h1>
            <p>Barrios de origen y destino de las rutas disponibles en tu ciudad.</p>
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

        {{-- ── Sidebar de barrios/paradas ─────────────────── --}}
        <div class="mapa-sidebar">

            {{-- Buscador --}}
            <div class="pas-card" style="flex-shrink:0">
                <div class="pas-card-body" style="padding:.75rem 1rem">
                    <input type="text" id="buscarBarrio"
                           class="form-control form-control-sm"
                           placeholder="Buscar barrio..."
                           oninput="filtrarBarrios(this.value)"
                           style="border-color:var(--border);border-radius:var(--r-sm)">
                </div>
            </div>

            {{-- Lista de barrios --}}
            <div class="pas-card" style="flex:1;overflow:hidden;display:flex;flex-direction:column">
                <div class="pas-card-head" style="flex-shrink:0">
                    <h3>
                        <span class="material-symbols-rounded">location_on</span>
                        Paradas ({{ $barrios->count() }})
                    </h3>
                </div>
                <div class="mapa-sidebar-inner" id="listaBarrios">
                    @forelse($barrios as $barrio)
                    <div class="mapa-parada-item barrio-item"
                         data-nombre="{{ strtolower($barrio->nombre) }}"
                         onclick="centrarMapa({{ $loop->index }}, '{{ $barrio->nombre }}')">
                        <div class="mapa-parada-dot"></div>
                        <div>
                            <div class="mapa-parada-nombre">{{ $barrio->nombre }}</div>
                            <div class="mapa-parada-meta">
                                {{ $rutas->where('id_barrio_origen', $barrio->id_barrio)->count() +
                                   $rutas->where('id_barrio_destino', $barrio->id_barrio)->count() }} ruta(s) pasan por aquí
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="pas-empty">
                        <span class="material-symbols-rounded">location_off</span>
                        <p>No hay barrios registrados para tu ciudad.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Leyenda --}}
            <div class="pas-card" style="flex-shrink:0">
                <div class="pas-card-body" style="padding:.75rem 1rem">
                    <div class="mapa-leyenda">
                        <div class="mapa-leyenda-item">
                            <div class="mapa-leyenda-dot" style="background:var(--pas)"></div>
                            Parada de ruta
                        </div>
                        <div class="mapa-leyenda-item">
                            <div class="mapa-leyenda-dot" style="background:var(--ok)"></div>
                            Origen
                        </div>
                        <div class="mapa-leyenda-item">
                            <div class="mapa-leyenda-dot" style="background:var(--err)"></div>
                            Destino
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Mapa (Leaflet) ───────────────────────────────── --}}
        <div class="mapa-container">
            <div id="map"></div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .sigu-main { padding-bottom: 0 !important; }
    .leaflet-popup-content-wrapper { border-radius: var(--r-md) !important; font-family: 'Inter Tight', sans-serif; font-size: .84rem; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Datos desde el servidor ────────────────────────────────────
const barrios = @json($barrios->map(fn($b) => [
    'id'     => $b->id_barrio,
    'nombre' => $b->nombre,
    'lat'    => $b->latitud  ?? null,
    'lng'    => $b->longitud ?? null,
]));

const rutas = @json($rutas->map(fn($r) => [
    'codigo'  => $r->codigo_ruta,
    'origen'  => $r->id_barrio_origen,
    'destino' => $r->id_barrio_destino,
]));

// Ciudad: coordenadas aproximadas (Colombia por defecto si no están en BD)
const ciudadLat = {{ $ciudadObj->latitud  ?? 4.5709 }};
const ciudadLng = {{ $ciudadObj->longitud ?? -74.2973 }};

// ── Inicializar mapa ───────────────────────────────────────────
const map = L.map('map').setView([ciudadLat, ciudadLng], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

// ── Iconos personalizados ──────────────────────────────────────
function crearIcono(color) {
    return L.divIcon({
        html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};
                           border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>`,
        className: '',
        iconSize: [14, 14],
        iconAnchor: [7, 7],
        popupAnchor: [0, -8],
    });
}

// ── Colocar marcadores ─────────────────────────────────────────
const marcadores = [];

// Calcular qué barrios son origen, destino o ambos
const origenes  = new Set(rutas.map(r => r.origen));
const destinos  = new Set(rutas.map(r => r.destino));

// Offset para barrios sin coordenadas (distribución en círculo alrededor del centro)
barrios.forEach((b, i) => {
    let lat = b.lat;
    let lng = b.lng;

    if (!lat || !lng) {
        // Distribuir en cuadrícula si no hay coords
        const fila = Math.floor(i / 5);
        const col  = i % 5;
        lat = ciudadLat + (fila - 2) * 0.005;
        lng = ciudadLng + (col - 2) * 0.005;
    }

    const esOrigen  = origenes.has(b.id);
    const esDestino = destinos.has(b.id);
    const color = esOrigen && esDestino ? '#2563EB'
                : esOrigen  ? '#22C55E'
                : esDestino ? '#EF4444'
                : '#6B7280';

    const rutasDelBarrio = rutas.filter(r => r.origen === b.id || r.destino === b.id);
    const rutasHtml = rutasDelBarrio.map(r => `<div style="margin-top:.2rem">
        <span style="background:#EFF6FF;color:#2563EB;border-radius:4px;padding:1px 6px;font-size:.72rem;font-weight:600">
            Ruta #${r.codigo}
        </span>
    </div>`).join('');

    const marker = L.marker([lat, lng], { icon: crearIcono(color) })
        .addTo(map)
        .bindPopup(`
            <div>
                <strong style="font-family:'Sora',sans-serif">${b.nombre}</strong>
                ${rutasHtml || '<div style="color:#9CA3AF;font-size:.78rem;margin-top:.25rem">Sin rutas asignadas</div>'}
            </div>
        `);

    marcadores.push({ barrio: b, marker, lat, lng });
});

// ── Funciones de interacción ───────────────────────────────────
function centrarMapa(index, nombre) {
    const entry = marcadores[index];
    if (!entry) return;

    map.setView([entry.lat, entry.lng], 15, { animate: true });
    entry.marker.openPopup();

    // Resaltar ítem en la lista
    document.querySelectorAll('.mapa-parada-item').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.mapa-parada-item')[index]?.classList.add('active');
}

function filtrarBarrios(query) {
    const items = document.querySelectorAll('.barrio-item');
    const q = query.toLowerCase();
    items.forEach(item => {
        const match = item.dataset.nombre.includes(q);
        item.style.display = match ? '' : 'none';
    });
}
</script>
@endpush
