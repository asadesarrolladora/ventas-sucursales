<?php
// 1. Iniciar sesión y configuración básica
session_start();
include '../config.php';

// Forzamos que la respuesta siempre sea JSON
header('Content-Type: application/json');

// 2. Validación de Seguridad estricta
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Sesión no válida o sin permisos de admin"]);
    exit;
}

// 3. Procesar los datos cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recibir y limpiar datos (evita espacios vacíos)
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
    $stock_nuevo = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

    // Validación simple antes de tocar la base de datos
    if (empty($nombre) || $precio <= 0 || $stock_nuevo <= 0) {
        echo json_encode(["status" => "error", "message" => "Por favor llena todos los campos con valores válidos"]);
        exit;
    }

    /* LÓGICA PARA EVITAR DUPLICADOS
       Esta consulta busca el nombre. Si ya existe, suma el stock al actual.
       Si no existe, crea una nueva fila.
    */
    $sql = "INSERT INTO productos (nombre, precio_base, cantidad_disponible) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            cantidad_disponible = cantidad_disponible + VALUES(cantidad_disponible),
            precio_base = VALUES(precio_base)";

    try {
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            // Esto captura errores si la tabla o columnas están mal escritas
            throw new Exception("Error en la preparación: " . $conn->error);
        }

        $stmt->bind_param("sdi", $nombre, $precio, $stock_nuevo);

        if ($stmt->execute()) {
            // Éxito: El JavaScript recibirá este JSON
            echo json_encode([
                "status" => "success", 
                "message" => "Producto '$nombre' actualizado correctamente."
            ]);
        } else {
            throw new Exception("Error al ejecutar: " . $stmt->error);
        }

    } catch (Exception $e) {
        // Esto evita el Error 500 y te dice exactamente qué falló
        echo json_encode([
            "status" => "error", 
            "message" => $e->getMessage()
        ]);
    }

} else {
    // Si alguien intenta entrar al archivo por URL sin enviar datos
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>