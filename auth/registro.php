<?php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

// Gestión de sesines
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres = trim($_POST['nombres']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $edad = $_POST['edad'];
    $sexo = $_POST['sexo'];
    $cedula = trim($_POST['cedula']);
    $telefono = trim($_POST['telefono']);

    
    if (empty($nombres)) { $errors[] = "El Nombres es obligatorio."; }
    if (empty($email)) { $errors[] = "El E-mail es obligatorio."; }
    if (!isValidEmail($email)) { $errors[] = "El formato del E-mail no es válido."; }
    if (empty($password)) { $errors[] = "La Contraseña es obligatorio."; }
    if (strlen($password) < 6) { $errors[] = "La Contraseña debe tener al menos 6 caracteres."; }
    if ($password !== $confirm_password) { $errors[] = "Las contraseñas no coinciden."; }
    if (empty($cedula)) { $errors[] = "La Cédula es obligatorio."; }
    //if (!isValidCedula($cedula)) { $errors[] = "El número de cédula no es válido."; }
    if (empty($edad)) { $errors[] = "La edad es obligatoria."; }
    if (!is_numeric($edad) || $edad < 18) { $errors[] = "La edad debe ser un número válido y mayor de 18."; }
    if (empty($sexo)) { $errors[] = "El sexo es obligatorio."; }


    // Verificar si el email o cédula ya existen
    try {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? OR cedula = ?");
        $stmt->execute([$email, $cedula]);
        if ($stmt->fetch()) {
            $errors[] = "El E-mail o la cédula ya están registrados.";
        }
    } catch (PDOException $e) {
        $errors[] = "Error al verificar datos: " . $e->getMessage();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Cifra el password y se guarda el hash del mismo. Es una medida de seguridad.
        $activation_code = generateToken(64); // Genera elcódigo de activación

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombres, email, password, edad, sexo, cedula, telefono, is_active, activation_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombres, $email, $hashed_password, $edad, $sexo, $cedula, $telefono, 1, $activation_code]); // activo==0 para aactivar vía E-mail
                $success = "Te has registrado correctamente en el sistema IFSE";
                $_POST = []; // Limpia los campos al recargar la página.
        } catch (PDOException $e) {
            $errors[] = "Error al registrar usuario: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Instituto IFSE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/icono.ico" type="image/x-icon">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="auth-container">
        <div class="auth-card">
            <h2>Regístrate en Instituto IFSE</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="nombres">Nombres Completos:</label>
                    <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($_POST['nombres'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="edad">Edad:</label>
                    <input type="number" id="edad" name="edad" value="<?php echo htmlspecialchars($_POST['edad'] ?? ''); ?>" min="18" required>
                </div>
                <div class="form-group">
                    <label for="sexo">Sexo:</label>
                    <select id="sexo" name="sexo" required>
                        <option value="">Seleccionar</option>
                        <option value="Masculino" <?php echo (isset($_POST['sexo']) && $_POST['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Femenino" <?php echo (isset($_POST['sexo']) && $_POST['sexo'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                        <option value="Otro" <?php echo (isset($_POST['sexo']) && $_POST['sexo'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cedula">Número de Cédula:</label>
                    <input type="text" id="cedula" name="cedula" value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>" maxlength="10" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Registrarse</button>
            </form>
            <p class="auth-link">¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión aquí</a></p>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
