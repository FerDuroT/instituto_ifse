<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';
require_once '../includes/gestion_sesiones.php'; // Protege esta página

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_price = 0;

// Lógica para eliminar item del carrito
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_from_cart'])) {
    $cart_item_id = $_POST['cart_item_id'];
    try {
        // Asegurarse de que el item pertenezca al usuario actual antes de eliminar
        $stmt_delete = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt_delete->execute([$cart_item_id, $user_id]);

        // Actualizar el contador del carrito en la sesión si se eliminó correctamente
        if ($stmt_delete->rowCount() > 0) {
            // Re-calcular el contador después de eliminar
            $stmt_cart_count = $pdo->prepare("SELECT SUM(quantity) as cart_count FROM cart_items WHERE user_id = ?");
            $stmt_cart_count->execute([$user_id]);
            $cart_data = $stmt_cart_count->fetch(PDO::FETCH_ASSOC);
            $_SESSION['cart_count'] = $cart_data['cart_count'] ?? 0;

            redirect_with_message("carrito.php", "success", "Curso eliminado del carrito.");
        } else {
            redirect_with_message("carrito.php", "warning", "No se pudo eliminar el curso del carrito o no existe.");
        }
    } catch (PDOException $e) {
        redirect_with_message("carrito.php", "danger", "Error al eliminar del carrito: " . $e->getMessage());
    }
}


// Obtener items del carrito
try {
    $stmt = $pdo->prepare("
        SELECT ci.id, c.titulo, c.price, ci.quantity, c.imagen
        FROM cart_items ci
        JOIN cursos c ON ci.course_id = c.id
        WHERE ci.user_id = ?
        ORDER BY ci.added_at DESC
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
    // Actualizar el contador del carrito en la sesión (por si acaso se hizo una compra en otra sesión)
    // Es mejor recalcularlo siempre al cargar la página para asegurar consistencia.
    $_SESSION['cart_count'] = count($cart_items);

} catch (PDOException $e) {
    $error_message = "Error al cargar el carrito: " . $e->getMessage();
    error_log($error_message);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - Instituto IFSE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/icono.ico" type="image/x-icon">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <main class="container content-section">
        <?php display_session_message(); ?>
        <h2>Mi Carrito de Compras</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($cart_items)): ?>
            <div class="cart-items-list">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars(BASE_URL . $item['imagen'] ?? '../img/logo.png'); ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['titulo']); ?></h3>
                            <p>Precio Unitario: $<?php echo number_format($item['price'], 2); ?></p>
                            <p>Cantidad: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            <p class="item-subtotal">Subtotal: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                        <form action="" method="POST" class="remove-form">
                            <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="remove_from_cart" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Total del Carrito: <span class="total-price">$<?php echo number_format($total_price, 2); ?></span></h3>
                <a href="pagar.php" class="btn btn-primary btn-lg">Pagar</a>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <p>Tu carrito de compras está vacío. <a href="../cursos.php">Explora todos los cursos disponibles</a> y refuerza tu futuro.</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>