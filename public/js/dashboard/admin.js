(function(){
  if (typeof window === 'undefined') return;
  const STATS_URL = window.ADMIN_STATS_URL || '/admin/dashboard/stats';

  // Chart instances
  let donutChart = null;
  const sparkCharts = [];

  function numberFormat(n){ return (n===null||n===undefined)?'—':String(n); }

  function updateKPIs(data){
    const empresaName = data.empresa?.nombre ?? (data.empresa?.nombre_empresa ?? '—');
    const totales = data.totales || {};

    const elEmpresa = document.getElementById('kpiEmpresa');
    const elUsuarios = document.getElementById('kpiUsuarios');
    const elDocumentos = document.getElementById('kpiDocumentos');
    if (elEmpresa) elEmpresa.textContent = empresaName;
    if (elUsuarios) elUsuarios.textContent = numberFormat(totales.usuarios ?? 0);
    if (elDocumentos) elDocumentos.textContent = numberFormat(totales.documentos ?? 0);

    // Trends — preferimos mostrar porcentajes reales; si previous == 0 and current > 0
    // el backend devuelve pct = null. En ese caso mostramos el cambio absoluto.
    const trends = data.trends || {};
    const cards = Array.from(document.querySelectorAll('.sa-kpi-card'));
    if (cards.length >= 3){
      // usuarios trend: backend may not provide series, so we use trends.usuarios
      const usuarioTrend = trends.usuarios || {};
      const docTrend = trends.documentos || {};

      const userTrendEl = cards[1].querySelector('.kpi-trend');
      const docTrendEl = cards[2].querySelector('.kpi-trend');

      if (userTrendEl){
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

      if (docTrendEl){
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

  function getSeriesArray(series, key){
    return series.map(s => Number(s[key] || 0));
  }

  function renderDonut(data){
    const ctx = document.getElementById('chartUsersDocs');
    if (!ctx) return;
    const totals = data.totales || {};
    const users = totals.usuarios || 0;
    const docs = totals.documentos || 0;

    const chartData = [users, docs];

    if (!donutChart){
      donutChart = new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: ['Usuarios','Documentos'],
          datasets: [{ data: chartData, backgroundColor: ['#6A4CC5','#9B84E3'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
      });
    } else {
      donutChart.data.datasets[0].data = chartData;
      donutChart.update();
    }
  }

  function ensureSparkCanvases(){
    const svgs = Array.from(document.querySelectorAll('.kpi-sparkline'));
    svgs.forEach((svg, idx) => {
      // if we already created a canvas, skip
      if (svg.dataset.replaced === '1') return;
      const canvas = document.createElement('canvas');
      canvas.width = 100; canvas.height = 28; canvas.className = 'kpi-spark-canvas';
      svg.parentNode.replaceChild(canvas, svg);
      svg.dataset.replaced = '1';
    });
  }

  function renderSparklines(data){
    const series = data.series || [];
    // arrays
    const usuarios = getSeriesArray(series, 'usuarios');
    const documentos = getSeriesArray(series, 'documentos');

    ensureSparkCanvases();
    const canvases = Array.from(document.querySelectorAll('.kpi-spark-canvas'));
    // Expecting order: empresa(0), usuarios(1), documentos(2) — we will map usuarios/docs to canvases 0/1/2 possibly
    if (canvases.length === 0) return;

    // Map: if there are 3 canvases, use 0->empresa (usuarios series), 1->usuarios (usuarios series), 2->documentos
    const map = [];
    if (canvases.length >= 3){
      map.push({canvas: canvases[0], data: usuarios, color: '#9B84E3'});
      map.push({canvas: canvases[1], data: usuarios, color: '#6A4CC5'});
      map.push({canvas: canvases[2], data: documentos, color: '#ff6b6b'});
    } else {
      // Fallback: fill first with usuarios, second with documentos
      map.push({canvas: canvases[0], data: usuarios, color: '#6A4CC5'});
      if (canvases[1]) map.push({canvas: canvases[1], data: documentos, color: '#9B84E3'});
    }

    map.forEach((m, i) => {
      const ctx = m.canvas.getContext('2d');
      // create or update Chart in sparkCharts
      if (!sparkCharts[i]){
        sparkCharts[i] = new Chart(ctx, {
          type: 'line',
          data: { labels: series.map(s => s.day), datasets: [{ data: m.data, borderColor: m.color, backgroundColor: 'rgba(0,0,0,0)', tension: 0.3, pointRadius: 0 }] },
          options: { responsive: false, maintainAspectRatio: false, plugins: { legend:{display:false} }, scales: { x:{ display:false }, y:{ display:false } }, elements: { line: { borderWidth: 2 } } }
        });
      } else {
        sparkCharts[i].data.labels = series.map(s => s.day);
        sparkCharts[i].data.datasets[0].data = m.data;
        sparkCharts[i].update();
      }
    });
  }

  async function fetchStats(){
    try{
      const res = await fetch(STATS_URL, { credentials: 'same-origin' });
      if (!res.ok) throw new Error('HTTP '+res.status);
      const data = await res.json();
      // Exponer para depuración y futuras visualizaciones
      window.ADMIN_STATS = data;
      console.debug('ADMIN_STATS', data);
      updateKPIs(data);
      renderDonut(data);
      renderSparklines(data);
    }catch(err){
      console.error('Error fetching admin stats', err);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    fetchStats();
    // Polling cada 15s — cambiar a websocket si se desea push
    setInterval(fetchStats, 15000);
  });
})();
