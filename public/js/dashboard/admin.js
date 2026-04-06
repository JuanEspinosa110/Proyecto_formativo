(function () {
  if (typeof window === 'undefined') return;
  const STATS_URL = window.ADMIN_STATS_URL || '/admin/dashboard/stats';

  // Chart instances
  let donutChart = null;
  let busesChart = null;
  let viajesChart = null;
  const sparkCharts = [];

  function numberFormat(n) { return (n === null || n === undefined) ? '—' : String(n); }

  function updateKPIs(data) {
    const empresaName = data.empresa?.nombre ?? (data.empresa?.nombre_empresa ?? '—');
    const totales = data.totales || {};

    const elEmpresa = document.getElementById('kpiEmpresa');
    const elUsuarios = document.getElementById('kpiUsuarios');
    const elDocumentos = document.getElementById('kpiDocumentos');
    const elBuses = document.getElementById('kpiBuses');
    if (elEmpresa) elEmpresa.textContent = empresaName;
    if (elUsuarios) elUsuarios.textContent = numberFormat(totales.usuarios ?? 0);
    if (elDocumentos) elDocumentos.textContent = numberFormat(totales.documentos ?? 0);
    if (elBuses) elBuses.textContent = numberFormat(totales.buses ?? 0);

    // Trends — preferimos mostrar porcentajes reales; si previous == 0 and current > 0
    // el backend devuelve pct = null. En ese caso mostramos el cambio absoluto.
    const trends = data.trends || {};
    const cards = Array.from(document.querySelectorAll('.sa-kpi-card'));
    if (cards.length >= 3) {
      // usuarios trend: backend may not provide series, so we use trends.usuarios
      const usuarioTrend = trends.usuarios || {};
      const docTrend = trends.documentos || {};

      const userTrendEl = cards[1].querySelector('.kpi-trend');
      const docTrendEl = cards[2].querySelector('.kpi-trend');

      if (userTrendEl) {
        if (usuarioTrend.pct === null) {
          // previous was zero — show absolute current value instead of %
          userTrendEl.textContent = `+${usuarioTrend.current ?? 0}`;
          userTrendEl.classList.add('positive');
        } else {
          const v = usuarioTrend.pct ?? 0;
          userTrendEl.textContent = `${v > 0 ? '+' : ''}${v}%`;
          userTrendEl.classList.toggle('positive', v >= 0);
          userTrendEl.classList.toggle('negative', v < 0);
        }
      }

      if (docTrendEl) {
        if (docTrend.pct === null) {
          docTrendEl.textContent = `+${docTrend.current ?? 0}`;
          docTrendEl.classList.add('positive');
        } else {
          const v = docTrend.pct ?? 0;
          docTrendEl.textContent = `${v > 0 ? '+' : ''}${v}%`;
          docTrendEl.classList.toggle('positive', v >= 0);
          docTrendEl.classList.toggle('negative', v < 0);
        }
      }
    }
  }

  function getSeriesArray(series, key) {
    return series.map(s => Number(s[key] || 0));
  }

  function renderDonut(data) {
    const canvas = document.getElementById('chartUsersDocs');
    if (!canvas) return;
    const totals = data.totales || {};
    const chartData = [totals.usuarios || 0, totals.documentos || 0];

    // Limpieza agresiva de Chart.js
    const old = Chart.getChart(canvas);
    if (old) old.destroy();

    donutChart = new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: ['Usuarios', 'Documentos'],
        datasets: [{ data: chartData, backgroundColor: ['#6A4CC5', '#9B84E3'] }]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
  }

  function ensureSparkCanvases() {
    const svgs = document.querySelectorAll('.kpi-sparkline');
    svgs.forEach(svg => {
      if (svg.dataset.replaced === '1') return;
      const canvas = document.createElement('canvas');
      canvas.width = 100; canvas.height = 28; canvas.className = 'kpi-spark-canvas';
      svg.parentNode.replaceChild(canvas, svg);
      svg.dataset.replaced = '1';
    });
  }

  function renderSparklines(data) {
    const series = data.series || [];
    const usuarios = getSeriesArray(series, 'usuarios');
    const documentos = getSeriesArray(series, 'documentos');

    ensureSparkCanvases();
    const canvases = document.querySelectorAll('.kpi-spark-canvas');
    if (canvases.length === 0) return;

    const map = [];
    if (canvases.length >= 3) {
      map.push({ canvas: canvases[0], data: usuarios, color: '#9B84E3' });
      map.push({ canvas: canvases[1], data: usuarios, color: '#6A4CC5' });
      map.push({ canvas: canvases[2], data: documentos, color: '#ff6b6b' });
    } else {
      map.push({ canvas: canvases[0], data: usuarios, color: '#6A4CC5' });
      if (canvases[1]) map.push({ canvas: canvases[1], data: documentos, color: '#9B84E3' });
    }

    map.forEach((m, i) => {
      const old = Chart.getChart(m.canvas);
      if (old) old.destroy();

      sparkCharts[i] = new Chart(m.canvas.getContext('2d'), {
        type: 'line',
        data: { 
          labels: series.map(s => s.day), 
          datasets: [{ data: m.data, borderColor: m.color, backgroundColor: 'rgba(0,0,0,0)', tension: 0.3, pointRadius: 0 }] 
        },
        options: { 
          responsive: false, maintainAspectRatio: false, 
          plugins: { legend: { display: false } }, 
          scales: { x: { display: false }, y: { display: false } }, 
          elements: { line: { borderWidth: 2 } } 
        }
      });
    });
  }

  function renderBusesEstado(data) {
    const canvas = document.getElementById('chartBusesEstado');
    if (!canvas) return;
    const buses = data.buses || [];
    const estados = {};
    buses.forEach(bus => {
      const nombre = bus.estado?.nombre_estado || 'Desconocido';
      estados[nombre] = (estados[nombre] || 0) + 1;
    });

    const old = Chart.getChart(canvas);
    if (old) old.destroy();

    busesChart = new Chart(canvas, {
      type: 'bar',
      data: {
        labels: Object.keys(estados),
        datasets: [{
          label: 'Buses',
          data: Object.values(estados),
          backgroundColor: ['#6A4CC5', '#9B84E3', '#ff6b6b', '#4cc56a', '#e3b684']
        }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });
  }

  function renderViajesRuta(data) {
    const canvas = document.getElementById('chartViajesRuta');
    if (!canvas) return;
    const viajes = data.viajes || [];
    const rutas = {};
    viajes.forEach(v => {
      const r = v.ruta?.id_ruta || 'Desconocido';
      rutas[r] = (rutas[r] || 0) + 1;
    });

    const old = Chart.getChart(canvas);
    if (old) old.destroy();

    viajesChart = new Chart(canvas, {
      type: 'line',
      data: {
        labels: Object.keys(rutas),
        datasets: [{
          label: 'Viajes',
          data: Object.values(rutas),
          borderColor: '#6A4CC5',
          backgroundColor: 'rgba(106,76,197,0.1)',
          tension: 0.3
        }]
      },
      options: { responsive: true, plugins: { legend: { display: true } } }
    });
  }

  function renderUsuariosTable(data) {
    const usuarios = data.usuarios || [];
    const tbody = document.getElementById('dashboardUsuariosTable');
    if (!tbody) return;
    tbody.innerHTML = '';
    usuarios.forEach(u => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${u.doc_usuario}</td>
        <td>${u.primer_nombre} ${u.primer_apellido}</td>
        <td>${u.correo}</td>
        <td>${u.telefono}</td>
        <td>${u.nombre_tipo ?? ''}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  function renderDocumentosTable(data) {
    const documentos = data.documentos || [];
    const tbody = document.getElementById('dashboardDocumentosTable');
    if (!tbody) return;
    tbody.innerHTML = '';
    documentos.forEach(d => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${d.id_documento}</td>
        <td>${d.nombre}</td>
        <td>${d.doc_usuario}</td>
        <td>${d.fecha_expedicion ?? ''}</td>
        <td>${d.fecha_vencimiento ?? ''}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  async function fetchStats() {
    try {
      const res = await fetch(STATS_URL, { credentials: 'same-origin' });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      window.ADMIN_STATS = data;
      updateKPIs(data);
      renderUsuariosTable(data);
      renderDocumentosTable(data);
      renderDonut(data);
      renderBusesEstado(data);
      renderViajesRuta(data);
      renderSparklines(data);
    } catch (err) {
      console.error('Error fetching admin stats', err);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    fetchStats();
    // Polling cada 15s — cambiar a websocket si se desea push
    setInterval(fetchStats, 15000);
  });
})();
