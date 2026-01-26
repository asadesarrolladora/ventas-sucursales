<?php
header('Content-Type: application/json');
require_once '../config.php';

$sql = "SELECT p.id_producto, p.nombre, s.nombre_sucursal, i.cantidad_disponible 
        FROM inventarios i 
        JOIN productos p ON i.id_producto = p.id_producto 
        JOIN sucursales s ON i.id_sucursal = s.id_sucursal";

$stmt = $pdo->query($sql);
echo json_encode($stmt->fetchAll());
?>
