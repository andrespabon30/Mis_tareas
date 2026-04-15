<?php
// Configuración de seguridad - SIN session_start() aquí

class Seguridad {
    
    // Generar token CSRF
    public static function generarTokenCSRF() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    // Verificar token CSRF
    public static function verificarTokenCSRF($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            die("Error de seguridad: Token CSRF inválido.");
        }
        return true;
    }
    
    // Limpiar y validar email
    public static function limpiarEmail($email) {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
    }
    
    // Limpiar texto
    public static function limpiarTexto($texto) {
        return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
    }
    
    // Validar contraseña (mínimo 8 caracteres, 1 mayúscula, 1 número)
    public static function validarPassword($password) {
        if (strlen($password) < 8) {
            return false;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        return true;
    }
    
    // Registrar intento de login fallido
    public static function registrarIntentoFallido($db, $email) {
        $stmt = $db->prepare("UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE email = ?");
        $stmt->execute([$email]);
        
        $stmt = $db->prepare("SELECT intentos_fallidos FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $intentos = $stmt->fetch();
        
        if ($intentos && $intentos['intentos_fallidos'] >= 5) {
            $bloqueo = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $stmt = $db->prepare("UPDATE usuarios SET bloqueado_hasta = ? WHERE email = ?");
            $stmt->execute([$bloqueo, $email]);
        }
    }
    
    // Verificar si usuario está bloqueado
    public static function estaBloqueado($db, $email) {
        $stmt = $db->prepare("SELECT bloqueado_hasta FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && $usuario['bloqueado_hasta'] && strtotime($usuario['bloqueado_hasta']) > time()) {
            return true;
        }
        return false;
    }
    
    // Resetear intentos fallidos
    public static function resetearIntentos($db, $email) {
        $stmt = $db->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE email = ?");
        $stmt->execute([$email]);
    }
    
    // Configurar sesión segura
    public static function configurarSesion() {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        // ini_set('session.cookie_secure', 1); // Solo HTTPS - descomentar si tienes SSL
        ini_set('session.cookie_samesite', 'Strict');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);
    }
    
    // Rate limiting por IP
    public static function rateLimiting($ip) {
        $carpeta_logs = __DIR__ . '/../logs/';
        if (!file_exists($carpeta_logs)) {
            mkdir($carpeta_logs, 0777, true);
        }
        $archivo = $carpeta_logs . 'rate_limit_' . date('Y-m-d') . '.txt';
        $limites = [];
        
        if (file_exists($archivo)) {
            $contenido = file_get_contents($archivo);
            if ($contenido) {
                $limites = json_decode($contenido, true);
            }
        }
        
        $hora = date('H');
        if (!isset($limites[$ip][$hora])) {
            $limites[$ip][$hora] = 0;
        }
        
        $limites[$ip][$hora]++;
        
        file_put_contents($archivo, json_encode($limites));
        
        return $limites[$ip][$hora] <= 50;
    }
}
?>