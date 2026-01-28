<?php
include '../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    try {
        $pdo->beginTransaction();

        // 1. Insertar el producto
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio_base) VALUES (?, ?)");
        $stmt->execute([$nombre, $precio]);
        $id_nuevo = $pdo->lastInsertId();

        // 2. Crear el registro de inventario (Sucursal 1 por defecto)
        $stmtInv = $pdo->prepare("INSERT INTO inventarios (id_producto, id_sucursal, cantidad_disponible) VALUES (?, 1, ?)");
        $stmtInv->execute([$id_nuevo, $stock]);

        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Producto guardado"]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>