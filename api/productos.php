<?php
include '../config.php';
header('Content-Type: application/json');

// Selecciona productos con stock en la sucursal 1
$query = "SELECT p.id_producto, p.nombre, p.precio_base, i.cantidad_disponible 
          FROM productos p 
          INNER JOIN inventarios i ON p.id_producto = i.id_producto 
          WHERE i.id_sucursal = 1";

$stmt = $pdo->prepare($query);
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
