<?php
include '../config.php';
header('Content-Type: application/json');

try {
    // Usamos LEFT JOIN para asegurar que el nombre aparezca incluso si hay errores de stock
    $query = "SELECT 
                p.id_producto, 
                p.nombre, 
                IFNULL(i.cantidad_disponible, 0) as cantidad_disponible 
              FROM productos p
              LEFT JOIN inventarios i ON p.id_producto = i.id_producto
              ORDER BY p.id_producto DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>