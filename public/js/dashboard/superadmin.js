let charts = {};

async function cargarDashboard(url) {
    fetch(url)
        .then(res => res.json())
        .then(data => {

            // ================= KPIs =================

            // Total empresas
            const totalEmpresas = data.empresas_estado
                .reduce((acc, e) => acc + Number(e.total), 0);

            document.getElementById('kpiEmpresas').textContent = totalEmpresas;


            // Total licencias
            const totalLicencias =
                Number(data.licencias_estado.activas) +
                Number(data.licencias_estado.por_vencer) +
                Number(data.licencias_estado.vencidas);

            document.getElementById('kpiLicencias').textContent = totalLicencias;


            // Planes activos (cantidad de planes que tienen al menos 1 licencia)
            document.getElementById('kpiPlanes').textContent =
                data.planes_populares.length;

            console.log('Licencias estado:', data.licencias_estado);



            crearLineEmpresas('chartEmpresasMensual', data.empresas_crecimiento);
            crearBarEmpresasEstado('chartEmpresasEstado', data.empresas_estado);
            crearBarLicenciasEstado('chartLicenciasEstado', data.licencias_estado);
            crearLineLicencias('chartLicenciasMensual', data.licencias_mensual);
            crearBarPlanes('chartPlanes', data.planes_populares);

        })
        .catch(err => console.error('Dashboard error:', err));
}

function crearLineEmpresas(id, data) {

    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;

    const labels = data.map(e => e.mes);
    const valores = data.map(e => e.total);

    if (charts[id]) {
        charts[id].data.labels = labels;
        charts[id].data.datasets[0].data = valores;
        charts[id].update();
        return;
    }

    charts[id] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Empresas registradas',
                data: valores,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
}

function crearBarEmpresasEstado(id, data) {

    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;

    const labels = data.map(e => e.nombre_estado);
    const valores = data.map(e => e.total);

    if (charts[id]) {
        charts[id].data.labels = labels;
        charts[id].data.datasets[0].data = valores;
        charts[id].update();
        return;
    }

    charts[id] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Empresas',
                data: valores,
                backgroundColor: '#0ea5e9'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
}

function crearBarLicenciasEstado(id, data) {

    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;

    const activas = Number(data?.activas ?? 0);
    const porVencer = Number(data?.por_vencer ?? 0);
    const vencidas = Number(data?.vencidas ?? 0);

    const labels = ['Activas', 'Por vencer', 'Vencidas'];
    const valores = [activas, porVencer, vencidas];

    if (charts[id]) {
        charts[id].data.labels = labels;
        charts[id].data.datasets[0].data = valores;
        charts[id].update();
        return;
    }

    charts[id] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Licencias',
                data: valores,
                backgroundColor: [
                    '#16a34a',
                    '#facc15',
                    '#dc2626'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}


function crearLineLicencias(id, data) {

    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;

    const labels = data.map(e => e.mes);
    const valores = data.map(e => e.total);

    if (charts[id]) {
        charts[id].data.labels = labels;
        charts[id].data.datasets[0].data = valores;
        charts[id].update();
        return;
    }

    charts[id] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Licencias emitidas',
                data: valores,
                borderColor: '#9333ea',
                backgroundColor: 'rgba(147,51,234,0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
}

function crearBarPlanes(id, data) {

    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;

    const labels = data.map(e => e.nombre_plan);
    const valores = data.map(e => e.total);

    if (charts[id]) {
        charts[id].data.labels = labels;
        charts[id].data.datasets[0].data = valores;
        charts[id].update();
        return;
    }

    charts[id] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Licencias por plan',
                data: valores,
                backgroundColor: '#f97316'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
}
