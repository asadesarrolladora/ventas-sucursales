<?php
header('Content-Type: application/json');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO ventas (id_sucursal, total_venta, fecha_hora) VALUES (?, ?, NOW())");
    $stmt->execute([$data['sucursal_id'], $data['total']]);
    $idVenta = $pdo->lastInsertId();

    foreach ($data['productos'] as $p) {
        // Insertar detalle
        $stmtDet = $pdo->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        $stmtDet->execute([$idVenta, $p['id_producto'], $p['cantidad'], $p['precio_unitario']]);

        // Restar stock
        $stmtInv = $pdo->prepare("UPDATE inventarios SET cantidad_disponible = cantidad_disponible - ? WHERE id_producto = ? AND id_sucursal = ?");
        $stmtInv->execute([$p['cantidad'], $p['id_producto'], $data['sucursal_id']]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
