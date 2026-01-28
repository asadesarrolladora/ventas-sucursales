<?php
include '../config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo->beginTransaction();

    // 1. Insertar la venta global
    $stmtVenta = $pdo->prepare("INSERT INTO ventas (total, id_sucursal) VALUES (?, 1)");
    $stmtVenta->execute([$data['total']]);
    $idVenta = $pdo->lastInsertId();

    foreach ($data['productos'] as $prod) {
        // 2. Insertar el detalle
        $stmtDetalle = $pdo->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, 1, ?)");
        $stmtDetalle->execute([$idVenta, $prod['id'], $prod['precio']]);

        // 3. DESCONTAR STOCK (Importante)
        $stmtStock = $pdo->prepare("UPDATE inventarios SET cantidad_disponible = cantidad_disponible - 1 WHERE id_producto = ? AND id_sucursal = 1");
        $stmtStock.execute([$prod['id']]);
    }

    $pdo->commit();
    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>