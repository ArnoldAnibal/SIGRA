<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN', 'MESERO', 'COCINA', 'CAJERO']);
$SIGRA_USER = sigra_current_user();
$resA = api_asistencias(sigra_token()); $SIGRA_ASISTENCIA = $resA["data"]["data"] ?? $resA["data"] ?? [];
$SIGRA_PERMISOS = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Control de Asistencia</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .as-body { padding: 24px; overflow-y: auto; height: calc(100vh - 64px - 73px); }
    .as-inner { max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }
    .clock {
      font-size: 56px; font-weight: 900; color: var(--navy);
      font-variant-numeric: tabular-nums; letter-spacing: -1px;
    }
    .clock-date { color: #6B7280; font-size: 14px; margin-top: 4px; }
    .marca-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 18px; }
    @media (max-width: 720px) { .marca-grid { grid-template-columns: 1fr 1fr; } }
    .marca-btn {
      padding: 18px 14px; border: 2px solid var(--border-lt); border-radius: 14px;
      background: #fff; cursor: pointer; text-align: center; transition: all .15s;
    }
    .marca-btn:hover:not(:disabled) { border-color: var(--amber); transform: translateY(-2px); }
    .marca-btn:disabled { opacity: .5; cursor: not-allowed; }
    .marca-btn .ico {
      width: 44px; height: 44px; border-radius: 50%; margin: 0 auto 10px;
      display: flex; align-items: center; justify-content: center; font-size: 20px;
    }
    .marca-btn.entrada .ico { background: #E8F5E9; color: #2E7D32; }
    .marca-btn.salida  .ico { background: #FFEBEE; color: #C62828; }
    .marca-btn.almuerzo-ini .ico { background: #FFF8E1; color: #F57C00; }
    .marca-btn.almuerzo-fin .ico { background: #E3F2FD; color: #1565C0; }
    .marca-btn .lbl { font-size: 13px; font-weight: 700; color: #1F2937; }
    .marca-btn .hora { font-size: 11.5px; color: #9CA3AF; margin-top: 2px; }
    .marca-btn.done { border-color: #A5D6A7; background: #F0F9F1; }
    .marca-btn.done .lbl { color: #2E7D32; }
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
          <div class="page-header-row">
            <div>
              <div class="page-title">Control de Asistencia</div>
              <div class="page-sub" id="pageSub">Registra tu entrada, almuerzo y salida</div>
            </div>
            <button class="btn btn-outline" onclick="SIGRA.openModal('permisoModal')">
              <i class="bi bi-calendar-plus"></i> Solicitar permiso
            </button>
          </div>
        </div>

        <div class="as-body">
          <div class="as-inner">

            <!-- Reloj y marcaje -->
            <div class="card">
              <div class="card-body" style="text-align:center;padding:30px 20px">
                <div class="clock" id="clock">--:--:--</div>
                <div class="clock-date" id="clockDate">—</div>

                <div class="marca-grid">
                  <button class="marca-btn entrada" id="btnEntrada" onclick="marcar('ENTRADA')">
                    <div class="ico"><i class="bi bi-box-arrow-in-right"></i></div>
                    <div class="lbl">Entrada</div>
                    <div class="hora" id="horaEntrada">—</div>
                  </button>
                  <button class="marca-btn almuerzo-ini" id="btnAlmIni" onclick="marcar('ALMUERZO_INI')">
                    <div class="ico"><i class="bi bi-cup-hot"></i></div>
                    <div class="lbl">Inicio almuerzo</div>
                    <div class="hora" id="horaAlmIni">—</div>
                  </button>
                  <button class="marca-btn almuerzo-fin" id="btnAlmFin" onclick="marcar('ALMUERZO_FIN')">
                    <div class="ico"><i class="bi bi-cup"></i></div>
                    <div class="lbl">Fin almuerzo</div>
                    <div class="hora" id="horaAlmFin">—</div>
                  </button>
                  <button class="marca-btn salida" id="btnSalida" onclick="marcar('SALIDA')">
                    <div class="ico"><i class="bi bi-box-arrow-right"></i></div>
                    <div class="lbl">Salida</div>
                    <div class="hora" id="horaSalida">—</div>
                  </button>
                </div>
              </div>
            </div>

            <!-- Historial / vista admin -->
            <div class="card" style="overflow:hidden">
              <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
                <span class="card-title" id="historialTitle">Mi historial</span>
                <div id="adminTabs" style="display:none;gap:6px">
                  <button class="filter-chip active" id="tabMios" onclick="setVista('MIOS')">Míos</button>
                  <button class="filter-chip" id="tabTodos" onclick="setVista('TODOS')">Todo el personal</button>
                  <button class="filter-chip" id="tabPermisos" onclick="setVista('PERMISOS')">Permisos</button>
                </div>
              </div>
              <div class="table-wrap">
                <table>
                  <thead id="histHead"></thead>
                  <tbody id="histBody"></tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal Permiso -->
  <div class="modal-overlay" id="permisoModal">
    <div class="modal" style="max-width:440px">
      <div class="modal-header">
        <h3 class="modal-title">Solicitar permiso</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('permisoModal')"><i class="bi bi-x"></i></button>
      </div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div>
          <label class="form-label">Tipo</label>
          <select id="pTipo" class="form-control">
            <option value="PERSONAL">Personal</option>
            <option value="MEDICO">Médico</option>
            <option value="FAMILIAR">Familiar</option>
            <option value="ESTUDIOS">Estudios</option>
            <option value="OTRO">Otro</option>
          </select>
        </div>
        <div style="display:flex;gap:10px">
          <div style="flex:1">
            <label class="form-label">Fecha desde</label>
            <input type="date" id="pDesde" class="form-control">
          </div>
          <div style="flex:1">
            <label class="form-label">Fecha hasta</label>
            <input type="date" id="pHasta" class="form-control">
          </div>
        </div>
        <div>
          <label class="form-label">Motivo</label>
          <textarea id="pMotivo" class="form-control" rows="3" placeholder="Explica brevemente el motivo..."></textarea>
        </div>
        <div style="display:flex;gap:10px;margin-top:4px">
          <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('permisoModal')">Cancelar</button>
          <button class="btn btn-primary" style="flex:1" onclick="enviarPermiso()">Enviar solicitud</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast toast-success" id="okToast">
    <i class="bi bi-check-circle" style="font-size:18px"></i>
    <div>
      <div class="toast-title" id="okTitle">Listo</div>
      <div class="toast-sub" id="okSub"></div>
    </div>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'asistencia' => $SIGRA_ASISTENCIA, 'permisos' => $SIGRA_PERMISOS
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script>
    SIGRA.initLayout('Asistencia');
    const user = SIGRA.getUser();
    const isAdmin = user && user.role === 'ADMIN';
    const usuarioKey = user.username || user.name;

    let vista = 'MIOS';

    function pad(n) { return String(n).padStart(2, '0'); }

    function tickClock() {
      const d = new Date();
      document.getElementById('clock').textContent =
        `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
      document.getElementById('clockDate').textContent = d.toLocaleDateString('es-GT',
        { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }
    setInterval(tickClock, 1000);
    tickClock();

    function actualizarBotones() {
      const reg = SIGRA_DATA.getAsistenciaHoy(usuarioKey) || {};
      const pares = [
        ['btnEntrada', 'horaEntrada', reg.entrada],
        ['btnAlmIni',  'horaAlmIni',  reg.almuerzoIni],
        ['btnAlmFin',  'horaAlmFin',  reg.almuerzoFin],
        ['btnSalida',  'horaSalida',  reg.salida],
      ];
      pares.forEach(([btnId, horaId, val]) => {
        const btn = document.getElementById(btnId);
        const lbl = document.getElementById(horaId);
        if (val) {
          btn.classList.add('done');
          if (btnId !== 'btnSalida') btn.disabled = true;
          lbl.textContent = val;
        } else {
          btn.classList.remove('done');
          btn.disabled = false;
          lbl.textContent = '—';
        }
      });
    }

    function marcar(evento) {
      const r = SIGRA_DATA.marcarAsistencia(usuarioKey, user.name, evento);
      if (!r.ok) {
        toast('No se pudo registrar', r.reason || '');
        return;
      }
      actualizarBotones();
      renderHistorial();
      const map = { ENTRADA:'Entrada', SALIDA:'Salida', ALMUERZO_INI:'Inicio almuerzo', ALMUERZO_FIN:'Fin almuerzo' };
      toast('Registrado', `${map[evento]} a las ${SIGRA_DATA.nowTime()}`);
    }

    function toast(title, sub) {
      document.getElementById('okTitle').textContent = title;
      document.getElementById('okSub').textContent = sub || '';
      SIGRA.showToast('okToast', 2200);
    }

    function setVista(v) {
      vista = v;
      ['tabMios','tabTodos','tabPermisos'].forEach(id => {
        document.getElementById(id).classList.toggle('active',
          (v === 'MIOS' && id === 'tabMios') ||
          (v === 'TODOS' && id === 'tabTodos') ||
          (v === 'PERMISOS' && id === 'tabPermisos'));
      });
      renderHistorial();
    }

    function calcularHoras(reg) {
      if (!reg.entrada || !reg.salida) return '—';
      const [h1, m1] = reg.entrada.split(':').map(Number);
      const [h2, m2] = reg.salida.split(':').map(Number);
      let minutos = (h2 * 60 + m2) - (h1 * 60 + m1);
      if (reg.almuerzoIni && reg.almuerzoFin) {
        const [ai, am] = reg.almuerzoIni.split(':').map(Number);
        const [bi, bm] = reg.almuerzoFin.split(':').map(Number);
        minutos -= ((bi * 60 + bm) - (ai * 60 + am));
      }
      if (minutos < 0) return '—';
      return `${Math.floor(minutos / 60)}h ${minutos % 60}m`;
    }

    function renderHistorial() {
      const head = document.getElementById('histHead');
      const body = document.getElementById('histBody');
      const title = document.getElementById('historialTitle');

      if (vista === 'PERMISOS') {
        title.textContent = 'Solicitudes de permiso';
        head.innerHTML = `<tr>
          <th>Empleado</th><th>Tipo</th><th>Desde</th><th>Hasta</th><th>Motivo</th><th>Estado</th><th></th>
        </tr>`;
        const permisos = SIGRA_DATA.getPermisos()
          .filter(p => isAdmin || p.usuario === usuarioKey);
        if (!permisos.length) {
          body.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">Sin solicitudes de permiso</td></tr>`;
          return;
        }
        body.innerHTML = permisos.map(p => {
          const badge = p.estado === 'APROBADO' ? '#E8F5E9;color:#2E7D32'
                      : p.estado === 'RECHAZADO' ? '#FFEBEE;color:#C62828'
                      : '#FFF8E1;color:#E65100';
          const acciones = (isAdmin && p.estado === 'PENDIENTE') ? `
            <button class="btn btn-sm" style="background:#2E7D32;color:#fff" onclick="aprobarPermiso(${p.id})">Aprobar</button>
            <button class="btn btn-sm btn-outline" style="color:#C62828" onclick="rechazarPermiso(${p.id})">Rechazar</button>
          ` : '';
          return `<tr>
            <td>${p.nombre}</td>
            <td><span class="badge" style="background:#EEF2FF;color:#1A2E4A">${p.tipo}</span></td>
            <td>${p.desde}</td>
            <td>${p.hasta}</td>
            <td style="font-size:12px;color:#6B7280">${p.motivo || '—'}</td>
            <td><span class="badge" style="background:${badge}">${p.estado}</span></td>
            <td>${acciones}</td>
          </tr>`;
        }).join('');
        return;
      }

      title.textContent = vista === 'TODOS' ? 'Asistencia · todo el personal' : 'Mi historial';
      head.innerHTML = `<tr>
        ${vista === 'TODOS' ? '<th>Empleado</th>' : ''}
        <th>Fecha</th>
        <th>Entrada</th>
        <th>Almuerzo</th>
        <th>Salida</th>
        <th class="r">Horas trabajadas</th>
      </tr>`;
      const regs = SIGRA_DATA.getAsistencia()
        .filter(r => vista === 'TODOS' ? isAdmin : r.usuario === usuarioKey)
        .sort((a, b) => b.fecha.localeCompare(a.fecha));
      if (!regs.length) {
        body.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">Sin registros aún</td></tr>`;
        return;
      }
      body.innerHTML = regs.map(r => `
        <tr>
          ${vista === 'TODOS' ? `<td style="font-weight:500">${r.nombre}</td>` : ''}
          <td>${r.fecha}</td>
          <td style="color:#2E7D32;font-weight:600">${r.entrada || '—'}</td>
          <td style="color:#F57C00">${r.almuerzoIni || '—'} → ${r.almuerzoFin || '—'}</td>
          <td style="color:#C62828;font-weight:600">${r.salida || '—'}</td>
          <td class="r" style="font-weight:700">${calcularHoras(r)}</td>
        </tr>`).join('');
    }

    function enviarPermiso() {
      const tipo = document.getElementById('pTipo').value;
      const desde = document.getElementById('pDesde').value;
      const hasta = document.getElementById('pHasta').value;
      const motivo = document.getElementById('pMotivo').value.trim();
      if (!desde || !hasta) { alert('Selecciona ambas fechas.'); return; }
      if (desde > hasta) { alert('La fecha "desde" debe ser anterior a "hasta".'); return; }
      SIGRA_DATA.addPermiso({ usuario: usuarioKey, nombre: user.name, tipo, desde, hasta, motivo });
      SIGRA.closeModal('permisoModal');
      document.getElementById('pMotivo').value = '';
      toast('Solicitud enviada', 'Pendiente de aprobación');
      if (vista === 'PERMISOS') renderHistorial();
    }

    function aprobarPermiso(id) {
      SIGRA_DATA.updatePermiso(id, { estado: 'APROBADO' });
      renderHistorial();
    }
    function rechazarPermiso(id) {
      SIGRA_DATA.updatePermiso(id, { estado: 'RECHAZADO' });
      renderHistorial();
    }

    /* Init */
    if (isAdmin) {
      document.getElementById('adminTabs').style.display = 'flex';
    }
    actualizarBotones();
    renderHistorial();
    document.getElementById('pDesde').value = SIGRA_DATA.todayKey();
    document.getElementById('pHasta').value = SIGRA_DATA.todayKey();
  </script>
</body>
</html>
