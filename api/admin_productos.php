<?php
session_start();
include '../config.php';

// Establecer cabecera para respuesta JSON
header('Content-Type: application/json');

// 1. Verificación de seguridad: Solo el administrador puede modificar el stock
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Acceso denegado: Se requieren permisos de administrador"]);
    exit;
}

// 2. Procesar la solicitud POST del formulario de admin.html
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validar que los campos no estén vacíos
    if (empty($_POST['nombre']) || !isset($_POST['precio']) || !isset($_POST['stock'])) {
        echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios"]);
        exit;
    }

    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock_nuevo = intval($_POST['stock']);

    /* LÓGICA ANTI-DUPLICADOS:
       Usamos 'ON DUPLICATE KEY UPDATE'. 
       Si el nombre ya existe en la base de datos:
       - cantidad_disponible: se suma el nuevo valor al que ya existía (ej: 9 + 1 = 10)
       - precio_base: se actualiza al precio más reciente ingresado.
    */
    $sql = "INSERT INTO productos (nombre, precio_base, cantidad_disponible) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            cantidad_disponible = cantidad_disponible + VALUES(cantidad_disponible),
            precio_base = VALUES(precio_base)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdi", $nombre, $precio, $stock_nuevo);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success", 
                "message" => "Inventario actualizado correctamente para: " . $nombre
            ]);
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error en la base de datos: " . $e->getMessage()]);
    }
} else {
    // Si intentan entrar por URL directa sin enviar el formulario
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>