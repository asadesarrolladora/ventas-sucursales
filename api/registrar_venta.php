<?php
// Asegúrate de que no haya espacios antes de esta etiqueta
include '../config.php';

// Esto ayuda a que el navegador ignore el reto de seguridad en peticiones AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Cuerpo de solicitud vacío"]);
    exit;
}

try {
    $pdo->beginTransaction();

    // Insertar venta
    $stmtV = $pdo->prepare("INSERT INTO ventas (total, id_sucursal) VALUES (?, 1)");
    $stmtV->execute([$data['total']]);
    $idVenta = $pdo->lastInsertId();

    foreach ($data['productos'] as $prod) {
        // Insertar detalle
        $stmtD = $pdo->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, 1, ?)");
        $stmtD->execute([$idVenta, $prod['id'], $prod['precio']]);

        // Actualizar stock
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