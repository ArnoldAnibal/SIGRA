<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN']);
$SIGRA_USER = sigra_current_user();
$SIGRA_CONFIG = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Configuración del Sistema</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .cfg-body  { padding: 24px; overflow-y: auto; height: calc(100vh - 64px - 73px); }
    .cfg-inner { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 220px 1fr; gap: 24px; }
    @media (max-width: 760px) { .cfg-inner { grid-template-columns: 1fr; } }

    .cfg-nav {
      background: #fff; border-radius: var(--r-lg); border: 1px solid var(--border-lt);
      padding: 8px; align-self: flex-start; height: fit-content;
    }
    .cfg-nav-btn {
      display: flex; align-items: center; gap: 10px;
      width: 100%; padding: 10px 14px;
      background: none; border: none; border-radius: 8px;
      font-size: 13.5px; font-weight: 500; color: #4B5563;
      cursor: pointer; text-align: left;
      transition: all .12s;
    }
    .cfg-nav-btn:hover { background: #F4F6FA; color: var(--navy); }
    .cfg-nav-btn.active { background: var(--navy); color: #fff; }
    .cfg-nav-btn i { font-size: 16px; }

    .cfg-section {
      background: #fff; border-radius: var(--r-lg); border: 1px solid var(--border-lt);
      padding: 22px;
    }
    .cfg-section h3 {
      font-size: 16px; font-weight: 700; color: var(--navy);
      margin-bottom: 4px;
    }
    .cfg-section .desc { font-size: 13px; color: #6B7280; margin-bottom: 18px; }

    .cfg-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 560px) { .cfg-grid-2 { grid-template-columns: 1fr; } }

    .switch-row {
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 0; border-bottom: 1px solid var(--border-lt);
    }
    .switch-row:last-child { border-bottom: none; }
    .switch-row-text { font-size: 13.5px; color: #1F2937; font-weight: 500; }
    .switch-row-sub  { font-size: 12px; color: #9CA3AF; margin-top: 2px; }

    .switch { position: relative; width: 42px; height: 24px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .switch-slider {
      position: absolute; cursor: pointer; inset: 0;
      background: #CBD5E1; border-radius: 999px; transition: .2s;
    }
    .switch-slider::before {
      content: ''; position: absolute; height: 18px; width: 18px;
      left: 3px; top: 3px; background: #fff; border-radius: 50%;
      transition: .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
    }
    .switch input:checked + .switch-slider { background: var(--amber); }
    .switch input:checked + .switch-slider::before { transform: translateX(18px); }

    .danger-zone {
      border: 1px dashed #FCA5A5; background: #FEF2F2;
      border-radius: var(--r-lg); padding: 18px;
    }

    .save-bar {
      position: sticky; bottom: 0; background: #fff;
      padding: 14px 0; border-top: 1px solid var(--border-lt);
      display: flex; justify-content: flex-end; gap: 10px;
      margin-top: 18px;
    }

    .lock-banner {
      background: #FFFBEB; border: 1px solid #FCD34D;
      padding: 14px 18px; border-radius: var(--r-lg);
      display: flex; align-items: center; gap: 12px;
      color: #92400E; font-size: 13.5px; margin-bottom: 18px;
    }
  </style>
</head>
<body>
  <div class="app">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar"></aside>

    <div class="main-wrap" id="mainWrap">
      <header class="topbar" id="topbar"></header>

      <main class="page-content" style="display:flex;flex-direction:column">
        <div class="page-header">
          <div class="page-title">Configuración del Sistema</div>
          <div class="page-sub">Datos del restaurante, impuestos y preferencias</div>
        </div>

        <div class="cfg-body">
          <div id="lockBanner" class="lock-banner" style="display:none">
            <i class="bi bi-lock"></i>
            <span>Solo los administradores pueden modificar la configuración. Estás en modo solo lectura.</span>
          </div>

          <div class="cfg-inner">
            <!-- Navegación -->
            <nav class="cfg-nav">
              <button class="cfg-nav-btn active" data-section="rest" onclick="setSection('rest')">
                <i class="bi bi-shop"></i> Restaurante
              </button>
              <button class="cfg-nav-btn" data-section="impuestos" onclick="setSection('impuestos')">
                <i class="bi bi-percent"></i> Impuestos
              </button>
              <button class="cfg-nav-btn" data-section="fel" onclick="setSection('fel')">
                <i class="bi bi-receipt"></i> Facturación FEL
              </button>
              <button class="cfg-nav-btn" data-section="notif" onclick="setSection('notif')">
                <i class="bi bi-bell"></i> Notificaciones
              </button>
              <button class="cfg-nav-btn" data-section="datos" onclick="setSection('datos')">
                <i class="bi bi-database"></i> Datos
              </button>
            </nav>

            <!-- Contenido -->
            <div id="cfgContent"></div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast toast-success" id="successToast">
    <i class="bi bi-check-circle" style="font-size:18px"></i>
    <div>
      <div class="toast-title" id="toastTitle">Listo</div>
      <div class="toast-sub" id="toastSub"></div>
    </div>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'config' => $SIGRA_CONFIG
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script>
    SIGRA.initLayout('Configuración del Sistema');
    const user = SIGRA.getUser();
    const isAdmin = user && user.role === 'ADMIN';
    if (!isAdmin) document.getElementById('lockBanner').style.display = 'flex';

    let cfg = SIGRA_DATA.getConfig();
    let currentSection = 'rest';

    function setSection(s) {
      currentSection = s;
      document.querySelectorAll('.cfg-nav-btn').forEach(b => b.classList.toggle('active', b.dataset.section === s));
      render();
    }

    function render() {
      const dis = !isAdmin ? 'disabled' : '';
      let html = '';

      if (currentSection === 'rest') {
        html = `
          <div class="cfg-section">
            <h3>Datos del Restaurante</h3>
            <p class="desc">Estos datos aparecen en facturas, planillas y reportes generados.</p>
            <div class="form-group">
              <label class="form-label">Nombre comercial</label>
              <input id="rNombre" class="form-control" value="${cfg.restaurante.nombre || ''}" ${dis}>
            </div>
            <div class="cfg-grid-2">
              <div class="form-group">
                <label class="form-label">NIT</label>
                <input id="rNit" class="form-control" value="${cfg.restaurante.nit || ''}" ${dis}>
              </div>
              <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input id="rTel" class="form-control" value="${cfg.restaurante.telefono || ''}" ${dis}>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Dirección</label>
              <input id="rDir" class="form-control" value="${cfg.restaurante.direccion || ''}" ${dis}>
            </div>
            <div class="form-group">
              <label class="form-label">Eslogan</label>
              <input id="rEslogan" class="form-control" value="${cfg.restaurante.eslogan || ''}" ${dis}>
            </div>
            ${isAdmin ? saveBar('rest') : ''}
          </div>`;
      }

      if (currentSection === 'impuestos') {
        html = `
          <div class="cfg-section">
            <h3>Impuestos y Moneda</h3>
            <p class="desc">Configura las tasas que se aplican al emitir facturas.</p>
            <div class="cfg-grid-2">
              <div class="form-group">
                <label class="form-label">IVA (%)</label>
                <input id="iIva" type="number" min="0" max="100" step="0.01" class="form-control" value="${cfg.impuestos.iva}" ${dis}>
              </div>
              <div class="form-group">
                <label class="form-label">Símbolo de moneda</label>
                <input id="iMoneda" class="form-control" maxlength="3" value="${cfg.impuestos.moneda}" ${dis}>
              </div>
            </div>
            ${isAdmin ? saveBar('impuestos') : ''}
          </div>`;
      }

      if (currentSection === 'fel') {
        html = `
          <div class="cfg-section">
            <h3>Facturación Electrónica (FEL)</h3>
            <p class="desc">Comportamiento por defecto de la emisión de facturas SAT.</p>
            <div class="form-group">
              <label class="form-label">Establecimiento</label>
              <input id="fEstab" class="form-control" value="${cfg.fel.establecimiento || ''}" ${dis}>
            </div>
            <div class="switch-row">
              <div>
                <div class="switch-row-text">Modo Online por defecto</div>
                <div class="switch-row-sub">Si está desactivado, las facturas inician en modo contingencia.</div>
              </div>
              <label class="switch">
                <input id="fOnline" type="checkbox" ${cfg.fel.online ? 'checked' : ''} ${dis}>
                <span class="switch-slider"></span>
              </label>
            </div>
            ${isAdmin ? saveBar('fel') : ''}
          </div>`;
      }

      if (currentSection === 'notif') {
        html = `
          <div class="cfg-section">
            <h3>Notificaciones</h3>
            <p class="desc">Alertas que recibirá el personal del sistema.</p>
            <div class="switch-row">
              <div>
                <div class="switch-row-text">Sonido al recibir pedido en cocina</div>
                <div class="switch-row-sub">Reproduce un tono breve cuando entra un pedido nuevo.</div>
              </div>
              <label class="switch">
                <input id="nSonido" type="checkbox" ${cfg.notificaciones.sonidoCocina ? 'checked' : ''} ${dis}>
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="switch-row">
              <div>
                <div class="switch-row-text">Alertas de cuentas vencidas</div>
                <div class="switch-row-sub">Muestra banner cuando hay cuentas por pagar atrasadas.</div>
              </div>
              <label class="switch">
                <input id="nAlertas" type="checkbox" ${cfg.notificaciones.alertasVencimiento ? 'checked' : ''} ${dis}>
                <span class="switch-slider"></span>
              </label>
            </div>
            ${isAdmin ? saveBar('notif') : ''}
          </div>`;
      }

      if (currentSection === 'datos') {
        html = `
          <div class="cfg-section">
            <h3>Datos del Sistema</h3>
            <p class="desc">Toda la información del sistema se guarda localmente en este navegador.</p>

            <div class="cfg-grid-2" style="margin-bottom:18px">
              <button class="btn btn-outline" onclick="exportarDatos()">
                <i class="bi bi-download"></i> Exportar respaldo (.json)
              </button>
              <label class="btn btn-outline" style="cursor:pointer;text-align:center">
                <i class="bi bi-upload"></i> Importar respaldo
                <input type="file" accept="application/json" style="display:none" onchange="importarDatos(event)">
              </label>
            </div>

            ${isAdmin ? `
            <div class="danger-zone">
              <h4 style="font-size:14px;font-weight:700;color:#B91C1C;margin-bottom:6px">Zona de peligro</h4>
              <p style="font-size:12.5px;color:#7F1D1D;margin-bottom:12px">
                Restablecer borrará todas las mesas, pedidos, facturas, empresas, abonos, proveedores y planillas. Esta acción no se puede deshacer.
              </p>
              <button class="btn btn-danger" onclick="resetAll()">
                <i class="bi bi-trash"></i> Restablecer todos los datos
              </button>
            </div>` : ''}
          </div>`;
      }

      document.getElementById('cfgContent').innerHTML = html;
    }

    function saveBar(section) {
      return `<div class="save-bar">
        <button class="btn btn-outline" onclick="render()">Descartar</button>
        <button class="btn btn-primary" onclick="guardar('${section}')">
          <i class="bi bi-check2"></i> Guardar cambios
        </button>
      </div>`;
    }

    function guardar(section) {
      if (!isAdmin) return;
      if (section === 'rest') {
        cfg.restaurante = {
          nombre:    document.getElementById('rNombre').value.trim(),
          nit:       document.getElementById('rNit').value.trim(),
          telefono:  document.getElementById('rTel').value.trim(),
          direccion: document.getElementById('rDir').value.trim(),
          eslogan:   document.getElementById('rEslogan').value.trim(),
        };
      } else if (section === 'impuestos') {
        cfg.impuestos = {
          iva:    Number(document.getElementById('iIva').value),
          moneda: document.getElementById('iMoneda').value.trim() || 'Q',
        };
      } else if (section === 'fel') {
        cfg.fel = {
          online: document.getElementById('fOnline').checked,
          establecimiento: document.getElementById('fEstab').value.trim(),
        };
      } else if (section === 'notif') {
        cfg.notificaciones = {
          sonidoCocina:      document.getElementById('nSonido').checked,
          alertasVencimiento: document.getElementById('nAlertas').checked,
        };
      }
      SIGRA_DATA.setConfig(cfg);
      showToast('Cambios guardados', `Sección ${section} actualizada`);
    }

    /* ── Backup ── */
    function exportarDatos() {
      const data = {};
      Object.entries(SIGRA_DATA.KEYS).forEach(([k, v]) => {
        const raw = localStorage.getItem(v);
        if (raw) data[k] = JSON.parse(raw);
      });
      const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `sigra-backup-${new Date().toISOString().slice(0,10)}.json`;
      a.click();
      URL.revokeObjectURL(url);
      showToast('Respaldo descargado', a.download);
    }

    function importarDatos(ev) {
      const file = ev.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        try {
          const data = JSON.parse(e.target.result);
          Object.entries(data).forEach(([k, val]) => {
            const key = SIGRA_DATA.KEYS[k];
            if (key) localStorage.setItem(key, JSON.stringify(val));
          });
          cfg = SIGRA_DATA.getConfig();
          render();
          showToast('Datos importados', 'Recarga otras pestañas para ver los cambios');
        } catch (err) {
          alert('Archivo inválido: ' + err.message);
        }
      };
      reader.readAsText(file);
    }

    function resetAll() {
      if (!confirm('¿Restablecer TODOS los datos? Esto borrará mesas, pedidos, facturas, empresas, proveedores, abonos y planillas.\n\nLa configuración del restaurante se mantiene.')) return;
      const conf = SIGRA_DATA.getConfig();
      SIGRA_DATA.resetAll();
      SIGRA_DATA.setConfig(conf);
      showToast('Datos restablecidos', 'Recarga la página para ver los seeds');
    }

    function showToast(title, sub) {
      document.getElementById('toastTitle').textContent = title;
      document.getElementById('toastSub').textContent = sub || '';
      SIGRA.showToast('successToast', 2500);
    }

    render();
  </script>
</body>
</html>
