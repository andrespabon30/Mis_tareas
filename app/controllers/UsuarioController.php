<?php
require_once '../../config/database.php';
require_once '../../config/seguridad.php';
session_start();

// Configurar sesión segura
Seguridad::configurarSesion();

$db = (new Database())->connect();

// Rate limiting por IP
$ip = $_SERVER['REMOTE_ADDR'];
if (!Seguridad::rateLimiting($ip)) {
    die("Demasiados intentos. Por favor, espera un momento.");
}

if($_POST['accion'] == "registro"){
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !Seguridad::verificarTokenCSRF($_POST['csrf_token'])) {
        die("Error de seguridad");
    }
    
    // Limpiar y validar datos
    $nombre = Seguridad::limpiarTexto($_POST['nombre']);
    $email = Seguridad::limpiarEmail($_POST['email']);
    $password = $_POST['password'];
    
    // Validaciones
    if (!$email) {
        header("Location: ../views/usuario/registro.php?error=email_invalido");
        exit();
    }
    
    if (!Seguridad::validarPassword($password)) {
        header("Location: ../views/usuario/registro.php?error=password_debil");
        exit();
    }
    
    if (strlen($nombre) < 3) {
        header("Location: ../views/usuario/registro.php?error=nombre_corto");
        exit();
    }
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $ip_registro = $_SERVER['REMOTE_ADDR'];
    
    try {
        $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password, ip_registro) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $password_hash, $ip_registro]);
        
        header("Location: ../views/usuario/login.php?registro=exitoso");
        exit();
        
    } catch(PDOException $e) {
        if($e->getCode() == 23000) {
            header("Location: ../views/usuario/registro.php?error=email_existe");
            exit();
        } else {
            header("Location: ../views/usuario/registro.php?error=general");
            exit();
        }
    }
}

if($_POST['accion'] == "login"){
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !Seguridad::verificarTokenCSRF($_POST['csrf_token'])) {
        die("Error de seguridad");
    }
    
    $email = Seguridad::limpiarEmail($_POST['email']);
    $password = $_POST['password'];
    
    if (!$email) {
        header("Location: ../views/usuario/login.php?error=email_invalido");
        exit();
    }
    
    // Verificar si usuario está bloqueado
    if (Seguridad::estaBloqueado($db, $email)) {
        header("Location: ../views/usuario/login.php?error=bloqueado");
        exit();
    }
    
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email=?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if($usuario && password_verify($password, $usuario['password'])){
        // Login exitoso - resetear intentos
        Seguridad::resetearIntentos($db, $email);
        
        // Actualizar último login
        $stmt = $db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
        $stmt->execute([$usuario['id']]);
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        header("Location: ../views/usuario/dashboard.php");
        exit();
    } else {
        // Login fallido - registrar intento
        Seguridad::registrarIntentoFallido($db, $email);
        header("Location: ../views/usuario/login.php?error=credenciales");
        exit();
    }
}
?>