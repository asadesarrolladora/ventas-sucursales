<?php
session_start();
session_unset(); // Borra las variables de sesión
session_destroy(); // Destruye la sesión
header('Location: ../login.html'); // Te redirige al login automáticamente
exit;
?>