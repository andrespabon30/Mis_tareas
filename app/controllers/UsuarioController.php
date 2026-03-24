<?php
require_once '../../config/database.php';
session_start();

$db = (new Database())->connect();

if($_POST['accion'] == "registro"){
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?,?,?)");
        $stmt->execute([$nombre, $email, $password]);
        
        header("Location: ../views/usuario/login.php?registro=exitoso");
        exit();
        
    } catch(PDOException $e) {
        // Verificar si es error de email duplicado
        if($e->getCode() == 23000 || strpos($e->getMessage(), 'Duplicate entry') !== false) {
            header("Location: ../views/usuario/registro.php?error=email_existe");
            exit();
        } else {
            header("Location: ../views/usuario/registro.php?error=general");
            exit();
        }
    }
}

if($_POST['accion'] == "login"){
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email=?");
    $stmt->execute([$_POST['email']]);
    $usuario = $stmt->fetch();

    if($usuario && password_verify($_POST['password'], $usuario['password'])){
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        header("Location: ../views/usuario/dashboard.php");
        exit();
    } else {
        header("Location: ../views/usuario/login.php?error=credenciales");
        exit();
    }
}
?>