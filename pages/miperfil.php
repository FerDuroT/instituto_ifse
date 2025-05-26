<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';
require_once '../includes/gestion_sesiones.php'; // Gestiona la sesión

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Obtener datos del usuario
$user_data = [];
try {
    $stmt = $pdo->prepare("SELECT email, edad, sexo, cedula, telefono FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al cargar datos del usuario en dashboard: " . $e->getMessage());
    // Puedes manejar este error de forma más amigable al usuario si lo deseas
    $user_data_error = "No se pudieron cargar tus datos de perfil.";
}

// Obtener cursos inscritos recientemente (ej. los últimos 3)
$recent_enrollments = [];
try {
    $stmt_enrollments = $pdo->prepare("
        SELECT c.titulo, c.imagen, e.enrollment_date
        FROM matriculas e
        JOIN cursos c ON e.course_id = c.id
        WHERE e.user_id = ?
        ORDER BY e.enrollment_date DESC
        LIMIT 3
    ");
    $stmt_enrollments->execute([$user_id]);
    $recent_enrollments = $stmt_enrollments->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al cargar cursos recientes en dashboard: " . $e->getMessage());
    $recent_enrollments_error = "No se pudieron cargar tus cursos recientes.";
}

// Obtener cursos recomendados (ej. los 3 cursos más populares o aleatorios que no tenga inscritos)
$recommended_courses = [];
try {
    $stmt_recommended = $pdo->prepare("
        SELECT id, titulo, imagen, price
        FROM cursos
        WHERE id NOT IN (SELECT course_id FROM matriculas WHERE user_id = ?)
        ORDER BY RAND()
        LIMIT 3
    ");
    $stmt_recommended->execute([$user_id]);
    $recommended_courses = $stmt_recommended->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al cargar cursos recomendados en dashboard: " . $e->getMessage());
    $recommended_courses_error = "No se pudieron cargar recomendaciones de cursos.";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Usuario - Instituto IFSE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/icono.ico" type="image/x-icon">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container content-section dashboard-page">
        <?php display_session_message(); ?>
        <h1>Bienvenid@, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p class="intro-text">Estos son los cursos que forman parte de tu educación:</p>

        <div class="dashboard-grid">
            <div class="dashboard-card profile-card">
                <h3>Mi Perfil</h3>
                <?php if (isset($user_data_error)): ?>
                    <div class="alert alert-danger"><?php echo $user_data_error; ?></div>
                <?php else: ?>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email'] ?? 'N/A'); ?></p>
                    <p><strong>Cédula:</strong> <?php echo htmlspecialchars($user_data['cedula'] ?? 'N/A'); ?></p>
                    <p><strong>Edad:</strong> <?php echo htmlspecialchars($user_data['edad'] ?? 'N/A'); ?></p>
                    <p><strong>Sexo:</strong> <?php echo htmlspecialchars($user_data['sexo'] ?? 'N/A'); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($user_data['telefono'] ?? 'N/A'); ?></p>
                    <?php endif; ?>
            </div>

            <div class="dashboard-card recent-courses-card">
                <h3>Cursos Recientes</h3>
                <?php if (isset($recent_enrollments_error)): ?>
                    <div class="alert alert-danger"><?php echo $recent_enrollments_error; ?></div>
                <?php elseif (!empty($recent_enrollments)): ?>
                    <div class="recent-courses-list">
                        <?php foreach ($recent_enrollments as $enrollment): ?>
                            <div class="recent-course-item">
                                <img src="<?php echo htmlspecialchars(BASE_URL . $enrollment['imagen'] ?? '../img/logo.png'); ?>" alt="<?php echo htmlspecialchars($enrollment['titulo']); ?>">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($enrollment['titulo']); ?></h4>
                                    <p>Inscrito el: <?php echo date('d/m/Y', strtotime($enrollment['enrollment_date'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="cursos_inscritos.php" class="btn btn-primary btn-sm btn-full-width">Ver Todos Mis Cursos</a>
                <?php else: ?>
                    <p>Aún no te has inscrito en ningún curso. ¡Es un buen momento para empezar!</p>
                    <a href="../cursos.php" class="btn btn-primary btn-sm btn-full-width">Explorar Cursos</a>
                <?php endif; ?>
            </div>

            <div class="dashboard-card recommended-courses-card">
                <h3>Cursos Recomendados</h3>
                <?php if (isset($recommended_courses_error)): ?>
                    <div class="alert alert-danger"><?php echo $recommended_courses_error; ?></div>
                <?php elseif (!empty($recommended_courses)): ?>
                    <div class="recommended-courses-list">
                        <?php foreach ($recommended_courses as $rec_course): ?>
                            <div class="recommended-course-item">
                                <img src="<?php echo htmlspecialchars(BASE_URL . $rec_course['imagen'] ?? '../img/logo.png'); ?>" alt="<?php echo htmlspecialchars($rec_course['titulo']); ?>">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($rec_course['titulo']); ?></h4>
                                    <p class="price">$<?php echo number_format($rec_course['price'], 2); ?></p>
                                </div>
                                <a href="detalle_curso.php?id=<?php echo $rec_course['id']; ?>" class="btn btn-secondary btn-sm">Ver</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="../cursos.php" class="btn btn-primary btn-sm btn-full-width">Ver Más Cursos</a>
                <?php else: ?>
                    <p>No tenemos recomendaciones para ti.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>