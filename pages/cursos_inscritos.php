<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';
require_once '../includes/gestion_sesiones.php'; // Gestión de sesiones

$user_id = $_SESSION['user_id'];
$enrolled_cursos = [];

try {
    $stmt = $pdo->prepare("
        SELECT c.titulo, c.description, c.imagen, c.duration_hours, e.enrollment_date, e.status
        FROM matriculas e
        JOIN cursos c ON e.course_id = c.id
        WHERE e.user_id = ?
        ORDER BY e.enrollment_date DESC
    ");
    $stmt->execute([$user_id]);
    $enrolled_cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error al cargar tus cursos inscritos: " . $e->getMessage();
    error_log($error_message);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos - Instituto IFSE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/icono.ico" type="image/x-icon">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container content-section">
        <?php display_session_message(); ?>
        <h2>Cursos Inscritos</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($enrolled_cursos)): ?>
            <div class="enrolled-courses-list">
                <?php foreach ($enrolled_cursos as $curso): ?>
                    <div class="course-card enrolled-card">
                        <img src="<?php echo htmlspecialchars(BASE_URL . $curso['imagen'] ?? '../img/icono.png'); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                            <p class="enrolled-date">Inscrito el: <?php echo date('d/m/Y', strtotime($curso['enrollment_date'])); ?></p>
                            <span class="status-badge status-<?php echo strtolower($curso['status']); ?>"><?php echo ucfirst($curso['status']); ?></span>
                            <a href="#" class="btn btn-primary btn-sm">Accede al Curso (Test)</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No te has matriculado aún</p>
                <a href="../cursos.php" class="btn btn-secondary">Catálogo de Cursos</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>