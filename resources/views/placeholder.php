<?php
require_once __DIR__ . '/includes/bootstrap.php';
sigra_require_login();
$SIGRA_USER = sigra_current_user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — En Desarrollo</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .coming-soon {
      display: flex; flex-direction: column; align-items: center;
      justify-content: center; min-height: 60vh; padding: 48px;
      text-align: center; color: #9CA3AF;
    }
    .coming-soon i  { font-size: 64px; margin-bottom: 20px; opacity: .35; color: var(--navy); }
    .coming-soon h2 { font-size: 22px; font-weight: 700; color: var(--navy); margin-bottom: 8px; }
    .coming-soon p  { font-size: 14px; color: #9CA3AF; max-width: 380px; }
  </style>
</head>
<body>
  <div class="app">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar"></aside>

    <div class="main-wrap" id="mainWrap">
      <header class="topbar" id="topbar"></header>

      <main class="page-content">
        <div class="coming-soon">
          <i class="bi bi-tools"></i>
          <h2 id="pageTitle">Módulo en Desarrollo</h2>
          <p>Esta funcionalidad estará disponible próximamente. Estamos trabajando para integrarla lo antes posible.</p>
          <a href="dashboard.php" class="btn btn-primary" style="margin-top:24px">
            <i class="bi bi-arrow-left"></i> Volver al Panel
          </a>
        </div>
      </main>
    </div>
  </div>

<?= sigra_bootstrap_script([
    'session' => $SIGRA_USER,
]) ?>

  <script src="layout.js"></script>
  <script>
    // Determine current page from URL
    const file = location.pathname.split('/').pop();
    const titles = {
      'menu.php':          'Gestión de Menú',
      'reportes.php':      'Reportes y Análisis',
      'configuracion.php': 'Configuración del Sistema',
    };
    const title = titles[file] || 'Módulo en Desarrollo';
    document.getElementById('pageTitle').textContent = title;
    document.title = `SIGRA — ${title}`;
    SIGRA.initLayout(title);
  </script>
</body>
</html>
