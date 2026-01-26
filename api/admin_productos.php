<?php
header('Content-Type: application/json');
require_once '../config.php';
$data = json_decode(file_get_contents('php://input'), true);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio_base) VALUES (?, ?)");
    $stmt->execute([$data['nombre'], $data['precio']]);
    
    // Al crear un producto, le asignamos stock 0 en la sucursal 1 por defecto
    $idNuevo = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO inventarios (id_producto, id_sucursal, cantidad_disponible) VALUES (?, 1, 0)")
        ->execute([$idNuevo]);

    echo json_encode(['status' => 'success']);
}
?>
