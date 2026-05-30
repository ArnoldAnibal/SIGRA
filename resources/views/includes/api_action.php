<?php
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$token = sigra_token();

if (!$token) {
    echo json_encode(['message' => 'No autenticado']);
    exit;
}

if ($action === 'clock_in') {
    $res = api_clock_in($token);
    echo json_encode($res['data']);
} elseif ($action === 'clock_out') {
    $res = api_clock_out($token);
    echo json_encode($res['data']);
} else {
    echo json_encode(['message' => 'Acción no válida']);
}