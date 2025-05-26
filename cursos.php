<?php
require_once 'includes/db.php';
require_once 'includes/funciones.php';

// Inicia la sesión para mostrar el estado del carrito en el header
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cursos = [];
try {
    $consultacursos = $pdo->query("SELECT * FROM cursos ORDER BY ID ASC LIMIT 10"); // Lista de cursos
    $cursos = $consultacursos->fetchAll(PDO::FETCH_ASSOC); //Array asociativo
} catch (PDOException $e) {
    // En caso de suceder algún error en la consulta
    $error_message = "Error al cargar los cursos: " . $e->getMessage();
    error_log($error_message);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos Disponibles - Instituto IFSE</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Archivo de estilos -->
    <link rel="icon" href="img/icono.ico" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container content-section">
        <?php display_session_message(); ?>
        <h2>Nuestros Cursos</h2>
        <p class="intro-text">Explora nuestra oferta de cursos que impulsarán tu carrera.</p>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($cursos)): ?>
            <div class="course-grid">
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-card">
                        <img src="<?php echo htmlspecialchars($curso['imagen'] ?? 'img/logo.jpg'); ?>" alt="<?php echo htmlspecialchars($curso['titulo']); ?>">
                        <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                        <p class="course-price">$<?php echo number_format($curso['price'], 2); ?></p>
                        <p class="course-duration"><?php echo htmlspecialchars($curso['duration_hours']); ?> horas</p>
                        <a href="pages/detalle_curso.php?id=<?php echo $curso['id']; ?>" class="btn btn-secondary">Ver Detalles</a>  <!-- Debe tener una sesión válida, caso contrario debe logearse -->
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No hay cursos disponibles en este momento. Por favor, inténtalo de nuevo más tarde.</p>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
