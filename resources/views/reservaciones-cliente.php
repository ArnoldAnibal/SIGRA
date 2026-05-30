<?php
require_once __DIR__ . '/includes/bootstrap.php';
// DEPRECADO: la opción "Reservar mesa" fue removida del flujo del cliente.
// El cliente ahora hace pedido en línea desde menu-cliente.php (entra a admin/cocina).
// Este archivo se conserva por compatibilidad; cualquier acceso redirige al menú.
header('Location: menu-cliente.php');
exit;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservaciones — Restaurante La Antigua</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root {
      --navy:  #1A2E4A;
      --amber: #E8A020;
      --cream: #FAF7F2;
      --dark:  #0D1B2A;
      --text:  #2D3748;
      --muted: #6B7280;
      --border:#E5E7EB;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--cream); color: var(--text); min-height: 100vh;
    }

    /* ── NAVBAR ── */
    .navbar {
      position: sticky; top: 0; z-index: 100;
      background: var(--navy); padding: 0 5%; height: 62px;
      display: flex; align-items: center; justify-content: space-between;
      box-shadow: 0 2px 16px rgba(0,0,0,.25);
    }
    .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .nav-brand-icon {
      width: 34px; height: 34px; border-radius: 9px;
      background: var(--amber); display: flex;
      align-items: center; justify-content: center;
    }
    .nav-brand-icon i { color: #fff; font-size: 17px; }
    .nav-brand-text { color: #fff; font-size: 17px; font-weight: 800; }
    .nav-links { display: flex; gap: 4px; }
    .nav-links a {
      color: rgba(255,255,255,.75); text-decoration: none;
      font-size: 13px; font-weight: 500; padding: 7px 13px;
      border-radius: 8px; transition: color .15s, background .15s;
    }
    .nav-links a:hover { color: #fff; background: rgba(255,255,255,.1); }
    .nav-links a.active { color: var(--amber); }
    .nav-user-badge {
      display: flex; align-items: center; gap: 8px;
      background: rgba(255,255,255,.1); border-radius: 18px;
      padding: 4px 12px 4px 5px;
    }
    .nav-avatar {
      width: 28px; height: 28px; border-radius: 50%;
      background: var(--amber); display: flex;
      align-items: center; justify-content: center;
      font-size: 11px; font-weight: 700; color: #fff;
    }
    .nav-user-name { color: rgba(255,255,255,.85); font-size: 12.5px; font-weight: 500; }

    /* ── HERO SMALL ── */
    .page-hero {
      background: linear-gradient(135deg, var(--navy) 0%, #243d5f 60%, #1a3a2a 100%);
      padding: 52px 5% 44px; text-align: center; position: relative; overflow: hidden;
    }
    .page-hero::before {
      content: '';
      position: absolute; inset: 0;
      background-image: url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1200&fit=crop&auto=format&q=60');
      background-size: cover; background-position: center;
      opacity: .1;
    }
    .page-hero-tag {
      display: inline-flex; align-items: center; gap: 7px;
      background: rgba(232,160,32,.15); border: 1px solid rgba(232,160,32,.35);
      color: var(--amber); font-size: 12px; font-weight: 700; letter-spacing: .6px;
      text-transform: uppercase; padding: 5px 16px; border-radius: 999px;
      margin-bottom: 16px; position: relative; z-index: 1;
    }
    .page-hero h1 {
      color: #fff; font-size: clamp(26px, 5vw, 44px);
      font-weight: 900; letter-spacing: -.5px;
      margin-bottom: 10px; position: relative; z-index: 1;
    }
    .page-hero p {
      color: rgba(255,255,255,.65); font-size: 15px;
      position: relative; z-index: 1;
    }

    /* ── LAYOUT ── */
    .page-wrap {
      max-width: 1080px; margin: 0 auto;
      padding: 48px 5% 80px;
      display: grid; grid-template-columns: 1.6fr 1fr; gap: 36px;
      align-items: start;
    }

    /* ── FORM CARD ── */
    .form-card {
      background: #fff; border-radius: 22px;
      box-shadow: 0 4px 24px rgba(0,0,0,.09);
      overflow: hidden;
    }
    .form-card-header {
      background: var(--navy); padding: 24px 28px;
      display: flex; align-items: center; gap: 14px;
    }
    .form-header-icon {
      width: 46px; height: 46px; border-radius: 12px;
      background: rgba(232,160,32,.2);
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; color: var(--amber);
    }
    .form-card-header h2 { color: #fff; font-size: 18px; font-weight: 700; }
    .form-card-header p  { color: rgba(255,255,255,.6); font-size: 12.5px; margin-top: 3px; }

    .form-body { padding: 28px; }

    /* Sección dentro del form */
    .form-section { margin-bottom: 28px; }
    .form-section-title {
      font-size: 12.5px; font-weight: 700; color: var(--muted);
      text-transform: uppercase; letter-spacing: .7px;
      margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
    }
    .form-section-title::after {
      content: ''; flex: 1; height: 1px; background: var(--border);
    }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-group { margin-bottom: 16px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label {
      display: block; font-size: 13px; font-weight: 600;
      color: var(--text); margin-bottom: 6px;
    }
    .form-label .req { color: #C62828; margin-left: 2px; }
    .form-control {
      width: 100%; height: 44px; padding: 0 14px;
      border: 1.5px solid var(--border); border-radius: 10px;
      font-size: 13.5px; color: var(--text); outline: none; background: #fff;
      transition: border-color .15s, box-shadow .15s;
      appearance: none;
    }
    .form-control:focus {
      border-color: var(--navy);
      box-shadow: 0 0 0 3px rgba(26,46,74,.1);
    }
    .form-control.is-invalid {
      border-color: #C62828;
      box-shadow: 0 0 0 3px rgba(198,40,40,.08);
    }
    .form-control[readonly] {
      background: #F9FAFB; color: var(--muted); cursor: default;
    }
    textarea.form-control {
      height: auto; padding: 12px 14px; resize: vertical; min-height: 90px;
    }
    .form-error {
      font-size: 11.5px; color: #C62828; margin-top: 4px; display: none;
    }
    .form-error.show { display: block; }
    .form-hint { font-size: 11.5px; color: var(--muted); margin-top: 4px; }

    /* Selector de personas */
    .people-selector {
      display: flex; align-items: center; gap: 12px;
    }
    .people-btn {
      width: 36px; height: 36px; border-radius: 50%;
      background: var(--cream); border: 2px solid var(--border);
      font-size: 18px; font-weight: 700; color: var(--navy);
      cursor: pointer; display: flex; align-items: center; justify-content: center;
      transition: background .15s, border-color .15s;
      flex-shrink: 0;
    }
    .people-btn:hover { background: #E5E7EB; border-color: #9CA3AF; }
    .people-display {
      font-size: 24px; font-weight: 900; color: var(--navy);
      min-width: 40px; text-align: center;
    }
    .people-label { font-size: 12.5px; color: var(--muted); }

    /* Horarios */
    .time-grid {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;
    }
    .time-btn {
      padding: 9px 4px; border-radius: 9px;
      border: 1.5px solid var(--border); background: #fff;
      font-size: 13px; font-weight: 600; color: var(--muted);
      cursor: pointer; text-align: center;
      transition: all .15s;
    }
    .time-btn:hover { border-color: var(--navy); color: var(--navy); }
    .time-btn.selected {
      background: var(--navy); color: #fff; border-color: var(--navy);
    }
    .time-btn.disabled {
      opacity: .4; cursor: not-allowed;
      background: #F3F4F6;
    }

    /* Submit */
    .btn-submit {
      width: 100%; height: 52px;
      background: linear-gradient(135deg, var(--amber), #c98418);
      color: var(--navy); border: none; border-radius: 13px;
      font-size: 16px; font-weight: 800; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 9px;
      box-shadow: 0 6px 20px rgba(232,160,32,.35);
      transition: transform .15s, box-shadow .15s, opacity .15s;
      margin-top: 8px;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(232,160,32,.45); }
    .btn-submit:active { transform: scale(.97); }
    .btn-submit:disabled { opacity: .55; cursor: not-allowed; transform: none; }

    /* ── SIDEBAR INFO ── */
    .sidebar { display: flex; flex-direction: column; gap: 20px; }

    .info-card {
      background: #fff; border-radius: 18px;
      box-shadow: 0 3px 16px rgba(0,0,0,.07);
      overflow: hidden;
    }
    .info-card-header {
      background: linear-gradient(135deg, var(--navy), #243d5f);
      padding: 18px 20px;
    }
    .info-card-header h3 {
      color: #fff; font-size: 15px; font-weight: 700;
      display: flex; align-items: center; gap: 8px;
    }
    .info-card-header h3 i { color: var(--amber); }
    .info-card-body { padding: 18px 20px; }

    .info-row-item {
      display: flex; align-items: flex-start; gap: 12px;
      padding: 12px 0; border-bottom: 1px solid var(--border);
    }
    .info-row-item:last-child { border-bottom: none; padding-bottom: 0; }
    .info-icon-sm {
      width: 34px; height: 34px; border-radius: 9px;
      background: #FFF8E1; display: flex;
      align-items: center; justify-content: center;
      font-size: 15px; color: var(--amber); flex-shrink: 0;
    }
    .info-row-label { font-size: 11px; color: var(--muted); font-weight: 500; text-transform: uppercase; letter-spacing: .4px; }
    .info-row-val { font-size: 13.5px; color: var(--text); font-weight: 600; margin-top: 1px; }

    /* Horario table */
    .schedule-row {
      display: flex; justify-content: space-between; align-items: center;
      padding: 9px 0; border-bottom: 1px solid var(--border);
      font-size: 13px;
    }
    .schedule-row:last-child { border-bottom: none; }
    .schedule-day { color: var(--text); font-weight: 500; }
    .schedule-time { color: var(--muted); }
    .schedule-row.today .schedule-day { color: var(--amber); font-weight: 700; }
    .schedule-row.today .schedule-time { color: var(--amber); }

    /* Políticas */
    .policy-item {
      display: flex; gap: 10px; padding: 10px 0;
      border-bottom: 1px solid var(--border);
      font-size: 12.5px; color: var(--muted);
    }
    .policy-item:last-child { border-bottom: none; }
    .policy-item i { color: var(--amber); font-size: 14px; flex-shrink: 0; margin-top: 2px; }

    /* ══ CONFIRMACIÓN ══ */
    .confirm-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,.55); z-index: 300;
      align-items: center; justify-content: center; padding: 20px;
    }
    .confirm-overlay.open {
      display: flex; animation: fadeIn .22s ease;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .confirm-box {
      background: #fff; border-radius: 24px;
      padding: 40px 36px; max-width: 460px; width: 100%;
      text-align: center;
      box-shadow: 0 32px 72px rgba(0,0,0,.3);
      animation: popIn .28s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes popIn {
      from { opacity: 0; transform: scale(.86); }
      to   { opacity: 1; transform: scale(1); }
    }
    .confirm-icon {
      width: 76px; height: 76px; border-radius: 50%;
      background: #E8F5E9; margin: 0 auto 20px;
      display: flex; align-items: center; justify-content: center;
      font-size: 34px; color: #2E7D32;
      animation: scaleIn .4s cubic-bezier(.34,1.56,.64,1) .1s both;
    }
    @keyframes scaleIn {
      from { transform: scale(0); }
      to   { transform: scale(1); }
    }
    .confirm-box h3 { font-size: 22px; font-weight: 800; color: var(--navy); margin-bottom: 10px; }
    .confirm-box p  { font-size: 14px; color: var(--muted); line-height: 1.65; margin-bottom: 8px; }
    .confirm-detail {
      background: var(--cream); border-radius: 12px;
      padding: 16px; margin: 20px 0; text-align: left;
    }
    .confirm-detail-row {
      display: flex; justify-content: space-between;
      padding: 6px 0; font-size: 13px;
      border-bottom: 1px solid var(--border);
    }
    .confirm-detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .confirm-detail-row span:first-child { color: var(--muted); }
    .confirm-detail-row span:last-child  { font-weight: 600; color: var(--navy); }
    .btn-confirm-done {
      width: 100%; height: 48px;
      background: var(--navy); color: #fff;
      border: none; border-radius: 12px;
      font-size: 15px; font-weight: 700; cursor: pointer;
      margin-top: 8px; transition: opacity .15s;
    }
    .btn-confirm-done:hover { opacity: .88; }

    /* ── RESPONSIVE ── */
    @media (max-width: 820px) {
      .page-wrap { grid-template-columns: 1fr; }
      .sidebar { order: -1; }
    }
    @media (max-width: 600px) {
      .form-row { grid-template-columns: 1fr; }
      .time-grid { grid-template-columns: repeat(3, 1fr); }
      .nav-links, .nav-user-badge { display: none; }
    }
    @media (max-width: 380px) {
      .time-grid { grid-template-columns: repeat(2, 1fr); }
    }
  </style>
</head>
<body>

  <!-- ══ NAVBAR ══ -->
  <nav class="navbar">
    <a class="nav-brand" href="portal-cliente.php">
      <div class="nav-brand-icon"><i class="bi bi-shop"></i></div>
      <span class="nav-brand-text">La Antigua</span>
    </a>
    <div class="nav-links">
      <a href="portal-cliente.php">Inicio</a>
      <a href="menu-cliente.php">Menú</a>
      <a href="portal-cliente.php#galeria">Galería</a>
      <a href="reservaciones-cliente.php" class="active">Reservaciones</a>
    </div>
    <div class="nav-user-badge" id="navUser" style="display:none">
      <div class="nav-avatar" id="navAvatar">?</div>
      <span class="nav-user-name" id="navUserName">Cliente</span>
    </div>
  </nav>

  <!-- ══ HERO ══ -->
  <div class="page-hero">
    <div class="page-hero-tag"><i class="bi bi-calendar-check"></i> Reservaciones</div>
    <h1>Reserva tu mesa</h1>
    <p>Asegura tu lugar en La Antigua · Grupos, celebraciones y eventos especiales bienvenidos</p>
  </div>

  <!-- ══ CONTENIDO ══ -->
  <div class="page-wrap">

    <!-- FORMULARIO -->
    <div class="form-card">
      <div class="form-card-header">
        <div class="form-header-icon"><i class="bi bi-calendar-plus"></i></div>
        <div>
          <h2>Nueva Reservación</h2>
          <p>Completa el formulario y te confirmaremos tu reserva</p>
        </div>
      </div>
      <div class="form-body">

        <!-- Sección: Tus Datos -->
        <div class="form-section">
          <div class="form-section-title"><i class="bi bi-person"></i> Tus datos</div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="rNombre">Nombre <span class="req">*</span></label>
              <input type="text" id="rNombre" class="form-control" placeholder="Juan">
              <div class="form-error" id="err-rNombre">El nombre es requerido.</div>
            </div>
            <div class="form-group">
              <label class="form-label" for="rApellido">Apellido <span class="req">*</span></label>
              <input type="text" id="rApellido" class="form-control" placeholder="García">
              <div class="form-error" id="err-rApellido">El apellido es requerido.</div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="rCorreo">Correo electrónico <span class="req">*</span></label>
              <input type="email" id="rCorreo" class="form-control" placeholder="tu@correo.com">
              <div class="form-error" id="err-rCorreo">Ingrese un correo válido.</div>
            </div>
            <div class="form-group">
              <label class="form-label" for="rTelefono">Teléfono <span class="req">*</span></label>
              <input type="tel" id="rTelefono" class="form-control" placeholder="+502 5555-0000">
              <div class="form-error" id="err-rTelefono">El teléfono es requerido.</div>
            </div>
          </div>
        </div>

        <!-- Sección: Fecha y Hora -->
        <div class="form-section">
          <div class="form-section-title"><i class="bi bi-calendar3"></i> Fecha y hora</div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="rFecha">Fecha <span class="req">*</span></label>
              <input type="date" id="rFecha" class="form-control">
              <div class="form-error" id="err-rFecha">Selecciona una fecha válida.</div>
            </div>
            <div class="form-group">
              <label class="form-label">Número de personas <span class="req">*</span></label>
              <div class="people-selector">
                <button type="button" class="people-btn" id="peopleMinus" onclick="changePeople(-1)">−</button>
                <div>
                  <div class="people-display" id="peopleCount">2</div>
                  <div class="people-label">personas</div>
                </div>
                <button type="button" class="people-btn" id="peoplePlus" onclick="changePeople(1)">+</button>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Hora de la reservación <span class="req">*</span></label>
            <div class="time-grid" id="timeGrid">
              <button type="button" class="time-btn" onclick="selectTime('11:00', this)">11:00</button>
              <button type="button" class="time-btn" onclick="selectTime('11:30', this)">11:30</button>
              <button type="button" class="time-btn" onclick="selectTime('12:00', this)">12:00</button>
              <button type="button" class="time-btn" onclick="selectTime('12:30', this)">12:30</button>
              <button type="button" class="time-btn" onclick="selectTime('13:00', this)">13:00</button>
              <button type="button" class="time-btn" onclick="selectTime('13:30', this)">13:30</button>
              <button type="button" class="time-btn" onclick="selectTime('14:00', this)">14:00</button>
              <button type="button" class="time-btn" onclick="selectTime('14:30', this)">14:30</button>
              <button type="button" class="time-btn" onclick="selectTime('18:00', this)">18:00</button>
              <button type="button" class="time-btn" onclick="selectTime('18:30', this)">18:30</button>
              <button type="button" class="time-btn" onclick="selectTime('19:00', this)">19:00</button>
              <button type="button" class="time-btn" onclick="selectTime('19:30', this)">19:30</button>
              <button type="button" class="time-btn" onclick="selectTime('20:00', this)">20:00</button>
              <button type="button" class="time-btn" onclick="selectTime('20:30', this)">20:30</button>
              <button type="button" class="time-btn" onclick="selectTime('21:00', this)">21:00</button>
              <button type="button" class="time-btn" onclick="selectTime('21:30', this)">21:30</button>
            </div>
            <div class="form-error" id="err-hora" style="margin-top:8px">Selecciona una hora.</div>
          </div>
        </div>

        <!-- Sección: Preferencias -->
        <div class="form-section">
          <div class="form-section-title"><i class="bi bi-chat-dots"></i> Detalles adicionales</div>

          <div class="form-group">
            <label class="form-label" for="rOcasion">Ocasión especial</label>
            <select id="rOcasion" class="form-control">
              <option value="">— Ninguna en particular —</option>
              <option value="cumpleanos">🎂 Cumpleaños</option>
              <option value="aniversario">💍 Aniversario</option>
              <option value="cena-romantica">🌹 Cena romántica</option>
              <option value="reunion-negocios">💼 Reunión de negocios</option>
              <option value="celebracion-familiar">👨‍👩‍👧‍👦 Celebración familiar</option>
              <option value="despedida">🥂 Despedida</option>
              <option value="otro">Otra ocasión</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="rZona">Preferencia de zona</label>
            <select id="rZona" class="form-control">
              <option value="sin-preferencia">Sin preferencia</option>
              <option value="salon-principal">Salón principal (interior)</option>
              <option value="terraza">Terraza exterior</option>
              <option value="privada">Área privada (mín. 8 personas)</option>
              <option value="barra">Cerca de la barra</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="rNotas">Solicitudes especiales</label>
            <textarea id="rNotas" class="form-control"
              placeholder="Ej: alergias alimentarias, decoración especial, silla de bebé, necesidades especiales..."></textarea>
            <div class="form-hint">Haremos todo lo posible para atender tus solicitudes.</div>
          </div>
        </div>

        <button type="button" class="btn-submit" id="submitBtn" onclick="submitReservation()">
          <i class="bi bi-calendar-check"></i> Confirmar Reservación
        </button>

      </div>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar">

      <!-- Información del restaurante -->
      <div class="info-card">
        <div class="info-card-header">
          <h3><i class="bi bi-shop"></i> La Antigua</h3>
        </div>
        <div class="info-card-body">
          <div class="info-row-item">
            <div class="info-icon-sm"><i class="bi bi-geo-alt-fill"></i></div>
            <div>
              <div class="info-row-label">Dirección</div>
              <div class="info-row-val">3a Calle y 7a Avenida,<br>Puerto Barrios, Izabal</div>
            </div>
          </div>
          <div class="info-row-item">
            <div class="info-icon-sm"><i class="bi bi-telephone-fill"></i></div>
            <div>
              <div class="info-row-label">Teléfono</div>
              <div class="info-row-val">+502 7948-0000</div>
            </div>
          </div>
          <div class="info-row-item">
            <div class="info-icon-sm"><i class="bi bi-envelope-fill"></i></div>
            <div>
              <div class="info-row-label">Correo</div>
              <div class="info-row-val">reservas@laantiguagt.com</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Horarios -->
      <div class="info-card">
        <div class="info-card-header">
          <h3><i class="bi bi-clock"></i> Horario de atención</h3>
        </div>
        <div class="info-card-body">
          <div class="schedule-row" id="sch-lunes">
            <span class="schedule-day">Lunes – Viernes</span>
            <span class="schedule-time">11:00 – 22:00</span>
          </div>
          <div class="schedule-row" id="sch-sabado">
            <span class="schedule-day">Sábado</span>
            <span class="schedule-time">10:00 – 23:00</span>
          </div>
          <div class="schedule-row" id="sch-domingo">
            <span class="schedule-day">Domingo</span>
            <span class="schedule-time">10:00 – 21:00</span>
          </div>
          <div class="schedule-row">
            <span class="schedule-day" style="color:#C62828">Días festivos</span>
            <span class="schedule-time">Horario especial</span>
          </div>
        </div>
      </div>

      <!-- Políticas -->
      <div class="info-card">
        <div class="info-card-header">
          <h3><i class="bi bi-info-circle"></i> Políticas de reservación</h3>
        </div>
        <div class="info-card-body">
          <div class="policy-item">
            <i class="bi bi-check-circle-fill"></i>
            <span>Las reservaciones deben hacerse con al menos <strong>2 horas de anticipación</strong>.</span>
          </div>
          <div class="policy-item">
            <i class="bi bi-check-circle-fill"></i>
            <span>Aguardamos tu llegada <strong>15 minutos</strong> después de la hora reservada.</span>
          </div>
          <div class="policy-item">
            <i class="bi bi-check-circle-fill"></i>
            <span>Para grupos de <strong>8 o más personas</strong>, contáctanos directamente por teléfono.</span>
          </div>
          <div class="policy-item">
            <i class="bi bi-check-circle-fill"></i>
            <span>Cancelaciones con al menos <strong>4 horas de anticipación</strong>, por favor.</span>
          </div>
          <div class="policy-item">
            <i class="bi bi-check-circle-fill"></i>
            <span>Recibirás <strong>confirmación por correo</strong> dentro de los 30 minutos siguientes.</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- ══ MODAL CONFIRMACIÓN ══ -->
  <div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
      <div class="confirm-icon"><i class="bi bi-check-lg"></i></div>
      <h3>¡Reservación enviada!</h3>
      <p>Tu solicitud fue recibida correctamente. Te confirmaremos por correo electrónico en menos de 30 minutos.</p>

      <div class="confirm-detail" id="confirmDetail">
        <!-- Llenado por JS -->
      </div>

      <p style="font-size:12.5px;color:#9CA3AF">¿Dudas o cambios? Llámanos al <strong style="color:#374151">+502 7948-0000</strong></p>

      <button class="btn-confirm-done" onclick="goHome()">
        <i class="bi bi-house"></i> Volver al inicio
      </button>
    </div>
  </div>

  <script>
    /* ── Cargar usuario desde sessionStorage ── */
    (function() {
      try {
        const raw = sessionStorage.getItem('laantigua_cliente');
        if (!raw) return;
        const u = JSON.parse(raw);

        /* Mostrar en navbar */
        const el = document.getElementById('navUser');
        if (el) {
          document.getElementById('navAvatar').textContent   = u.initial || '?';
          document.getElementById('navUserName').textContent = u.nombre  || 'Cliente';
          el.style.display = 'flex';
        }
        /* Pre-llenar campos del formulario */
        const n = document.getElementById('rNombre');
        const a = document.getElementById('rApellido');
        const c = document.getElementById('rCorreo');
        if (n && u.nombre)    n.value = u.nombre;
        if (a && u.apellido)  a.value = u.apellido;
        if (c && u.correo)    c.value = u.correo;
      } catch(e) {}
    })();

    /* ── Fecha mínima: mañana ── */
    (function() {
      const today = new Date();
      today.setDate(today.getDate() + 1);
      const min = today.toISOString().split('T')[0];
      const input = document.getElementById('rFecha');
      if (input) { input.min = min; input.value = min; }
    })();

    /* ── Personas ── */
    let people = 2;
    function changePeople(delta) {
      people = Math.max(1, Math.min(20, people + delta));
      document.getElementById('peopleCount').textContent = people;
      document.getElementById('peopleMinus').disabled = people <= 1;
      document.getElementById('peoplePlus').disabled  = people >= 20;
    }

    /* ── Hora seleccionada ── */
    let selectedTime = null;
    function selectTime(t, btn) {
      if (btn.classList.contains('disabled')) return;
      document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      selectedTime = t;
      document.getElementById('err-hora').classList.remove('show');
    }

    /* ── Limpiar error al escribir ── */
    ['rNombre','rApellido','rCorreo','rTelefono','rFecha'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.addEventListener('input', () => {
        el.classList.remove('is-invalid');
        const err = document.getElementById('err-' + id);
        if (err) err.classList.remove('show');
      });
    });

    /* ── Validar y enviar ── */
    function setErr(id, show, msg) {
      const input = document.getElementById(id);
      const err   = document.getElementById('err-' + id);
      if (input) input.classList.toggle('is-invalid', show);
      if (err)   { err.classList.toggle('show', show); if (msg) err.textContent = msg; }
    }

    function isValidEmail(e) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e); }

    function submitReservation() {
      const nombre    = document.getElementById('rNombre').value.trim();
      const apellido  = document.getElementById('rApellido').value.trim();
      const correo    = document.getElementById('rCorreo').value.trim();
      const telefono  = document.getElementById('rTelefono').value.trim();
      const fecha     = document.getElementById('rFecha').value;
      const ocasion   = document.getElementById('rOcasion').value;
      const zona      = document.getElementById('rZona').value;

      let valid = true;

      setErr('rNombre',   !nombre,              'El nombre es requerido.');
      if (!nombre)   valid = false;
      setErr('rApellido', !apellido,             'El apellido es requerido.');
      if (!apellido) valid = false;
      setErr('rCorreo',   !isValidEmail(correo), 'Ingresa un correo válido.');
      if (!isValidEmail(correo)) valid = false;
      setErr('rTelefono', !telefono,             'El teléfono es requerido.');
      if (!telefono) valid = false;
      setErr('rFecha',    !fecha,                'Selecciona una fecha.');
      if (!fecha)    valid = false;

      if (!selectedTime) {
        document.getElementById('err-hora').classList.add('show');
        valid = false;
      }
      if (!valid) return;

      /* Formatear fecha */
      const fechaObj  = new Date(fecha + 'T00:00:00');
      const diasSem   = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
      const meses     = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
      const fechaStr  = `${diasSem[fechaObj.getDay()]}, ${fechaObj.getDate()} de ${meses[fechaObj.getMonth()]} de ${fechaObj.getFullYear()}`;

      const zonaLabels = {
        'sin-preferencia': 'Sin preferencia',
        'salon-principal': 'Salón principal',
        'terraza':         'Terraza exterior',
        'privada':         'Área privada',
        'barra':           'Cerca de la barra'
      };
      const ocasionLabels = {
        '':                   '—',
        'cumpleanos':          '🎂 Cumpleaños',
        'aniversario':         '💍 Aniversario',
        'cena-romantica':      '🌹 Cena romántica',
        'reunion-negocios':    '💼 Reunión de negocios',
        'celebracion-familiar':'👨‍👩‍👧‍👦 Celebración familiar',
        'despedida':           '🥂 Despedida',
        'otro':                'Otra ocasión'
      };

      /* Generar código de confirmación */
      const code = 'LA-' + Math.random().toString(36).slice(2,7).toUpperCase();

      /* Mostrar detalle en modal */
      const detail = document.getElementById('confirmDetail');
      detail.innerHTML = `
        <div class="confirm-detail-row">
          <span>Código</span><span>${code}</span>
        </div>
        <div class="confirm-detail-row">
          <span>Nombre</span><span>${nombre} ${apellido}</span>
        </div>
        <div class="confirm-detail-row">
          <span>Fecha</span><span>${fechaStr}</span>
        </div>
        <div class="confirm-detail-row">
          <span>Hora</span><span>${selectedTime} hrs</span>
        </div>
        <div class="confirm-detail-row">
          <span>Personas</span><span>${people} persona${people !== 1 ? 's' : ''}</span>
        </div>
        <div class="confirm-detail-row">
          <span>Zona</span><span>${zonaLabels[zona] || zona}</span>
        </div>
        ${ocasion ? `<div class="confirm-detail-row">
          <span>Ocasión</span><span>${ocasionLabels[ocasion] || ocasion}</span>
        </div>` : ''}
        <div class="confirm-detail-row">
          <span>Confirmación a</span><span>${correo}</span>
        </div>
      `;

      /* Guardar en sessionStorage */
      const reserva = {
        code, nombre, apellido, correo, telefono,
        fecha, fechaStr, hora: selectedTime,
        personas: people, zona, ocasion,
        ts: Date.now()
      };
      try {
        const prev = JSON.parse(sessionStorage.getItem('laantigua_reservas') || '[]');
        prev.push(reserva);
        sessionStorage.setItem('laantigua_reservas', JSON.stringify(prev));
      } catch(e) {}

      /* Mostrar modal confirmación */
      document.getElementById('confirmOverlay').classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function goHome() {
      window.location.href = 'portal-cliente.php';
    }
  </script>
</body>
</html>
