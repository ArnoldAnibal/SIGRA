<?php
require_once __DIR__ . '/includes/bootstrap.php';
// Página pública: no exige sesión. Solo inicializa para que el modal
// de registro pueda guardar al cliente en $_SESSION si backend lo desea.
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>La Antigua — Restaurante Caribeño · Puerto Barrios</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 16px;
      background: linear-gradient(135deg, #1A2E4A 0%, #243d5f 55%, #1a3a2a 100%);
    }
    .wrapper { width: 100%; max-width: 430px; }

    /* ── Logo ── */
    .logo-wrap { text-align: center; margin-bottom: 28px; }
    .logo-icon {
      width: 92px; height: 92px;
      background: linear-gradient(145deg, #E8A020, #c98418);
      border-radius: 26px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 18px;
      box-shadow: 0 12px 32px rgba(232,160,32,.4), 0 4px 12px rgba(0,0,0,.3);
    }
    .logo-icon i { font-size: 44px; color: #fff; }
    .logo-title  { color: #fff; font-size: 32px; font-weight: 800; letter-spacing: -.5px; }
    .logo-sub    { color: rgba(255,255,255,.7); font-size: 14px; margin-top: 5px; }
    .logo-place  { color: rgba(255,255,255,.35); font-size: 11.5px; margin-top: 3px; }

    /* ── Card principal ── */
    .card {
      background: #fff; border-radius: 26px;
      box-shadow: 0 28px 64px rgba(0,0,0,.35);
      padding: 38px 34px;
    }
    .card-welcome { text-align: center; margin-bottom: 28px; }
    .card-welcome h2 {
      font-size: 20px; font-weight: 700; color: #1A2E4A; margin-bottom: 6px;
    }
    .card-welcome p { font-size: 13px; color: #6B7280; line-height: 1.6; }

    /* ── Botón principal ── */
    .btn-enter {
      display: flex; align-items: center; justify-content: center;
      gap: 10px; width: 100%; padding: 14px 20px;
      background: linear-gradient(135deg, #1A2E4A, #243d5f);
      color: #fff; border: none; border-radius: 13px;
      font-size: 15px; font-weight: 700;
      cursor: pointer;
      box-shadow: 0 6px 20px rgba(26,46,74,.3);
      transition: transform .15s, box-shadow .15s, opacity .15s;
    }
    .btn-enter:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(26,46,74,.4);
    }
    .btn-enter:active { transform: scale(.97); }
    .btn-enter i { font-size: 18px; }

    /* ── Separador ── */
    .divider {
      display: flex; align-items: center; gap: 12px;
      margin: 22px 0 18px; color: #9CA3AF; font-size: 12px;
    }
    .divider::before, .divider::after {
      content: ''; flex: 1; height: 1px; background: #E5E7EB;
    }

    /* ── Info/accesos directos ── */
    .info-row { display: flex; flex-direction: column; gap: 10px; }
    .info-item {
      display: flex; align-items: center; gap: 14px;
      padding: 14px 16px;
      border: 1px solid #E5E7EB; border-radius: 13px;
      background: #FAFAFA; text-decoration: none;
      font-size: 13px; color: #374151;
      transition: border-color .15s, background .15s, transform .1s;
      cursor: pointer;
    }
    .info-item:hover { border-color: #CBD5E0; background: #F3F4F6; transform: translateY(-1px); }
    .info-item:active { transform: scale(.98); }
    .info-icon {
      width: 42px; height: 42px; border-radius: 11px;
      display: flex; align-items: center; justify-content: center;
      font-size: 19px; flex-shrink: 0;
    }
    .info-icon.green  { background: #E8F5E9; color: #2E7D32; }
    .info-icon.amber  { background: #FFF8E1; color: #F9A825; }
    .info-icon.blue   { background: #EEF2FF; color: #1A2E4A; }
    .info-item-text strong { display: block; font-weight: 600; color: #1A2E4A; font-size: 13px; }
    .info-item-text span   { font-size: 11.5px; color: #9CA3AF; }
    .info-chevron { margin-left: auto; color: #D1D5DB; font-size: 13px; }

    .footer-note {
      text-align: center; color: rgba(255,255,255,.22);
      font-size: 11px; margin-top: 22px;
    }

    /* ══════════════════════════════════════════
       MODAL: Registro de Cliente
    ══════════════════════════════════════════ */
    .modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,.55); z-index: 200;
      align-items: center; justify-content: center; padding: 16px;
    }
    .modal-overlay.open { display: flex; animation: fadeIn .2s ease; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    .modal-box {
      background: #fff; border-radius: 24px;
      padding: 36px 32px; max-width: 450px; width: 100%;
      box-shadow: 0 32px 72px rgba(0,0,0,.4);
      animation: popIn .28s cubic-bezier(.34,1.56,.64,1);
      max-height: 92vh; overflow-y: auto;
    }
    @keyframes popIn {
      from { opacity: 0; transform: scale(.86); }
      to   { opacity: 1; transform: scale(1); }
    }

    .modal-head { text-align: center; margin-bottom: 22px; }
    .modal-icon-wrap {
      width: 68px; height: 68px; border-radius: 50%;
      background: linear-gradient(145deg, #FFF8E1, #FFF3CD);
      border: 2px solid #FFE082;
      margin: 0 auto 14px;
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; color: #E8A020;
    }
    .modal-box h3 {
      font-size: 19px; font-weight: 700; color: #1A2E4A;
      margin-bottom: 5px; text-align: center;
    }
    .modal-sub { font-size: 12.5px; color: #9CA3AF; text-align: center; line-height: 1.5; }

    /* Campos */
    .mform-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .mform-group { margin-bottom: 14px; }
    .mform-label {
      display: block; font-size: 13px; font-weight: 500;
      color: #374151; margin-bottom: 5px;
    }
    .mform-label .req { color: #C62828; margin-left: 2px; }
    .mform-control {
      width: 100%; height: 44px; padding: 0 14px;
      border: 1.5px solid #D1D5DB; border-radius: 10px;
      font-size: 13.5px; color: #1F2937; outline: none; background: #fff;
      transition: border-color .15s, box-shadow .15s;
    }
    .mform-control:focus {
      border-color: #1A2E4A;
      box-shadow: 0 0 0 3px rgba(26,46,74,.1);
    }
    .mform-control.is-invalid { border-color: #C62828; box-shadow: 0 0 0 3px rgba(198,40,40,.08); }
    .mform-error { font-size: 11.5px; color: #C62828; margin-top: 4px; display: none; }
    .mform-error.show { display: block; }

    /* Acciones del modal */
    .modal-actions { display: flex; gap: 10px; margin-top: 22px; }
    .btn-ingresar {
      flex: 1; height: 48px;
      background: linear-gradient(135deg, #1A2E4A, #243d5f);
      color: #fff; border: none; border-radius: 12px;
      font-size: 14px; font-weight: 700; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      box-shadow: 0 4px 16px rgba(26,46,74,.25);
      transition: opacity .15s, transform .1s;
    }
    .btn-ingresar:hover  { opacity: .9; }
    .btn-ingresar:active { transform: scale(.97); }
    .btn-modal-cancel {
      height: 48px; padding: 0 24px;
      border: 1.5px solid #D1D5DB; border-radius: 12px;
      background: #fff; color: #6B7280;
      font-size: 14px; font-weight: 500; cursor: pointer;
      transition: border-color .15s, color .15s;
    }
    .btn-modal-cancel:hover { border-color: #9CA3AF; color: #374151; }

    @media (max-width: 380px) {
      .mform-row { grid-template-columns: 1fr; }
      .card { padding: 30px 22px; }
    }
  </style>
</head>
<body>
  <div class="wrapper">

    <!-- ── Logo ── -->
    <div class="logo-wrap">
      <div class="logo-icon"><i class="bi bi-shop"></i></div>
      <div class="logo-title">La Antigua</div>
      <div class="logo-sub">Restaurante Caribeño · Puerto Barrios, Izabal</div>
      <div class="logo-place">Sabores auténticos del Caribe guatemalteco</div>
    </div>

    <!-- ── Card principal ── -->
    <div class="card">

      <div class="card-welcome">
        <h2>¡Bienvenido!</h2>
        <p>Regístrate para hacer pedidos en línea, o explora directamente nuestro menú y servicios.</p>
      </div>

      <!-- Botón de registro -->
      <button class="btn-enter" onclick="showRegisterModal()">
        <i class="bi bi-person-plus"></i>
        Registrarme / Ingresar
      </button>

      <div class="divider">o explora directamente</div>

      <!-- Accesos directos -->
      <div class="info-row">
        <a class="info-item" href="menu-cliente.php">
          <div class="info-icon green"><i class="bi bi-book-open"></i></div>
          <div class="info-item-text">
            <strong>Ver el menú y pedir en línea</strong>
            <span>Platillos, bebidas y pedido a domicilio</span>
          </div>
          <i class="bi bi-chevron-right info-chevron"></i>
        </a>
        <a class="info-item" href="portal-cliente.php">
          <div class="info-icon amber"><i class="bi bi-images"></i></div>
          <div class="info-item-text">
            <strong>Conoce el restaurante</strong>
            <span>Fotos, reseñas, visión y misión</span>
          </div>
          <i class="bi bi-chevron-right info-chevron"></i>
        </a>
        <!-- NOTA: El acceso a "Reservar una mesa" fue removido.
             El cliente ahora hace pedidos en línea desde el menú. -->
      </div>

    </div><!-- /card -->

    <p class="footer-note">La Antigua © 2026 · Restaurante · Puerto Barrios, Izabal, Guatemala</p>
  </div>

  <!-- ══ MODAL: Registro de Cliente ══ -->
  <div class="modal-overlay" id="registerModal" onclick="handleOverlayClick(event)">
    <div class="modal-box">

      <div class="modal-head">
        <div class="modal-icon-wrap">
          <i class="bi bi-person-circle"></i>
        </div>
        <h3>Crea tu perfil</h3>
        <p class="modal-sub">Completa tus datos para hacer reservaciones y pedidos más fácilmente</p>
      </div>

      <!-- Nombre y Apellido en fila -->
      <div class="mform-row">
        <div class="mform-group">
          <label class="mform-label" for="regNombre">
            Nombre <span class="req">*</span>
          </label>
          <input type="text" id="regNombre" class="mform-control"
                 placeholder="Juan" autocomplete="given-name">
          <div class="mform-error" id="nombreError">El nombre es requerido.</div>
        </div>
        <div class="mform-group">
          <label class="mform-label" for="regApellido">
            Apellido <span class="req">*</span>
          </label>
          <input type="text" id="regApellido" class="mform-control"
                 placeholder="García" autocomplete="family-name">
          <div class="mform-error" id="apellidoError">El apellido es requerido.</div>
        </div>
      </div>

      <!-- Correo electrónico -->
      <div class="mform-group">
        <label class="mform-label" for="regCorreo">
          Correo electrónico <span class="req">*</span>
        </label>
        <input type="email" id="regCorreo" class="mform-control"
               placeholder="ejemplo@correo.com" autocomplete="email">
        <div class="mform-error" id="correoError">Ingrese un correo electrónico válido.</div>
      </div>

      <!-- Dirección -->
      <div class="mform-group">
        <label class="mform-label" for="regDireccion">
          Dirección <span class="req">*</span>
        </label>
        <input type="text" id="regDireccion" class="mform-control"
               placeholder="Col. Las Palmas, Puerto Barrios" autocomplete="street-address">
        <div class="mform-error" id="direccionError">La dirección es requerida.</div>
      </div>

      <div class="modal-actions">
        <button class="btn-ingresar" onclick="handleRegister()">
          <i class="bi bi-box-arrow-in-right"></i> Ingresar al portal
        </button>
        <button class="btn-modal-cancel" onclick="closeModal()">
          Cancelar
        </button>
      </div>

      <!-- Botón Google (deshabilitado: disponible en próximas actualizaciones) -->
      <div style="display:flex;align-items:center;gap:10px;margin:18px 0 6px;color:#9CA3AF;font-size:11.5px">
        <div style="flex:1;height:1px;background:#E5E7EB"></div>
        o continúa con
        <div style="flex:1;height:1px;background:#E5E7EB"></div>
      </div>
      <button type="button" disabled
              style="width:100%;height:46px;background:#fff;border:1.5px solid #E5E7EB;border-radius:11px;display:flex;align-items:center;justify-content:center;gap:10px;font-size:13.5px;font-weight:600;color:#374151;cursor:not-allowed;opacity:.7"
              title="Disponible en próximas actualizaciones">
        <i class="bi bi-google" style="color:#DB4437;font-size:18px"></i>
        Registrarme con Google
        <span style="font-size:10px;background:#FFF3CD;color:#92400E;padding:2px 7px;border-radius:999px;font-weight:700">PRÓXIMAMENTE</span>
      </button>

    </div>
  </div>

  <script>
    /* ── Modal ── */
    function showRegisterModal() {
      const fields = ['regNombre','regApellido','regCorreo','regDireccion'];
      fields.forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.value = ''; el.classList.remove('is-invalid'); }
      });
      ['nombreError','apellidoError','correoError','direccionError'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.remove('show');
      });
      document.getElementById('registerModal').classList.add('open');
      document.body.style.overflow = 'hidden';
      setTimeout(() => document.getElementById('regNombre').focus(), 260);
    }

    function closeModal() {
      document.getElementById('registerModal').classList.remove('open');
      document.body.style.overflow = '';
    }

    function handleOverlayClick(e) {
      if (e.target === document.getElementById('registerModal')) closeModal();
    }

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeModal();
    });

    /* Limpiar errores al escribir */
    [
      ['regNombre',   'nombreError'],
      ['regApellido', 'apellidoError'],
      ['regCorreo',   'correoError'],
      ['regDireccion','direccionError']
    ].forEach(([inputId, errId]) => {
      document.getElementById(inputId).addEventListener('input', () => {
        document.getElementById(inputId).classList.remove('is-invalid');
        document.getElementById(errId).classList.remove('show');
      });
    });

    /* Validación y registro */
    function setErr(inputId, errId, show) {
      document.getElementById(inputId).classList.toggle('is-invalid', show);
      document.getElementById(errId).classList.toggle('show', show);
    }

    function isValidEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function handleRegister() {
      const nombre    = document.getElementById('regNombre').value.trim();
      const apellido  = document.getElementById('regApellido').value.trim();
      const correo    = document.getElementById('regCorreo').value.trim();
      const direccion = document.getElementById('regDireccion').value.trim();

      let valid = true;
      setErr('regNombre',    'nombreError',    !nombre);    if (!nombre)    valid = false;
      setErr('regApellido',  'apellidoError',  !apellido);  if (!apellido)  valid = false;
      setErr('regCorreo',    'correoError',    !isValidEmail(correo));
      if (!isValidEmail(correo)) valid = false;
      setErr('regDireccion', 'direccionError', !direccion); if (!direccion) valid = false;
      if (!valid) return;

      const fullName = `${nombre} ${apellido}`;
      const initial  = (nombre[0] + apellido[0]).toUpperCase();

      /* Guardar en sessionStorage exclusivo de clientes */
      sessionStorage.setItem('laantigua_cliente', JSON.stringify({
        nombre, apellido, fullName, correo, direccion,
        initial, registrado: true,
        ts: Date.now()
      }));

      window.location.href = 'portal-cliente.php';
    }
  </script>
</body>
</html>
