<?php
session_start();
include '../config.php';
header('Content-Type: application/json');

// Protección de Seguridad
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Sesión expirada o no válida"]);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) exit;

try {
    $pdo->beginTransaction();

    // 1. Registrar Venta
    $stmtV = $pdo->prepare("INSERT INTO ventas (total, id_sucursal) VALUES (?, 1)");
    $stmtV->execute([$data['total']]);
    $idVenta = $pdo->lastInsertId();

    // 2. Detalle y Descuento de Stock
    foreach ($data['productos'] as $prod) {
        $stmtD = $pdo->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, 1, ?)");
        $stmtD->execute([$idVenta, $prod['id'], $prod['precio']]);

        $stmtS = $pdo->prepare("UPDATE inventarios SET cantidad_disponible = cantidad_disponible - 1 WHERE id_producto = ? AND id_sucursal = 1");
        $stmtS->execute([$prod['id']]);
    }

    $pdo->commit();
    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>