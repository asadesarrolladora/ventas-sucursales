<?php
session_start();
include '../config.php';
header('Content-Type: application/json');

// 1. Verificación de Seguridad
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

// 2. Procesar datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock_nuevo = intval($_POST['stock']);

    if (empty($nombre)) {
        echo json_encode(["status" => "error", "message" => "El nombre es obligatorio"]);
        exit;
    }

    // 3. Consulta con ON DUPLICATE KEY UPDATE
    // Esta consulta evita el error 500 al detectar el nombre duplicado
    $sql = "INSERT INTO productos (nombre, precio_base, cantidad_disponible) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            cantidad_disponible = cantidad_disponible + VALUES(cantidad_disponible),
            precio_base = VALUES(precio_base)";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        // Si hay error en la preparación (ej. tabla no encontrada)
        echo json_encode(["status" => "error", "message" => "Error de sistema: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("sdi", $nombre, $precio, $stock_nuevo);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "¡Stock actualizado!"]);
    } else {
        // Capturamos cualquier otro error de base de datos
        echo json_encode(["status" => "error", "message" => "No se pudo actualizar: " . $stmt->error]);
    }
}
?>