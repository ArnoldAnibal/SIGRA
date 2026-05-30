<?php
require_once __DIR__ . '/includes/bootstrap.php';

/* Rutas por rol (también usadas por JS más abajo via window.SIGRA_ROUTES). */
$ROUTES_BY_ROLE = [
    'ADMIN'  => 'dashboard.php',
    'MESERO' => 'pedidos-mesa.php',
    'COCINA' => 'cocina.php',
    'CAJERO' => 'facturacion.php',
];

/* Si ya hay sesión activa, redirige según el rol. */
$current = sigra_current_user();
if ($current !== null) {
    $dest = $ROUTES_BY_ROLE[$current['role'] ?? ''] ?? 'dashboard.php';
    header('Location: ' . $dest);
    exit;
}

$loginError = null;

/* ── HUECO BACKEND: procesamiento del POST de login ─────────
   Validar contra BD, leer el rol asignado al usuario, llenar
   $_SESSION['user'] y redirigir. El selector de rol fue
   removido de la UI: el rol lo determina el backend.
   ─────────────────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $loginError = sigra_process_login($username, $password, $ROUTES_BY_ROLE);
        // Si llegamos aquí, hubo error (si fue OK, ya redirigió arriba)
    } else {
        $loginError = 'Ingresá tu usuario y contraseña.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGRA — Acceso Empleados</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 16px;
      background: linear-gradient(160deg, #0F1E33 0%, #1A2E4A 50%, #0d2137 100%);
    }
    .wrapper { width: 100%; max-width: 420px; }

    /* ── Logo ── */
    .logo-wrap { text-align: center; margin-bottom: 28px; }
    .logo-icon {
      width: 80px; height: 80px;
      background: #1A2E4A;
      border: 2px solid rgba(232,160,32,.5);
      border-radius: 20px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,.4);
      position: relative;
    }
    .logo-icon i { font-size: 36px; color: #E8A020; }
    .logo-badge {
      position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%);
      background: #E8A020; color: #1A2E4A;
      font-size: 9px; font-weight: 800; letter-spacing: .6px;
      text-transform: uppercase; padding: 2px 10px; border-radius: 999px;
      white-space: nowrap;
    }
    .logo-title { color: #fff; font-size: 26px; font-weight: 700; letter-spacing: -.5px; margin-top: 14px; }
    .logo-sub   { color: rgba(255,255,255,.55); font-size: 13px; margin-top: 5px; }

    /* ── Card ── */
    .login-card {
      background: #fff; border-radius: 22px;
      box-shadow: 0 24px 56px rgba(0,0,0,.4);
      padding: 32px;
    }

    .card-header {
      display: flex; align-items: center; gap: 12px;
      margin-bottom: 24px; padding-bottom: 20px;
      border-bottom: 1px solid #F3F4F6;
    }
    .card-header-icon {
      width: 42px; height: 42px; border-radius: 10px;
      background: #EEF2FF;
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; color: #1A2E4A; flex-shrink: 0;
    }
    .card-header-text strong {
      display: block; font-size: 16px; font-weight: 700; color: #1A2E4A;
    }
    .card-header-text span {
      font-size: 12px; color: #9CA3AF;
    }

    /* ── Error ── */
    .error-box {
      display: none; background: #C62828; color: #fff;
      padding: 10px 14px; border-radius: 8px;
      font-size: 13px; margin-bottom: 16px;
      display: flex; align-items: center; gap: 8px;
    }
    .error-box.show { display: flex; }

    /* ── Form ── */
    .form-group { margin-bottom: 18px; }
    .form-label {
      display: block; font-size: 13px; font-weight: 500;
      color: #374151; margin-bottom: 6px;
    }
    .form-control {
      width: 100%; height: 44px; padding: 0 16px;
      border: 1.5px solid #D1D5DB; border-radius: 9px;
      font-size: 13.5px; color: #1F2937; outline: none;
      transition: border-color .15s, box-shadow .15s;
    }
    .form-control:focus {
      border-color: #1A2E4A;
      box-shadow: 0 0 0 3px rgba(26,46,74,.1);
    }
    .form-control.is-invalid { border-color: #C62828; box-shadow: none; }
    .form-error { font-size: 12px; color: #C62828; margin-top: 4px; display: none; }
    .form-error.show { display: block; }

    /* ── Password ── */
    .pw-wrap { position: relative; }
    .pw-wrap .form-control { padding-right: 46px; }
    .pw-toggle {
      position: absolute; right: 13px; top: 50%;
      transform: translateY(-50%);
      color: #9CA3AF; font-size: 16px;
      background: none; border: none; cursor: pointer;
      transition: color .15s;
    }
    .pw-toggle:hover { color: #6B7280; }

    /* ── Selector de rol ── */
    .role-section-label {
      font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 8px; display: block;
    }
    .role-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
      margin-bottom: 22px;
    }
    .role-btn {
      height: 44px; border-radius: 9px;
      border: 2px solid #E5E7EB;
      font-size: 13px; font-weight: 500;
      color: #6B7280; background: #fff;
      cursor: pointer; transition: all .15s;
      display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .role-btn i { font-size: 14px; }
    .role-btn:hover { border-color: #9CA3AF; background: #F9FAFB; }
    .role-btn.active { color: #fff; border-color: transparent; }

    /* ── Submit ── */
    .btn-submit {
      width: 100%; height: 48px;
      background: #1A2E4A; color: #fff;
      border: none; border-radius: 11px;
      font-size: 15px; font-weight: 700;
      cursor: pointer; transition: opacity .15s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-submit:hover { opacity: .9; }
    .btn-submit:disabled { opacity: .55; cursor: not-allowed; }

    /* ── Spinner ── */
    .spinner {
      width: 18px; height: 18px;
      border: 2px solid rgba(255,255,255,.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin .8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Demo note ── */
    .demo-note {
      text-align: center; font-size: 11.5px; color: #9CA3AF; margin-top: 16px;
      padding: 10px 14px; background: #F9FAFB; border-radius: 8px;
      border: 1px solid #E5E7EB;
    }
    .demo-note strong { color: #6B7280; }

    /* ── Link cliente ── */
    .client-link {
      text-align: center; margin-top: 22px;
      font-size: 12px; color: rgba(255,255,255,.35);
    }
    .client-link a {
      color: rgba(255,255,255,.6); font-weight: 600; text-decoration: none;
      display: inline-flex; align-items: center; gap: 5px;
      transition: color .15s;
    }
    .client-link a:hover { color: rgba(255,255,255,.9); }

    .footer-note {
      text-align: center; color: rgba(255,255,255,.18);
      font-size: 11px; margin-top: 16px;
    }
  </style>
</head>
<body>
  <div class="wrapper">

    <!-- ── Logo ── -->
    <div class="logo-wrap">
      <div class="logo-icon">
        <i class="bi bi-shield-lock"></i>
        <span class="logo-badge">Empleados</span>
      </div>
      <div class="logo-title">SIGRA</div>
      <div class="logo-sub">Portal de Acceso Interno · Restaurante La Antigua</div>
    </div>

    <!-- ── Card ── -->
    <div class="login-card">

      <div class="card-header">
        <div class="card-header-icon">
          <i class="bi bi-person-badge-fill"></i>
        </div>
        <div class="card-header-text">
          <strong>Acceso para Empleados</strong>
          <span>Ingresa tus credenciales asignadas por el sistema</span>
        </div>
      </div>

      <div class="error-box <?= $loginError ? 'show' : '' ?>" id="errorBox" <?= $loginError ? '' : 'style="display:none"' ?>>
        <i class="bi bi-exclamation-circle-fill"></i>
        <span id="errorMsg"><?= htmlspecialchars($loginError ?? 'Credenciales incorrectas.') ?></span>
      </div>

      <form id="loginForm" method="post" action="login-empleados.php" novalidate>

        <!-- Usuario -->
        <div class="form-group">
          <label class="form-label" for="username">
            <i class="bi bi-person" style="margin-right:5px;opacity:.6"></i>Usuario
          </label>
          <input type="text" id="username" name="username" class="form-control"
                 placeholder="Ingrese su usuario" autocomplete="username"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
          <div class="form-error" id="usernameError">El usuario es requerido.</div>
        </div>

        <!-- Contraseña -->
        <div class="form-group">
          <label class="form-label" for="password">
            <i class="bi bi-key" style="margin-right:5px;opacity:.6"></i>Contraseña
          </label>
          <div class="pw-wrap">
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Ingrese su contraseña" autocomplete="current-password">
            <button type="button" class="pw-toggle" id="pwToggle" aria-label="Ver contraseña">
              <i class="bi bi-eye" id="pwIcon"></i>
            </button>
          </div>
          <div class="form-error" id="passwordError">La contraseña es requerida.</div>
        </div>

        <!-- NOTA: el selector de rol fue removido. El rol ahora lo determina
             el backend a partir del usuario en BD (ver $ROUTES_BY_ROLE arriba). -->

        <button type="submit" class="btn-submit" id="submitBtn">
          <i class="bi bi-box-arrow-in-right"></i>
          <span id="btnText">Iniciar Sesión</span>
        </button>

      </form>

      <!-- Botón Google: pendiente para próximas actualizaciones -->
      <button type="button" class="btn-submit" disabled
              style="margin-top:12px;background:#fff;color:#374151;border:1.5px solid #E5E7EB;cursor:not-allowed;opacity:.7"
              title="Disponible en próximas actualizaciones">
        <i class="bi bi-google" style="color:#DB4437"></i>
        Continuar con Google
        <span style="font-size:10px;background:#FFF3CD;color:#92400E;padding:2px 7px;border-radius:999px;font-weight:700;margin-left:6px">PRÓXIMAMENTE</span>
      </button>

      <div class="demo-note">
        Modo demo: usa <strong>admin / mesero / cocina / cajero</strong> con cualquier contraseña.
        El rol se inferirá automáticamente.
      </div>

    </div><!-- /card -->

    <p class="footer-note">SIGRA v2.1.0 © 2026 · Uso exclusivo del personal autorizado</p>
  </div>

  <!-- Rutas por rol (las define PHP en $ROUTES_BY_ROLE). Si backend ya redirigió,
       este JS nunca corre. Si estamos en modo demo, lo usa para enviar al usuario. -->
<?= sigra_bootstrap_script([
    'routesByRole' => $ROUTES_BY_ROLE,
]) ?>

  <script>
    const BOOT = (() => {
      try { return JSON.parse(document.getElementById('sigra-bootstrap').textContent.trim() || '{}'); }
      catch { return {}; }
    })();

    const pwToggle = document.getElementById('pwToggle');
    const pwInput  = document.getElementById('password');
    const pwIcon   = document.getElementById('pwIcon');
    pwToggle.addEventListener('click', () => {
      const show = pwInput.type === 'password';
      pwInput.type = show ? 'text' : 'password';
      pwIcon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    const form = document.getElementById('loginForm');

    function showFieldError(id, show) {
      document.getElementById(id + 'Error').classList.toggle('show', show);
      document.getElementById(id).classList.toggle('is-invalid', show);
    }

    form.addEventListener('submit', (e) => {
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();
      let valid = true;
      showFieldError('username', !username); if (!username) valid = false;
      showFieldError('password', !password); if (!password) valid = false;
      if (!valid) e.preventDefault();
    });

    ['username', 'password'].forEach(id => {
      document.getElementById(id).addEventListener('input', () => showFieldError(id, false));
    });
</script>
</body>
</html>
