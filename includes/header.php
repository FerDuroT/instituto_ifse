<?php
require_once 'config.php';
// Inicia la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instituto IFSE</title>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
        <link rel="icon" href="<?php echo BASE_URL; ?>../img/icono.ico" type="image/x-icon">
    </head>
    <body>
        <header class="main-header">
            <div class="container">
                <a href="<?php echo BASE_URL; ?>index.php" class="logo">
                    <img src="<?php echo BASE_URL; ?>img/logo.png" alt="Instituto IFSE Logo">
                    <!-- <span>INICIO</span> -->
                </a>

                <div class="user-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-info">
                            <span class="user-name">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <div class="user-dropdown">
                                <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary btn-home">Home</a>
                                <a href="<?php echo BASE_URL; ?>pages/miperfil.php" class="btn btn-primary btn-perfil">Mi Perfil</a>
                                <a href="<?php echo BASE_URL; ?>pages/cursos_inscritos.php" class="btn btn-primary btn-educacion">Mi Educación</a>
                                <a href="<?php echo BASE_URL; ?>pages/carrito.php" class="btn btn-primary btn-educacion">Mi Carrito</a>
                                <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-primary btn-logout">Cerrar Sesión</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary btn-home">Home</a>
                        <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-primary btn-login">Iniciar Sesión</a>
                        <a href="<?php echo BASE_URL; ?>auth/registro.php" class="btn btn-secondary btn-register">Regístrate</a>
                    <?php endif; ?>
                </div>
                <button class="menu-toggle" aria-label="Abrir menú">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </header>
    </body>
</html>