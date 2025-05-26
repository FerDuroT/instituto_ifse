<?php
// Protege las páginas que deben ser accedidas únicamente por usuarios autenticados
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si es usuario autenticado
if (!isset($_SESSION['user_id'])) {
    // Si no es autenticado, envía a la página de inicio de sesión
    header("Location: /instituto_ifse/auth/login.php");
    exit();
}
?>