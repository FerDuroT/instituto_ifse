<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';
require_once '../includes/gestion_sesiones.php'; // Protege esta página

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_price = 0;

// Obtener items del carrito para el resumen
try {
    $stmt = $pdo->prepare("
        SELECT ci.id, c.id as course_id, c.titulo, c.price, ci.quantity
        FROM cart_items ci
        JOIN cursos c ON ci.course_id = c.id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
} catch (PDOException $e) {
    redirect_with_message("carrito.php", "danger", "Error al cargar el carrito para el pago: " . $e->getMessage());
}

if (empty($cart_items)) {
    redirect_with_message("carrito.php", "warning", "Tu carrito está vacío. No puedes proceder al pago.");
}

$errors = [];
$success = '';
$selected_payment_method = $_POST['payment_method'] ?? ''; // Mantener el método seleccionado

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'] ?? '';

    if (empty($payment_method)) {
        $errors[] = "Por favor, selecciona un método de pago.";
    }

    // Valida tarjeta de crédito (solo de ejemplo)
    if ($payment_method === 'tarjeta_credito') {
        $card_number = trim($_POST['card_number'] ?? '');
        $card_expiry = trim($_POST['card_expiry'] ?? '');
        $card_cvv = trim($_POST['card_cvv'] ?? '');

        if (empty($card_number) || !preg_match("/^[0-9]{13,19}$/", $card_number)) {
            $errors[] = "Número de tarjeta de crédito inválido.";
        }
        if (empty($card_expiry) || !preg_match("/^(0[1-9]|1[0-2])\/?([0-9]{2})$/", $card_expiry)) {
            $errors[] = "Fecha de expiración inválida (MM/AA).";
        }
        if (empty($card_cvv) || !preg_match("/^[0-9]{3,4}$/", $card_cvv)) {
            $errors[] = "CVV inválido.";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction(); // Iniciar transacción

            // Registrar inscripciones y vaciar carrito
            foreach ($cart_items as $item) {
                $stmt_enroll = $pdo->prepare("
                    INSERT INTO matriculas (user_id, course_id, enrollment_date, payment_method, status)
                    VALUES (?, ?, NOW(), ?, 'completado')
                ");
                $stmt_enroll->execute([$user_id, $item['course_id'], $payment_method]);
            }

            // Vaciar el carrito del usuario
            $stmt_clear_cart = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt_clear_cart->execute([$user_id]);

            $pdo->commit(); // Confirmar transacción

            $_SESSION['cart_count'] = 0; // Reiniciar el contador del carrito en sesión
            redirect_with_message("../pages/cursos_inscritos.php", "success", "¡Tu compra se ha procesado exitosamente! Los cursos han sido añadidos a tu perfil.");

        } catch (PDOException $e) {
            $pdo->rollBack(); // Revertir transacción en caso de error
            $errors[] = "Error al procesar el pago y la inscripción: " . $e->getMessage();
            error_log("Error de pago/inscripción: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso de Pago - Instituto IFSE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container content-section">
        <?php display_session_message(); ?>
        <h2>Finalizar Compra</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="checkout-layout">
            <div class="checkout-summary">
                <h3>Resumen del Pedido</h3>
                <?php if (!empty($cart_items)): ?>
                    <div class="summary-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="summary-item">
                                <span><?php echo htmlspecialchars($item['title']); ?> (x<?php echo htmlspecialchars($item['quantity']); ?>)</span>
                                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="summary-total">
                        <strong>Total a Pagar:</strong> <span>$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                <?php else: ?>
                    <p>No hay artículos en tu carrito para procesar.</p>
                <?php endif; ?>
            </div>

            <div class="checkout-form-container">
                <h3>Método de Pago</h3>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="paymentForm">
                    <div class="form-group radio-group">
                        <input type="radio" id="deposito" name="payment_method" value="deposito_bancario" onchange="this.form.submit()"
                            <?php echo ($selected_payment_method == 'deposito_bancario') ? 'checked' : ''; ?> required>
                        <label for="deposito">Depósito Bancario</label>
                    </div>
                    <div class="form-group radio-group">
                        <input type="radio" id="tarjeta" name="payment_method" value="tarjeta_credito" onchange="this.form.submit()"
                            <?php echo ($selected_payment_method == 'tarjeta_credito') ? 'checked' : ''; ?> required>
                        <label for="tarjeta">Tarjeta de Crédito</label>
                    </div>

                    <div id="creditCardFields" style="display: <?php echo ($selected_payment_method == 'tarjeta_credito') ? 'block' : 'none'; ?>;">
                        <div class="form-group">
                            <label for="card_number">Número de Tarjeta:</label>
                            <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX" value="<?php echo htmlspecialchars($_POST['card_number'] ?? ''); ?>"
                                <?php echo ($selected_payment_method == 'tarjeta_credito') ? 'required' : ''; ?>>
                        </div>
                        <div class="form-group half-width">
                            <label for="card_expiry">Fecha de Expiración (MM/AA):</label>
                            <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/AA" value="<?php echo htmlspecialchars($_POST['card_expiry'] ?? ''); ?>"
                                <?php echo ($selected_payment_method == 'tarjeta_credito') ? 'required' : ''; ?>>
                        </div>
                        <div class="form-group half-width">
                            <label for="card_cvv">CVV:</label>
                            <input type="text" id="card_cvv" name="card_cvv" placeholder="XXX" value="<?php echo htmlspecialchars($_POST['card_cvv'] ?? ''); ?>"
                                <?php echo ($selected_payment_method == 'tarjeta_credito') ? 'required' : ''; ?>>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">Confirmar y Pagar</button>
                </form>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>