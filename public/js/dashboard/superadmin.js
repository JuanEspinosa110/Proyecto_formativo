let charts = {};

async function cargarDashboard(url) {
    fetch(url)
        .then(res => res.json())
        .then(data => {

            crearGrafica('chartUsuarios', 'Usuarios', data.usuario);
            crearGrafica('chartEmpresas', 'Empresas', data.empresa);
            crearGrafica('chartTarjetas', 'Tarjetas', data.tarjeta);

            crearGraficaDocumentos(data.documentos);


        })
        .catch(err => console.error('Dashboard error:', err));
}

function crearGrafica(id, label, value) {
    const canvas = document.getElementById(id);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    if (charts[id]) {
        charts[id].data.datasets[0].data = [Number(value)];
        charts[id].update();
        return;
    }

    charts[id] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [label],
            datasets: [{
                data: [Number(value)],
                backgroundColor: '#2563eb'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
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

