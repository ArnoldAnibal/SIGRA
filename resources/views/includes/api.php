<?php
/**
 * SIGRA — Capa de comunicación con la API Laravel
 */

define('API_BASE', 'http://127.0.0.1:8000/api/v1');

/* ═══════════════════════════════════════════════════════════
   FUNCIÓN BASE
   ═══════════════════════════════════════════════════════════ */

function api_request(string $method, string $endpoint, array $data = [], ?string $token = null): array {
    $ch = curl_init(API_BASE . $endpoint);
    $headers = ['Content-Type: application/json', 'Accept: application/json'];
    if ($token) $headers[] = 'Authorization: Bearer ' . $token;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    if (!empty($data)) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    if ($curlError) return ['status' => 0, 'data' => ['message' => 'No se pudo conectar: ' . $curlError]];
    return ['status' => $httpCode, 'data' => json_decode($response, true) ?? []];
}

/* ═══════════════════════════════════════════════════════════
   AUTH
   ═══════════════════════════════════════════════════════════ */

function api_login_empleado(string $username, string $password): array {
    return api_request('POST', '/auth/empleados/login', ['login' => $username, 'password' => $password]);
}

function api_logout_empleado(string $token): array {
    return api_request('POST', '/auth/empleados/logout', [], $token);
}

/* ═══════════════════════════════════════════════════════════
   MESAS
   ═══════════════════════════════════════════════════════════ */

function api_mesas(?string $token = null): array {
    $res = api_request('GET', '/mesas', [], $token);
    if (isset($res['data']) && is_array($res['data'])) {
        $res['data'] = array_map(fn($m) => [
            'id'        => $m['id_mesa'],
            'numero'    => $m['numero_mesa'],
            'capacidad' => $m['capacidad'],
            'estado'    => strtoupper($m['estado']),
            'pedido'    => null,
            'reserva'   => null,
        ], $res['data']);
    }
    return $res;
}

function api_mesa(int $id, ?string $token = null): array {
    return api_request('GET', '/mesas/' . $id, [], $token);
}

function api_mesa_crear(array $data, string $token): array {
    return api_request('POST', '/mesas', $data, $token);
}

function api_mesa_actualizar(int $id, array $data, string $token): array {
    return api_request('PUT', '/mesas/' . $id, $data, $token);
}

function api_mesa_eliminar(int $id, string $token): array {
    return api_request('DELETE', '/mesas/' . $id, [], $token);
}

/* ═══════════════════════════════════════════════════════════
   PRODUCTOS / MENÚ
   ═══════════════════════════════════════════════════════════ */

// Mapa de id_categoria → key de categoría para el frontend
function _categoria_key(int $id_cat): string {
    $map = [
        3 => 'bebidas', 4 => 'postres', 5 => 'entradas',
        6 => 'desayunos', 7 => 'platos', 8 => 'platos',
        9 => 'entradas', 10 => 'platos', 11 => 'entradas',
        12 => 'platos', 13 => 'entradas', 14 => 'bebidas',
    ];
    return $map[$id_cat] ?? 'platos';
}

function api_productos(?string $token = null): array {
    $res = api_request('GET', '/productos-menu', [], $token);
    $raw = $res['data']['data'] ?? $res['data'] ?? [];
    if (is_array($raw)) {
        $res['data'] = array_map(fn($p) => [
            'id'          => $p['id_producto'],
            'categoria'   => _categoria_key((int)($p['id_categoria'] ?? $p['categoria_id'] ?? 7)),
            'nombre'      => $p['nombre'],
            'descripcion' => $p['descripcion'] ?? '',
            'precio'      => (float)($p['precio_venta'] ?? 0),
            'stock'       => $p['stock'] ?? 0,
            'stock_minimo'=> $p['stock_minimo'] ?? 0,
            'disponible'  => $p['disponible'] ?? 1,
            'img'         => $p['imagen_url'] ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&fit=crop&auto=format&q=70',
        ], $raw);
    }
    return $res;
}

function api_producto(int $id, ?string $token = null): array {
    return api_request('GET', '/productos-menu/' . $id, [], $token);
}

function api_producto_crear(array $data, string $token): array {
    return api_request('POST', '/productos-menu', $data, $token);
}

function api_producto_actualizar(int $id, array $data, string $token): array {
    return api_request('PUT', '/productos-menu/' . $id, $data, $token);
}

function api_producto_eliminar(int $id, string $token): array {
    return api_request('DELETE', '/productos-menu/' . $id, [], $token);
}

/* ═══════════════════════════════════════════════════════════
   CATEGORÍAS
   ═══════════════════════════════════════════════════════════ */

function api_categorias(?string $token = null): array {
    return api_request('GET', '/categorias-menu', [], $token);
}

function api_categoria_crear(array $data, string $token): array {
    return api_request('POST', '/categorias-menu', $data, $token);
}

function api_categoria_actualizar(int $id, array $data, string $token): array {
    return api_request('PUT', '/categorias-menu/' . $id, $data, $token);
}

function api_categoria_eliminar(int $id, string $token): array {
    return api_request('DELETE', '/categorias-menu/' . $id, [], $token);
}

/* ═══════════════════════════════════════════════════════════
   PEDIDOS
   ═══════════════════════════════════════════════════════════ */

function api_pedidos(?string $token = null): array {
    return api_request('GET', '/pedidos', [], $token);
}

function api_pedido(int $id, ?string $token = null): array {
    return api_request('GET', '/pedidos/' . $id, [], $token);
}

function api_pedido_crear(array $data, string $token): array {
    return api_request('POST', '/pedidos', $data, $token);
}

function api_pedido_actualizar(int $id, array $data, string $token): array {
    return api_request('PUT', '/pedidos/' . $id, $data, $token);
}

function api_pedido_eliminar(int $id, string $token): array {
    return api_request('DELETE', '/pedidos/' . $id, [], $token);
}

/* ═══════════════════════════════════════════════════════════
   DETALLES DE PEDIDO
   ═══════════════════════════════════════════════════════════ */

function api_detalles_pedido(?string $token = null): array {
    return api_request('GET', '/detalles-pedido', [], $token);
}

function api_detalle_crear(array $data, string $token): array {
    return api_request('POST', '/detalles-pedido', $data, $token);
}

function api_detalle_actualizar(int $id, array $data, string $token): array {
    return api_request('PUT', '/detalles-pedido/' . $id, $data, $token);
}

function api_detalle_eliminar(int $id, string $token): array {
    return api_request('DELETE', '/detalles-pedido/' . $id, [], $token);
}

/* ═══════════════════════════════════════════════════════════
   CLIENTES
   ═══════════════════════════════════════════════════════════ */

function api_clientes(string $token): array {
    return api_request('GET', '/clientes', [], $token);
}

function api_cliente(int $id, string $token): array {
    return api_request('GET', '/clientes/' . $id, [], $token);
}

function api_cliente_crear(array $data, string $token): array {
    return api_request('POST', '/clientes', $data, $token);
}

function api_cliente_actualizar(int $id, array $data, string $token): array {
    return api_request('PUT', '/clientes/' . $id, $data, $token);
}

function api_cliente_eliminar(int $id, string $token): array {
    return api_request('DELETE', '/clientes/' . $id, [], $token);
}

/* ═══════════════════════════════════════════════════════════
   EMPLEADOS
   ═══════════════════════════════════════════════════════════ */

function api_empleados(string $token): array {
    $res = api_request('GET', '/empleados', [], $token);
    $raw = $res['data']['data'] ?? $res['data'] ?? [];
    if (is_array($raw)) {
        $res['data'] = array_map(fn($e) => [
            'id'            => $e['id_empleado'],
            'nombre'        => $e['nombre'],
            'puesto'        => strtoupper($e['rol']),
            'username'      => $e['username'],
            'salarioBruto'  => 0,
            'igss'          => 0,
            'isr'           => 0,
        ], $raw);
    }
    return $res;
}

function api_empleado_crear(array $data, string $token): array {
    return api_request('POST', '/empleados', $data, $token);
}

function api_empleado_actualizar(int $id, array $data, string $token): array {
    return api_request('PUT', '/empleados/' . $id, $data, $token);
}

function api_empleado_eliminar(int $id, string $token): array {
    return api_request('DELETE', '/empleados/' . $id, [], $token);
}

/* ═══════════════════════════════════════════════════════════
   FACTURAS
   ═══════════════════════════════════════════════════════════ */

function api_facturas(string $token): array {
    return api_request('GET', '/facturas-electronicas', [], $token);
}

function api_factura(int $id, string $token): array {
    return api_request('GET', '/facturas-electronicas/' . $id, [], $token);
}

function api_factura_crear(array $data, string $token): array {
    return api_request('POST', '/facturas-electronicas', $data, $token);
}

function api_factura_actualizar(int $id, array $data, string $token): array {
    return api_request('PUT', '/facturas-electronicas/' . $id, $data, $token);
}

/* ═══════════════════════════════════════════════════════════
   PAGOS
   ═══════════════════════════════════════════════════════════ */

function api_pagos(string $token): array {
    return api_request('GET', '/pagos', [], $token);
}

function api_pago_crear(array $data, string $token): array {
    return api_request('POST', '/pagos', $data, $token);
}

/* ═══════════════════════════════════════════════════════════
   PLANILLAS
   ═══════════════════════════════════════════════════════════ */

function api_planillas(string $token): array {
    $res = api_request('GET', '/planillas', [], $token);
    $raw = $res['data']['data'] ?? $res['data'] ?? [];
    if (is_array($raw)) {
        $res['data'] = array_map(fn($p) => [
            'id'          => $p['id_planilla'],
            'empleadoId'  => $p['id_empleado'],
            'periodo'     => $p['periodo'] ?? '',
            'salarioNeto' => (float)($p['salario_neto'] ?? 0),
            'salarioBruto'=> (float)($p['salario_neto'] ?? 0),
            'igss'        => (float)($p['descuento'] ?? 0),
            'isr'         => 0,
            'fecha'       => $p['fecha_inicio'] ?? $p['created_at'] ?? '',
        ], $raw);
    }
    return $res;
}

function api_planilla_crear(array $data, string $token): array {
    return api_request('POST', '/planillas', $data, $token);
}

function api_planilla_actualizar(int $id, array $data, string $token): array {
    return api_request('PUT', '/planillas/' . $id, $data, $token);
}

function api_planilla_eliminar(int $id, string $token): array {
    return api_request('DELETE', '/planillas/' . $id, [], $token);
}

/* ═══════════════════════════════════════════════════════════
   ASISTENCIAS
   ═══════════════════════════════════════════════════════════ */

function api_asistencias(string $token): array {
    return api_request('GET', '/asistencias', [], $token);
}

function api_clock_in(string $token): array {
    return api_request('POST', '/asistencias', [], $token);
}

function api_clock_out(string $token): array {
    return api_request('PUT', '/asistencias/cerrar-jornada', [], $token);
}

/* ═══════════════════════════════════════════════════════════
   PROVEEDORES
   ═══════════════════════════════════════════════════════════ */

function api_proveedores(string $token): array {
    $res = api_request('GET', '/proveedores', [], $token);
    $raw = $res['data']['data'] ?? $res['data'] ?? [];
    if (is_array($raw)) {
        $res['data'] = array_map(fn($p) => [
            'id'       => $p['id_proveedor'],
            'nombre'   => $p['nombre'],
            'contacto' => $p['nombre'],
            'telefono' => $p['telefono'] ?? '',
            'email'    => '',
            'direccion'=> $p['direccion'] ?? '',
        ], $raw);
    }
    return $res;
}

function api_cuentas_por_pagar(string $token): array {
    $res = api_request('GET', '/cuentas-por-pagar', [], $token);
    $raw = $res['data']['data'] ?? $res['data'] ?? [];
    if (is_array($raw)) {
        $res['data'] = array_map(fn($c) => [
            'id'          => $c['id'],
            'proveedorId' => $c['id_proveedor'],
            'concepto'    => $c['descripcion'] ?? '',
            'monto'       => (float)($c['monto'] ?? 0),
            'fechaVence'  => $c['fecha_vencimiento'] ?? '',
            'estado'      => strtoupper($c['estado'] ?? 'PENDIENTE'),
        ], $raw);
    }
    return $res;
}
function api_empresas_credito(string $token): array {
    $res = api_request('GET', '/clientes', [], $token);
    $raw = $res['data']['data'] ?? $res['data'] ?? [];
    $empresas = array_values(array_filter($raw, fn($c) => $c['tipo_cliente'] === 'Empresa'));
    $res['data'] = array_map(fn($e) => [
        'id'            => $e['id_cliente'],
        'nombre'        => $e['nombre'],
        'nit'           => $e['nit'] ?? 'CF',
        'contacto'      => $e['nombre'],
        'telefono'      => $e['telefono'] ?? '',
        'limiteCredito' => (float)($e['limite_credito'] ?? 0),
        'utilizado'     => (float)($e['saldo_credito_actual'] ?? 0),
        'estado'        => $e['estado'] ?? 'Activo',
    ], $empresas);
    return $res;
}