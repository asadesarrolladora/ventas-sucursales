<?php
// Datos sacados de tu panel de InfinityFree
$host = 'sql200.infinityfree.com'; 
$db   = 'if0_40999856_sistema_ventas'; 
$user = 'if0_40999856'; 
$pass = 'ovejuoQ1V5JQl4'; // La que viste en Account Details

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    // Esto evita el error de "Unexpected token E" en la consola
    echo json_encode(["status" => "error", "message" => "Fallo de conexión: " . $e->getMessage()]);
    exit;
}
?>