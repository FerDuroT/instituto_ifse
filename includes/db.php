<?php
// Configuración de la conexión a la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', '');     
define('DB_NAME', 'instituto_ifse');

// Realiza la conexión
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Establecer el modo de error de PDO a excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Establecer el juego de caracteres a utf8mb4
    $pdo->exec("set names utf8mb4");
} catch (PDOException $e) {
    die("Error en conexión a la base de datos. " . $e->getMessage());
}
?>