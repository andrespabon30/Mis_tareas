<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/seguridad.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Seguridad::configurarSesion();

$db = (new Database())->connect();

// Rate limiting por IP
$ip = $_SERVER['REMOTE_ADDR'];
if (!Seguridad::rateLimiting($ip)) {
    die("Demasiados intentos. Por favor, espera un momento.");
}

// REGISTRO
if(isset($_POST['accion']) && $_POST['accion'] == "registro"){
    
    if (!isset($_POST['csrf_token']) || !Seguridad::verificarTokenCSRF($_POST['csrf_token'])) {
        die("Error de seguridad");
    }
    
    $nombre = Seguridad::limpiarTexto($_POST['nombre']);
    $email = Seguridad::limpiarEmail($_POST['email']);
    $password = $_POST['password'];
    
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
        } else {
            header("Location: ../views/usuario/registro.php?error=general");
        }
        exit();
    }
}

// LOGIN
if(isset($_POST['accion']) && $_POST['accion'] == "login"){
    
    if (!isset($_POST['csrf_token']) || !Seguridad::verificarTokenCSRF($_POST['csrf_token'])) {
        die("Error de seguridad");
    }
    
    $email = Seguridad::limpiarEmail($_POST['email']);
    $password = $_POST['password'];
    
    if (!$email) {
        header("Location: ../views/usuario/login.php?error=email_invalido");
        exit();
    }
    
    if (Seguridad::estaBloqueado($db, $email)) {
        header("Location: ../views/usuario/login.php?error=bloqueado");
        exit();
    }
    
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email=?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if($usuario && password_verify($password, $usuario['password'])){
        Seguridad::resetearIntentos($db, $email);
        
        $stmt = $db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
        $stmt->execute([$usuario['id']]);
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        header("Location: ../views/usuario/dashboard.php");
        exit();
    } else {
        Seguridad::registrarIntentoFallido($db, $email);
        header("Location: ../views/usuario/login.php?error=credenciales");
        exit();
    }
}

header("Location: ../views/usuario/login.php");
exit();
?>