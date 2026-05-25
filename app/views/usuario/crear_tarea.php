<?php
session_start();
if(!isset($_SESSION['usuario_id'])){ 
    header("Location: login.php"); 
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    require_once __DIR__ . '/../../../config/database.php';
    $db = (new Database())->connect();
    
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = $_POST['categoria'] ?? 'General';
    $prioridad = $_POST['prioridad'] ?? 'media';
    $fecha_vencimiento = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null;
    $usuario_id = $_SESSION['usuario_id'];
    
    $stmt = $db->prepare("INSERT INTO tareas (usuario_id, titulo, descripcion, categoria, prioridad, fecha_vencimiento) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $titulo, $descripcion, $categoria, $prioridad, $fecha_vencimiento]);
    
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Tarea</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .card-header { background: linear-gradient(135deg, #4361ee, #3f37c9); color: white; padding: 25px; }
        .card-body { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; font-family: inherit; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #4361ee; }
        .btn { padding: 12px 25px; border-radius: 50px; border: none; cursor: pointer; font-weight: 500; }
        .btn-primary { background: linear-gradient(135deg, #4361ee, #3f37c9); color: white; }
        .btn-danger { background: linear-gradient(135deg, #f72585, #b5179e); color: white; }
        button { width: 100%; margin-bottom: 10px; }
        .button-group { display: flex; gap: 15px; margin-top: 20px; }
        .button-group a, .button-group button { flex: 1; text-align: center; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-plus-circle"></i> Nueva Tarea</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Título *</label>
                        <input type="text" name="titulo" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="categoria">
                            <option value="Trabajo">💼 Trabajo</option>
                            <option value="Estudio">📚 Estudio</option>
                            <option value="Personal">🏠 Personal</option>
                            <option value="Urgente">⚠️ Urgente</option>
                            <option value="General">📋 General</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Prioridad</label>
                        <select name="prioridad">
                            <option value="alta">🔴 Alta</option>
                            <option value="media" selected>🟡 Media</option>
                            <option value="baja">🟢 Baja</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha de vencimiento</label>
                        <input type="date" name="fecha_vencimiento">
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="dashboard.php" class="btn btn-danger">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>