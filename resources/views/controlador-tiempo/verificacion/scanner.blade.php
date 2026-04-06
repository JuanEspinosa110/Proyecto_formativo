@extends('controlador-tiempo.layouts.app')

@section('title', 'Scanner de Bus — Controlador')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd">
        <h1 class="sigu-page-title text-primary">Escaneo de Bus</h1>
        <p class="sigu-page-sub">Use la cámara de su dispositivo para leer el código QR que le presente el conductor.</p>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8 col-lg-6">
            <div class="bg-white rounded-4 shadow-sm p-4 text-center">
                
                <div id="reader" style="width: 100%; min-height: 350px; background: #f8f9fa; border-radius: 1rem; overflow: hidden; border: 2px dashed #dee2e6;">
                </div>

                <div id="scanner-status" class="mt-4 p-3 bg-light rounded-3 d-flex align-items-center justify-content-center gap-2">
                    <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                    <span class="small fw-bold text-muted">Buscando cámara...</span>
                </div>

                <div class="mt-4 alert alert-info border-0 rounded-4 text-start">
                    <div class="d-flex gap-3 align-items-center">
                        <span class="material-symbols-rounded fs-1 text-primary">info</span>
                        <div>
                            <p class="mb-0 small fw-medium">
                                El lector identificará automáticamente el ID del recorrido y lo redirigirá a la ficha técnica del bus en ruta.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Redirigir a la vista de verificación con el ID escaneado
        // Usamos url() de Blade para asegurar que incluya el subdirectorio si existe (ej. /clickpasajes)
        const baseUrl = "{{ url('/controlador-tiempo/verificacion') }}";
        window.location.href = `${baseUrl}/${decodedText}`;
        html5QrcodeScanner.clear();
    }

    function onScanFailure(error) {
        // Ignorar errores menores de escaneo
    }

    // Traducción manual de etiquetas
    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", 
        { 
            fps: 10, 
            qrbox: {width: 250, height: 250},
            rememberLastUsedCamera: true,
            aspectRatio: 1.0
        },
        /* verbose= */ false
    );
    
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    // Traducción dinámica con MutationObserver (Solución robusta para elementos asíncronos)
    const translateUI = () => {
        const translations = {
            "Request Camera Permissions": "Solicitar Permiso de Cámara",
            "Scan an Image File": "Escanear un Archivo de Imagen",
            "Scan using camera directly": "Escanear usando Cámara Directamente",
            "Choose Image": "Elegir Imagen",
            "No image choosen": "Ninguna seleccionada",
            "Or drop an image to scan": "O arrastre una imagen para escanear",
            "Select Camera": "Seleccionar Cámara",
            "Start Scanning": "Iniciar Escaneo",
            "Stop Scanning": "Detener Escaneo",
            "Camera scan": "Escaneo por Cámara",
            "File scan": "Escaneo por Archivo"
        };

        // Traducir Botones
        document.querySelectorAll('#reader button').forEach(btn => {
            Object.keys(translations).forEach(key => {
                if (btn.innerText.includes(key)) {
                    btn.innerText = btn.innerText.replace(key, translations[key]);
                }
            });
        });

        // Traducir Enlaces (Anchors)
        document.querySelectorAll('#reader a').forEach(a => {
            Object.keys(translations).forEach(key => {
                if (a.innerText.includes(key)) {
                    a.innerText = a.innerText.replace(key, translations[key]);
                }
            });
        });

        // Traducir spans y divs específicos
        document.querySelectorAll('#reader span, #reader div').forEach(el => {
            if (el.children.length === 0) { // Solo si es texto directo
                Object.keys(translations).forEach(key => {
                    if (el.innerText === key) {
                        el.innerText = translations[key];
                    }
                });
            }
        });

        // Traducir placeholder de cámara
        const selectCamera = document.getElementById('html5-qrcode-select-camera');
        if(selectCamera && selectCamera.options[0] && selectCamera.options[0].text.includes("Select Camera")) {
            selectCamera.options[0].text = "Seleccione una cámara";
        }
    };

    // Observar cambios en el div del lector
    const observer = new MutationObserver((mutations) => {
        translateUI();
    });

    observer.observe(document.getElementById('reader'), {
        childList: true,
        subtree: true
    });

    document.addEventListener('DOMContentLoaded', () => {
        translateUI(); // Ejecución inicial
        setTimeout(() => {
            const status = document.getElementById('scanner-status');
            status.innerHTML = '<span class="text-success">●</span> <span class="small fw-bold text-muted">Escáner Activo</span>';
        }, 1000);
    });
</script>
@endpush
