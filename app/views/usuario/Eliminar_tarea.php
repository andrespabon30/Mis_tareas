<?php
session_start();
if(!isset($_SESSION['usuario_id'])){ 
    header("Location: login.php"); 
    exit();
}

if(isset($_GET['id'])){
    require_once '../../../config/database.php';
    $db = (new Database())->connect();
    
    $id = $_GET['id'];
    
    $stmt = $db->prepare("DELETE FROM tareas WHERE id=? AND usuario_id=?");
    $stmt->execute([$id, $_SESSION['usuario_id']]);
}

header("Location: dashboard.php");
exit();
?>