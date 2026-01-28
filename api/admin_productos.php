<?php
session_start();
include '../config.php';
header('Content-Type: application/json');

// Protección de Seguridad
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Acceso denegado. Inicie sesión."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;

    try {
        $pdo->beginTransaction();
        // Insertar producto base
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio_base) VALUES (?, ?)");
        $stmt->execute([$nombre, $precio]);
        $id_nuevo = $pdo->lastInsertId();

        // Insertar stock en sucursal 1
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