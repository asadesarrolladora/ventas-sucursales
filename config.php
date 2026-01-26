<?php
// config.php
$host = 'sqlXXX.infinityfree.com'; // El "MySQL Hostname" que te dio el panel
$db   = 'if0_40999856_sistema_ventas'; // Tu nombre de base de datos
$user = 'if0_40999856'; // Tu usuario de base de datos
$pass = 'ovejuoQ1V5JQl4'; // La contraseña de tu cuenta de hosting
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>