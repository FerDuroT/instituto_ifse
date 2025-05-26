<?php
// Valida formato de email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Valida cédula 
function isValidCedula($cedula) {
    if (!preg_match("/^[0-9]{10}$/", $cedula)) {
        return false; 
    }
    $region = substr($cedula, 0, 2);
    if ($region < 1 || $region > 24) {
        return false; // región no válida
    }
    $last_digit = substr($cedula, 9, 1);
    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
        $digit = (int)$cedula[$i];
        if ($i % 2 == 0) { 
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        $sum += $digit;
    }
    $check_digit = ((($sum / 10) - floor($sum / 10)) * 10);
    $check_digit = 10 - $check_digit;
    if ($check_digit == 10) {
        $check_digit = 0;
    }
    return $check_digit == (int)$last_digit;
}

// Enviar email (no env[ia realmente])
function sendEmail($to, $subject, $message) {
    // Solo se registra el intento.
    error_log("--- SIMULACIÓN DE ENVÍO DE EMAIL ---");
    error_log("Para: " . $to);
    error_log("Asunto: " . $subject);
    error_log("Mensaje: " . $message);
    error_log("------------------------------------");
    return true; // Envio correcto
}

// Generar un código/token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Función para redirigir con  mensaje
function redirect_with_message($location, $type, $message) {
    $escaped_message = urlencode($message);
    $escaped_type = urlencode($type);
    // Redirigir y pasar el mensaje y tipo como parámetros GET
    header("Location: " . $location . "?msg_type=" . $escaped_type . "&msg=" . $escaped_message);
    exit();
}

// Mostrar mensajes de sesión/URL
function display_url_message() {
    if (isset($_GET['msg']) && isset($_GET['msg_type'])) {
        $message = htmlspecialchars(urldecode($_GET['msg']));
        $type = htmlspecialchars(urldecode($_GET['msg_type']));
        echo '<div class="alert alert-' . $type . '">';
        echo $message;
        echo '</div>';
    }
}

//Mensaje usando $_SESSION para persistencia
function display_session_message() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['message']) && isset($_SESSION['message_type'])) {
        echo '<div class="alert alert-' . $_SESSION['message_type'] . '">';
        echo $_SESSION['message'];
        echo '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Mensaje alerta simple de SweetAlert. Referencia: https://github.com/Carlos007007/SPV
function sweet_alert_single($data){
			$alert="
				<script>
					swal(
					  '".$data['title']."',
					  '".$data['text']."',
					  '".$data['type']."'
					);
				</script>"
			;
			return $alert;
		}
?>