<?php
include '../config.php'; // Usa la configuración de InfinityFree

$data = json_decode(file_get_contents('php://input'), true);
$id_sucursal = 1; // Ajustar según sea necesario
$productos = $data['productos'];
$total = $data['total'];

try {
    $pdo->beginTransaction();

    foreach ($productos as $item) {
        // 1. Verificar stock actual antes de vender
        $stmt = $pdo->prepare("SELECT cantidad_disponible FROM inventarios WHERE id_producto = ? AND id_sucursal = ?");
        $stmt->execute([$item['id'], $id_sucursal]);
        $stockActual = $stmt->fetchColumn();

        if ($stockActual < $item['cantidad']) {
            throw new Exception("Stock insuficiente para el producto ID: " . $item['id']);
        }

        // 2. Restar del inventario
        $update = $pdo->prepare("UPDATE inventarios SET cantidad_disponible = cantidad_disponible - ? WHERE id_producto = ? AND id_sucursal = ?");
        $update->execute([$item['cantidad'], $item['id'], $id_sucursal]);
    }

    // 3. Registrar la venta
    $insVenta = $pdo->prepare("INSERT INTO ventas (id_sucursal, total_venta) VALUES (?, ?)");
    $insVenta->execute([$id_sucursal, $total]);

    $pdo->commit();
    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>