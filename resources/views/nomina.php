<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_role(['ADMIN']);
$SIGRA_USER = sigra_current_user();
$resP   = api_planillas(sigra_token());
$resEmp = api_empleados(sigra_token());
$SIGRA_PLANILLAS = $resP['data']['data'] ?? $resP['data'] ?? [];
$empleadosRaw    = $resEmp['data']['data'] ?? $resEmp['data'] ?? [];

// Cruzar empleados con su última planilla para obtener salario
$SIGRA_EMPLEADOS = array_map(function($emp) use ($SIGRA_PLANILLAS) {
    $planilla = null;
    foreach ($SIGRA_PLANILLAS as $p) {
        if ($p['empleadoId'] == $emp['id']) {
            if (!$planilla || $p['id'] > $planilla['id']) {
                $planilla = $p;
            }
        }
    }
    $salario = $planilla ? $planilla['salarioNeto'] : 0;
    $igss    = round($salario * 0.0483, 2);
    return array_merge($emp, [
        'salarioBruto' => $salario,
        'igss'         => $igss,
        'isr'          => 0,
    ]);
}, $empleadosRaw);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Nómina</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.0/jspdf.plugin.autotable.min.js"></script>
  <style>
    .nom-body  { padding: 24px; overflow-y: auto; height: calc(100vh - 64px - 73px); }
    .nom-inner { max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

    .emp-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: var(--navy); color: #fff;
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 700; flex-shrink: 0;
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
          <div class="page-header-row">
            <div>
              <div class="page-title">Administración de Nómina</div>
              <div class="page-sub">7 empleados activos · Guatemala</div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
              <button class="btn btn-outline" id="btnNuevoEmpleado"><i class="bi bi-person-plus"></i> Nuevo empleado</button>
              <button class="btn btn-outline" id="btnExportarPdf"><i class="bi bi-download"></i> Exportar PDF</button>
              <button class="btn btn-outline" id="btnImprimir"><i class="bi bi-printer"></i> Imprimir</button>
              <button class="btn btn-primary" id="btnGenerar">
                <i class="bi bi-file-text"></i> Generar Planilla
              </button>
            </div>
          </div>
        </div>

        <div class="nom-body">
          <div class="nom-inner">

            <!-- Period Selector -->
            <div class="card">
              <div class="card-header"><span class="card-title">Período de Nómina</span></div>
              <div class="card-body">
                <div style="display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap">
                  <div>
                    <label class="form-label-sm">Mes</label>
                    <select id="selMes" class="form-control" style="width:160px" onchange="onPeriodChange()">
                      <option>Enero</option><option>Febrero</option><option>Marzo</option>
                      <option>Abril</option><option selected>Mayo</option><option>Junio</option>
                      <option>Julio</option><option>Agosto</option><option>Septiembre</option>
                      <option>Octubre</option><option>Noviembre</option><option>Diciembre</option>
                    </select>
                  </div>
                  <div>
                    <label class="form-label-sm">Año</label>
                    <select id="selAnio" class="form-control" onchange="onPeriodChange()">
                      <option selected>2026</option><option>2025</option><option>2024</option>
                    </select>
                  </div>
                  <div class="ml-auto">
                    <div id="generatedBadge" style="display:none;background:#E8F5E9;color:#2E7D32;padding:6px 12px;border-radius:var(--r);font-size:13px;display:none;align-items:center;gap:6px">
                      <i class="bi bi-check-circle"></i>
                      <span id="generatedLabel"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid-4" id="summaryCards"><!-- filled by JS --></div>

            <!-- Employees Table -->
            <div class="card" style="overflow:hidden">
              <div class="card-header">
                <span class="card-title" id="tableTitle">Detalle de Empleados · Mayo 2026</span>
              </div>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Empleado</th>
                      <th>Puesto</th>
                      <th class="r">Salario Bruto</th>
                      <th class="r">IGSS 4.83%</th>
                      <th class="r">ISR</th>
                      <th class="r">Salario Neto</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id="empTable"></tbody>
                  <tfoot id="empTotals"></tfoot>
                </table>
              </div>
            </div>

            <!-- Info box -->
            <div class="info-box info-box-blue">
              <i class="bi bi-info-circle" style="flex-shrink:0;font-size:16px"></i>
              <div style="font-size:13px">
                <strong>Nota:</strong> Las deducciones de IGSS (4.83%) son sobre el salario bruto devengado.
                El ISR se aplica según escala del SAT para ingresos superiores a Q 48,000 anuales.
                Esta planilla cumple con el Código de Trabajo de Guatemala.
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Nuevo Empleado Modal -->
  <div class="modal-overlay" id="empModal">
    <div class="modal" style="max-width:440px">
      <div class="modal-header">
        <h3 class="modal-title">Nuevo empleado</h3>
        <button class="modal-close" onclick="SIGRA.closeModal('empModal')"><i class="bi bi-x"></i></button>
      </div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div>
          <label class="form-label">Nombre completo</label>
          <input type="text" id="eNombre" class="form-control" placeholder="Ej. Juan Pérez">
        </div>
        <div>
          <label class="form-label">Puesto</label>
          <select id="ePuesto" class="form-control">
            <option value="ADMIN">ADMIN</option>
            <option value="MESERO" selected>MESERO</option>
            <option value="COCINA">COCINA</option>
            <option value="CAJERO">CAJERO</option>
          </select>
        </div>
        <div>
          <label class="form-label">Salario bruto (Q)</label>
          <input type="number" step="0.01" min="0" id="eSalario" class="form-control" placeholder="0.00">
        </div>
        <div>
          <label class="form-label">ISR mensual (Q) <span style="color:#9CA3AF;font-weight:400">(0 si no aplica)</span></label>
          <input type="number" step="0.01" min="0" id="eIsr" class="form-control" value="0">
        </div>
        <div class="info-box info-box-blue" style="font-size:12px">
          <i class="bi bi-info-circle"></i>
          <div>IGSS se calcula automáticamente al 4.83% del salario bruto.</div>
        </div>
        <div style="display:flex;gap:10px;margin-top:4px">
          <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('empModal')">Cancelar</button>
          <button class="btn btn-primary" style="flex:1" onclick="guardarEmpleado()">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Confirm Modal -->
  <div class="modal-overlay" id="confirmModal">
    <div class="modal" style="max-width:380px">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
        <div class="modal-icon" style="background:#EFF6FF;width:44px;height:44px;margin:0;flex-shrink:0;font-size:20px">
          <i class="bi bi-file-text" style="color:var(--blue)"></i>
        </div>
        <h3 class="modal-title">¿Generar Planilla?</h3>
      </div>
      <p style="font-size:13px;color:#6B7280;margin-bottom:10px">Se generará la planilla de nómina para el período:</p>
      <div style="background:#F9FAFB;border-radius:var(--r);padding:14px;text-align:center;margin-bottom:20px">
        <div style="font-size:20px;font-weight:700;color:var(--navy)" id="confirmPeriod">Mayo 2026</div>
        <div style="font-size:13px;color:#6B7280;margin-top:4px" id="confirmDetail"></div>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-outline" style="flex:1" onclick="SIGRA.closeModal('confirmModal')">Cancelar</button>
        <button class="btn btn-primary" style="flex:1" onclick="generarPlanilla()">Confirmar</button>
      </div>
    </div>
  </div>

  <!-- Success Toast -->
  <div class="toast toast-success" id="successToast">
    <i class="bi bi-check-circle" style="font-size:18px"></i>
    <div>
      <div class="toast-title" id="toastTitle">Planilla generada</div>
      <div class="toast-sub" id="toastSub"></div>
    </div>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
    'planillas' => $SIGRA_PLANILLAS, 'empleados' => $SIGRA_EMPLEADOS
]) ?>

  <script src="layout.js"></script>
  <script src="sigra-data.js"></script>
  <script src="sigra-pdf.js"></script>
  <script>
    SIGRA.initLayout('Nómina');

    let empleados = SIGRA_DATA.getEmpleados();

    const puestoBadge = {
      ADMIN:   { bg:'#EEF2FF', text:'#1A2E4A' },
      MESERO:  { bg:'#E3F2FD', text:'#1565C0' },
      COCINA:  { bg:'#FFF3E0', text:'#E65100' },
      CAJERO:  { bg:'#E8F5E9', text:'#2E7D32' },
    };

    let totalBruto, totalIgss, totalIsr, totalNeto;
    function recalcTotales() {
      totalBruto = empleados.reduce((a, e) => a + e.salarioBruto, 0);
      totalIgss  = empleados.reduce((a, e) => a + e.igss, 0);
      totalIsr   = empleados.reduce((a, e) => a + e.isr, 0);
      totalNeto  = empleados.reduce((a, e) => a + (e.salarioBruto - e.igss - e.isr), 0);
    }
    recalcTotales();

    let generated = false;

    function onPeriodChange() { generated = false; document.getElementById('generatedBadge').style.display = 'none'; }

    function getMes()  { return document.getElementById('selMes').value; }
    function getAnio() { return document.getElementById('selAnio').value; }

    function render() {
      document.getElementById('tableTitle').textContent = `Detalle de Empleados · ${getMes()} ${getAnio()}`;

      // Summary cards
      document.getElementById('summaryCards').innerHTML = [
        { label:'Total Bruto',  value:`Q ${totalBruto.toFixed(2)}`, color:'#1A2E4A' },
        { label:'IGSS (4.83%)', value:`Q ${totalIgss.toFixed(2)}`,  color:'#F57C00' },
        { label:'ISR',          value:`Q ${totalIsr.toFixed(2)}`,   color:'#C62828' },
        { label:'Total Neto',   value:`Q ${totalNeto.toFixed(2)}`,  color:'#2E7D32' },
      ].map(c => `
        <div class="kpi-card">
          <p class="text-xs text-muted mb-1">${c.label}</p>
          <p style="font-size:19px;font-weight:700;color:${c.color}">${c.value}</p>
        </div>`).join('');

      // Employees table
      document.getElementById('empTable').innerHTML = empleados.map(emp => {
        const neto  = emp.salarioBruto - emp.igss - emp.isr;
        const badge = puestoBadge[emp.puesto] || { bg:'#F5F5F5', text:'#666' };
        const init  = emp.nombre.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
        return `
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div class="emp-avatar">${init}</div>
                <span style="font-weight:500;color:#1F2937">${emp.nombre}</span>
              </div>
            </td>
            <td><span class="badge" style="background:${badge.bg};color:${badge.text}">${emp.puesto}</span></td>
            <td class="r" style="color:#374151">Q ${emp.salarioBruto.toFixed(2)}</td>
            <td class="r" style="color:#E65100">- Q ${emp.igss.toFixed(2)}</td>
            <td class="r" style="color:#C62828">
              ${emp.isr > 0 ? `- Q ${emp.isr.toFixed(2)}` : '<span style="color:#D1D5DB">—</span>'}
            </td>
            <td class="r" style="font-weight:700;color:#2E7D32">Q ${neto.toFixed(2)}</td>
            <td class="r"><button class="btn btn-sm" title="Eliminar" style="background:none;border:none;color:#C62828;cursor:pointer" onclick="eliminarEmpleado(${emp.id})"><i class="bi bi-trash"></i></button></td>
          </tr>`;
      }).join('');

      // Totals
      document.getElementById('empTotals').innerHTML = `
        <tr>
          <td colspan="2" style="color:#1F2937">TOTALES</td>
          <td class="r" style="color:#1F2937">Q ${totalBruto.toFixed(2)}</td>
          <td class="r" style="color:#E65100">- Q ${totalIgss.toFixed(2)}</td>
          <td class="r" style="color:#C62828">- Q ${totalIsr.toFixed(2)}</td>
          <td class="r" style="color:#2E7D32">Q ${totalNeto.toFixed(2)}</td>
          <td></td>
        </tr>`;

      document.querySelector('.page-sub').textContent = `${empleados.length} empleados activos · Guatemala`;
    }

    function eliminarEmpleado(id) {
      const emp = empleados.find(e => e.id === id);
      if (!emp || !confirm(`¿Eliminar a ${emp.nombre} de la planilla?`)) return;
      SIGRA_DATA.deleteEmpleado(id);
      empleados = SIGRA_DATA.getEmpleados();
      recalcTotales();
      render();
      document.getElementById('toastTitle').textContent = 'Empleado eliminado';
      document.getElementById('toastSub').textContent = emp.nombre;
      SIGRA.showToast('successToast', 2500);
    }

    function guardarEmpleado() {
      const nombre = document.getElementById('eNombre').value.trim();
      const puesto = document.getElementById('ePuesto').value;
      const salarioBruto = Number(document.getElementById('eSalario').value);
      const isr = Number(document.getElementById('eIsr').value) || 0;
      if (!nombre || !salarioBruto || salarioBruto <= 0) {
        alert('Completa nombre y un salario mayor a 0.');
        return;
      }
      const igss = +(salarioBruto * 0.0483).toFixed(2);
      SIGRA_DATA.addEmpleado({ nombre, puesto, salarioBruto, igss, isr });
      empleados = SIGRA_DATA.getEmpleados();
      recalcTotales();
      SIGRA.closeModal('empModal');
      render();
      document.getElementById('toastTitle').textContent = 'Empleado agregado';
      document.getElementById('toastSub').textContent = `${nombre} · ${puesto}`;
      SIGRA.showToast('successToast', 2500);
    }

    function openConfirm() {
      document.getElementById('confirmPeriod').textContent = `${getMes()} ${getAnio()}`;
      document.getElementById('confirmDetail').textContent = `${empleados.length} empleados · Q ${totalNeto.toFixed(2)} neto total`;
      SIGRA.openModal('confirmModal');
    }

    function getTotales() {
      return { bruto: totalBruto, igss: totalIgss, isr: totalIsr, neto: totalNeto };
    }

    function generarPlanilla() {
      SIGRA.closeModal('confirmModal');
      generated = true;
      const periodo = `${getMes()} ${getAnio()}`;
      const badge = document.getElementById('generatedBadge');
      document.getElementById('generatedLabel').textContent = `Planilla ${periodo} generada`;
      badge.style.display = 'flex';

      // Guardar la planilla
      SIGRA_DATA.addPlanilla({
        id: Date.now(),
        periodo,
        fechaGeneracion: new Date().toISOString().slice(0, 10),
        empleados: empleados.map(e => ({ ...e, neto: e.salarioBruto - e.igss - e.isr })),
        totales: getTotales(),
      });

      // Descargar PDF
      SIGRA_PDF.descargarPlanilla(periodo, empleados, getTotales());

      document.getElementById('toastTitle').textContent = 'Planilla generada';
      document.getElementById('toastSub').textContent = `${periodo} · PDF descargado · Q ${totalNeto.toFixed(2)} neto`;
      SIGRA.showToast('successToast', 3000);
    }

    function exportarPdf() {
      const periodo = `${getMes()} ${getAnio()}`;
      SIGRA_PDF.descargarPlanilla(periodo, empleados, getTotales());
      document.getElementById('toastTitle').textContent = 'PDF descargado';
      document.getElementById('toastSub').textContent = `Planilla ${periodo}`;
      SIGRA.showToast('successToast', 2500);
    }

    function imprimir() {
      const periodo = `${getMes()} ${getAnio()}`;
      SIGRA_PDF.imprimirPlanilla(periodo, empleados, getTotales());
    }

    document.getElementById('btnNuevoEmpleado').addEventListener('click', () => {
      document.getElementById('eNombre').value = '';
      document.getElementById('eSalario').value = '';
      document.getElementById('eIsr').value = 0;
      document.getElementById('ePuesto').value = 'MESERO';
      SIGRA.openModal('empModal');
    });
    document.getElementById('btnGenerar').addEventListener('click', openConfirm);
    document.getElementById('btnExportarPdf').addEventListener('click', exportarPdf);
    document.getElementById('btnImprimir').addEventListener('click', imprimir);

    render();
  </script>
</body>
</html>
