<?php
/**
 * SIGRA — Bootstrap del frontend
 * Inicializa sesión, carga la API y provee helpers de autenticación.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/api.php';

/* ═══════════════════════════════════════════════════════════
   HELPERS DE SESIÓN
   ═══════════════════════════════════════════════════════════ */

/** Devuelve el usuario autenticado o null. */
function sigra_current_user(): ?array {
    return $_SESSION['sigra_user'] ?? null;
}

/** Devuelve el token Bearer del usuario o null. */
function sigra_token(): ?string {
    return $_SESSION['sigra_user']['token'] ?? null;
}

/**
 * Protege una página: si el usuario no está logueado o no tiene
 * uno de los roles permitidos, redirige al login.
 *
 * Uso: sigra_require_role(['ADMIN', 'CAJERO']);
 */
function sigra_require_role(array $roles = []): void {
    $user = sigra_current_user();

    if ($user === null) {
        header('Location: /sigra/login-empleados.php');
        exit;
    }

    if (!empty($roles) && !in_array($user['role'], $roles, true)) {
        header('Location: /sigra/login-empleados.php?error=forbidden');
        exit;
    }
}

/**
 * Inyecta un bloque JSON en el HTML para que el JS del frontend
 * pueda leer rutas, datos del usuario, etc.
 *
 * Uso: <?= sigra_bootstrap_script(['routesByRole' => $ROUTES]) ?>
 */
function sigra_bootstrap_script(array $data = []): string {
    $user = sigra_current_user();
    $payload = array_merge($data, [
        'user' => $user ? [
            'id'       => $user['id']       ?? null,
            'name'     => $user['name']     ?? '',
            'role'     => $user['role']     ?? '',
            'username' => $user['username'] ?? '',
            'initial'  => $user['initial']  ?? '',
        ] : null,
    ]);

    $json = json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP);
    return '<script id="sigra-bootstrap" type="application/json">' . $json . '</script>';
}

/* ═══════════════════════════════════════════════════════════
   PROCESAMIENTO DE LOGIN (llamado desde login-empleados.php)
   ═══════════════════════════════════════════════════════════ */

/**
 * Intenta hacer login contra la API Laravel.
 * Devuelve null si OK (y ya habrá redirigido), o un string de error.
 */
function sigra_process_login(string $username, string $password, array $routesByRole): ?string {
    $res = api_login_empleado($username, $password);

    if ($res['status'] === 0) {
        return 'No se pudo conectar con el servidor. ¿Está corriendo Laravel?';
    }

    if ($res['status'] === 200 && isset($res['data']['token'])) {
        $emp = $res['data']['empleado'];

        // Mapear rol de Laravel al rol en mayúsculas que usa el frontend
        $rolMap = [
            'Administrador' => 'ADMIN',
            'Mesero'        => 'MESERO',
            'Cocinero'      => 'COCINA',
            'Cajero'        => 'CAJERO',
        ];

        $role    = $rolMap[$emp['rol']] ?? strtoupper($emp['rol']);
        $nombre  = $emp['nombre'] ?? $username;
        $parts   = explode(' ', $nombre);
        $initial = '';
        foreach ($parts as $p) {
            $initial .= mb_strtoupper(mb_substr($p, 0, 1));
            if (mb_strlen($initial) >= 2) break;
        }

        $_SESSION['sigra_user'] = [
            'id'       => $emp['id_empleado'],
            'name'     => $nombre,
            'role'     => $role,
            'username' => $emp['username'],
            'initial'  => $initial,
            'token'    => $res['data']['token'],
            'isGuest'  => false,
        ];

        $dest = $routesByRole[$role] ?? 'dashboard.php';
        header('Location: ' . $dest);
        exit;
    }

    // Error del servidor (401, 422, etc.)
    return $res['data']['message'] ?? 'Usuario o contraseña incorrectos.';
}
