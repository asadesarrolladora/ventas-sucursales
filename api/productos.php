<?php
header('Content-Type: application/json');
require_once '../config.php';

$stmt = $pdo->query("SELECT id_producto, nombre, precio_base FROM productos");
echo json_encode($stmt->fetchAll());
?>
