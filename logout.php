<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Para borrar los datos de la sesión
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Redirige a la página inicial
require_once 'includes/config.php';
header("Location: " . BASE_URL . "index.php");
exit(); // Para terminar el script
?>