<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

// Inicia la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario ya está logueado, muestra su perfil
if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/miperfil.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Por favor, ingresa tu E-mail y Contraseña.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, nombres, password, is_active FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['is_active'] == 0) {
                    $errors[] = "La cuenta debe ser activada con el código enviado al E-mail";
                } elseif (password_verify($password, $user['password'])) {
                    // Contraseña correcta, iniciar sesión
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nombres'];

                    // Contar items del carrito y guardar en sesión
                    $stmt_cart_count = $pdo->prepare("SELECT SUM(quantity) as cart_count FROM cart_items WHERE user_id = ?");
                    $stmt_cart_count->execute([$user['id']]);
                    $cart_data = $stmt_cart_count->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['cart_count'] = $cart_data['cart_count'] ?? 0;

                    redirect_with_message("../pages/miperfil.php", "success", "¡Bienvenido de nuevo, " . $user['nombres'] . "!");
                } else {
                    $errors[] = "E-mail o contraseña incorrectos.";
                }
            } else {
                $errors[] = "E-mail o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            $errors[] = "Error de base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Instituto IFSE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="auth-container">
        <div class="auth-card">
            <h2>Iniciar Sesión</h2>
            <?php display_session_message(); ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </form>
            <p class="auth-link">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>            
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
