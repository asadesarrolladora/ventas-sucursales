<?php
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