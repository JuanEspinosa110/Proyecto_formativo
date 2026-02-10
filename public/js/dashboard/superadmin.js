let charts = {};

async function cargarDashboard(url) {
    fetch(url)
        .then(res => res.json())
        .then(data => {

            crearGraficaEstado('chartUsuarios', 'Usuarios', data.usuarios);
            crearGraficaEstado('chartEmpresas', 'Empresas', data.empresas);
            crearGraficaEstado('chartTarjetas', 'Tarjetas', data.tarjetas);

            crearGraficaDocumentos(data.documentos);

        })
        .catch(err => console.error('Dashboard error:', err));
}

function crearGraficaEstado(id, label, dataEstados) {

    const canvas = document.getElementById(id);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    const datos = [
        Number(dataEstados.activos),
        Number(dataEstados.inactivos)
    ];

    if (charts[id]) {
        charts[id].data.datasets[0].data = datos;
        charts[id].update();
        return;
    }

    charts[id] = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Activos', 'Inactivos'],
            datasets: [{
                data: datos,
                backgroundColor: [
                    '#16a34a',  // verde activo
                    '#dc2626'   // rojo inactivo
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}


function crearGraficaDocumentos(data) {
    const canvas = document.getElementById('chartDocumentos');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    if (charts['chartDocumentos']) {
        charts['chartDocumentos'].data.datasets[0].data = [
            data.activos,
            data.por_vencer,
            data.vencidos
        ];
        charts['chartDocumentos'].update();
        return;
    }

    charts['chartDocumentos'] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Activos', 'Por vencer', 'Vencidos'],
            datasets: [{
                data: [
                    data.activos,
                    data.por_vencer,
                    data.vencidos
                ],
                backgroundColor: [
                    '#16a34a', // verde
                    '#facc15', // amarillo
                    '#dc2626'  // rojo
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

