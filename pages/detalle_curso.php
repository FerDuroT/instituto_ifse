<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';
require_once '../includes/gestion_sesiones.php'; // Gestión de la sesión, el usuario debe estar autenticado
require_once '../includes/config.php';

$course = null;
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM cursos WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error al cargar el curso: " . $e->getMessage();
        error_log($error_message);
    }
}

if (!$course) {
    redirect_with_message("cursos.php", "Error", "Curso no encontrado.");
}

// Añadir al carrito
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user_id'];
    $course_id_to_add = $_POST['course_id']; // ID del curso actual
    $quantity = 1; // Se añade de a uno al carrito

    try {
        // Verificar si el curso ya está en el carrito
        $stmt_check = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND course_id = ?");
        $stmt_check->execute([$user_id, $course_id_to_add]);
        $existing_item = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($existing_item) {

            redirect_with_message("detalle_curso.php?id=" . $course_id, "Adertencia", "Este curso ya está en tu carrito.");
        } else {
            // Insertar nuevo item en el carrito
            $stmt_insert = $pdo->prepare("INSERT INTO cart_items (user_id, course_id, quantity) VALUES (?, ?, ?)");
            $stmt_insert->execute([$user_id, $course_id_to_add, $quantity]);

            // Actualizar el contador del carrito
            $_SESSION['cart_count'] = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] + $quantity : $quantity;

            redirect_with_message("detalle_curso.php?id=" . $course_id, "Completado", "Curso añadido al carrito.");
        }
    } catch (PDOException $e) {
        redirect_with_message("detalle_curso.php?id=" . $course_id, "Error", "Error al añadir al carrito: " . $e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['titulo']); ?> - Instituto IFSE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/icono.ico" type="image/x-icon">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container content-section">
        <?php display_session_message(); ?>
        <div class="course-detail">
            <div class="course-image">
                <img src="<?php echo htmlspecialchars(BASE_URL . $course['imagen'] ?? '../img/ifse_logo.jpg'); ?>" alt="<?php echo htmlspecialchars($course['titulo']); ?>">
            </div>
            <div class="course-info">
                <h1><?php echo htmlspecialchars($course['titulo']); ?></h1>
                <p class="price-big">$<?php echo number_format($course['price'], 2); ?></p>
                <p class="duration-big"><i class="far fa-clock"></i> <?php echo htmlspecialchars($course['duration_hours']); ?> horas</p>
                <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                
                <form action="" method="POST">
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg">
                        <i class="fas fa-cart-plus"></i> Añadir al Carrito
                    </button>
                </form>

                <div class="course-actions">
                    <a href="../cursos.php" class="btn btn-secondary-outline"><i class="fas fa-arrow-left"></i> Volver al Catálogo</a>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>