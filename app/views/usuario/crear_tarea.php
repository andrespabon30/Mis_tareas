<?php
session_start();
if(!isset($_SESSION['usuario_id'])){ 
    header("Location: login.php"); 
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    require_once '../../../config/database.php';
    $db = (new Database())->connect();
    
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $prioridad = $_POST['prioridad'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?: null;
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
    <title>Nueva Tarea | Gestor de Tareas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container-custom {
            max-width: 600px;
            margin: 0 auto;
        }

        .card-modern {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            margin-top: 50px;
        }

        .card-header-modern {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            color: white;
            padding: 25px 30px;
        }

        .card-header-modern h2 {
            margin: 0;
            font-size: 1.8rem;
        }

        .card-header-modern p {
            margin: 5px 0 0;
            opacity: 0.9;
        }

        .card-body-modern {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2d3748;
        }

        .form-group label i {
            margin-right: 8px;
            color: #4361ee;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        select.form-control {
            cursor: pointer;
            background: white;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn-modern {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-family: inherit;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f72585, #b5179e);
            color: white;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .button-group .btn-modern {
            flex: 1;
            text-align: center;
        }

        @media (max-width: 768px) {
            .card-header-modern {
                padding: 20px;
            }
            
            .card-body-modern {
                padding: 20px;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="card-modern">
            <div class="card-header-modern">
                <h2><i class="fas fa-plus-circle"></i> Nueva Tarea</h2>
                <p>Completa los detalles de tu nueva tarea</p>
            </div>
            <div class="card-body-modern">
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-heading"></i> Título *</label>
                        <input type="text" name="titulo" class="form-control" placeholder="Ej: Completar proyecto PHP" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Descripción</label>
                        <textarea name="descripcion" class="form-control" placeholder="Describe los detalles de la tarea..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-bookmark"></i> Categoría</label>
                        <select name="categoria" class="form-control">
                            <option value="Trabajo">💼 Trabajo</option>
                            <option value="Estudio">📚 Estudio</option>
                            <option value="Personal">🏠 Personal</option>
                            <option value="Urgente">⚠️ Urgente</option>
                            <option value="General" selected>📋 General</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-flag"></i> Prioridad</label>
                        <select name="prioridad" class="form-control">
                            <option value="alta">🔴 Alta</option>
                            <option value="media" selected>🟡 Media</option>
                            <option value="baja">🟢 Baja</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Fecha de vencimiento (opcional)</label>
                        <input type="date" name="fecha_vencimiento" class="form-control">
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn-modern btn-primary">
                            <i class="fas fa-save"></i> Guardar Tarea
                        </button>
                        <a href="dashboard.php" class="btn-modern btn-danger">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>