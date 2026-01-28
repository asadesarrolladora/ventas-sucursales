<?php
include '../config.php'; // Usa la conexión de InfinityFree
header('Content-Type: application/json');

try {
    // Usamos un INNER JOIN para combinar el nombre del producto con su stock
    $query = "SELECT 
                p.id_producto, 
                p.nombre, 
                i.cantidad_disponible 
              FROM productos p
              INNER JOIN inventarios i ON p.id_producto = i.id_producto
              WHERE i.id_sucursal = 1
              ORDER BY p.id_producto DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultados);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>