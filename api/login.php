<?php
session_start();
include '../config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$user = $data['usuario'] ?? '';
$pass = $data['password'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT id_usuario, usuario, rol FROM usuarios WHERE usuario = ? AND password = ?");
    $stmt->execute([$user, $pass]);
    $usuarioValido = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuarioValido) {
        $_SESSION['user_id'] = $usuarioValido['id_usuario'];
        $_SESSION['rol'] = $usuarioValido['rol'];
        echo json_encode(["status" => "success", "rol" => $usuarioValido['rol']]);
    } else {
        echo json_encode(["status" => "error", "message" => "Usuario o clave incorrectos"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}