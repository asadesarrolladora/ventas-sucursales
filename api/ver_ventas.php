<?php
session_start();
include '../config.php';
header('Content-Type: application/json');

// 1. Verificación de Sesión Básica
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // No autorizado
    echo json_encode(["status" => "error", "message" => "Sesión no iniciada"]);
    exit;
}

// 2. PROTECCIÓN EXTRA POR ROL
// Solo permitimos el paso si el rol guardado en la sesión es 'admin'
if ($_SESSION['rol'] !== 'admin') {
    http_response_code(403); // Prohibido (Forbidden)
    echo json_encode([
        "status" => "error", 
        "message" => "Acceso denegado: No tienes permisos de administrador para ver estos datos."
    ]);
    exit;
}
session_start();
include '../config.php';
header('Content-Type: application/json');

// Protección de Seguridad
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "No autorizado"]));
}

try {
    // Consulta que une ventas con nombres de productos
    $query = "SELECT v.id_venta, v.fecha, v.total, p.nombre as producto 
              FROM ventas v
              JOIN detalle_ventas dv ON v.id_venta = dv.id_venta
              JOIN productos p ON dv.id_producto = p.id_producto
              ORDER BY v.fecha DESC";

    $stmt = $pdo->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>