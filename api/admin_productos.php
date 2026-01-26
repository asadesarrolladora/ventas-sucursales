<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock_inicial = isset($_POST['stock']) ? $_POST['stock'] : 0; // Nuevo campo

    try {
        $pdo->beginTransaction();

        // 1. Insertar el producto
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio_base) VALUES (?, ?)");
        $stmt->execute([$nombre, $precio]);
        $id_nuevo_producto = $pdo->lastInsertId();

        // 2. Crear el registro en inventarios para la sucursal 1
        $stmtInv = $pdo->prepare("INSERT INTO inventarios (id_producto, id_sucursal, cantidad_disponible) VALUES (?, 1, ?)");
        $stmtInv->execute([$id_nuevo_producto, $stock_inicial]);

        $pdo->commit();
        echo "Producto creado con stock: " . $stock_inicial;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>