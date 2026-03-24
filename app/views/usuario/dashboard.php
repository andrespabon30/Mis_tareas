<?php
session_start();
if(!isset($_SESSION['usuario_id'])){ 
    header("Location: login.php"); 
    exit();
}

require_once '../../../config/database.php';
$db = (new Database())->connect();

$stmt = $db->prepare("SELECT * FROM tareas WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$tareas = $stmt->fetchAll();

$stmt = $db->prepare("SELECT COUNT(*) as total, 
                      SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                      SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas 
                      FROM tareas WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$stats = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tareas</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .card-modern {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
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

        .btn-modern {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 5px;
            border: none;
            cursor: pointer;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-success {
            background: linear-gradient(135deg, #4cc9f0, #4895ef);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f72585, #b5179e);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f8961e, #f48c06);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #4361ee;
        }

        .stat-label {
            color: #6c757d;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .table-modern {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 20px;
        }

        .table-modern th {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 500;
        }

        .table-modern td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .table-modern tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
            display: inline-block;
        }

        .badge-pendiente {
            background: #f8961e;
        }

        .badge-completada {
            background: #4cc9f0;
        }

        .empty-state {
            text-align: center;
            padding: 60px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #4361ee;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: #2d3748;
        }

        .empty-state p {
            color: #6c757d;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .table-modern {
                font-size: 0.8rem;
            }
            
            .table-modern th,
            .table-modern td {
                padding: 8px 10px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .card-header-modern {
                padding: 20px;
            }
            
            .card-body-modern {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="card-modern">
            <div class="card-header-modern">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h2><i class="fas fa-tasks"></i> Mis Tareas</h2>
                        <p>Bienvenido, <?php echo $_SESSION['nombre']; ?></p>
                    </div>
                    <div>
                        <a href="crear_tarea.php" class="btn-modern btn-success"><i class="fas fa-plus"></i> Nueva Tarea</a>
                        <a href="logout.php" class="btn-modern btn-danger"><i class="fas fa-sign-out-alt"></i> Salir</a>
                    </div>
                </div>
            </div>
            
            <div class="card-body-modern">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total']; ?></div>
                        <div class="stat-label">Total Tareas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: #f8961e;"><?php echo $stats['pendientes']; ?></div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: #4cc9f0;"><?php echo $stats['completadas']; ?></div>
                        <div class="stat-label">Completadas</div>
                    </div>
                </div>
                
                <?php if(count($tareas) > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th><i class="fas fa-tag"></i> Título</th>
                                <th><i class="fas fa-align-left"></i> Descripción</th>
                                <th><i class="fas fa-bookmark"></i> Categoría</th>
                                <th><i class="fas fa-flag"></i> Prioridad</th>
                                <th><i class="fas fa-check-circle"></i> Estado</th>
                                <th><i class="fas fa-calendar"></i> Fecha</th>
                                <th><i class="fas fa-cog"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tareas as $tarea): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($tarea['titulo']); ?></strong></td>
                                <td><?php echo htmlspecialchars(substr($tarea['descripcion'], 0, 50)); ?></td>
                                <td><span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem;"><?php echo htmlspecialchars($tarea['categoria']); ?></span></td>
                                <td><?php echo $tarea['prioridad'] == 'alta' ? '🔴 Alta' : ($tarea['prioridad'] == 'media' ? '🟡 Media' : '🟢 Baja'); ?></td>
                                <td><span class="badge badge-<?php echo $tarea['estado']; ?>"><?php echo $tarea['estado'] == 'pendiente' ? '⏳ Pendiente' : '✅ Completada'; ?></span></td>
                                <td><?php echo date('d/m/Y', strtotime($tarea['fecha_creacion'])); ?></td>
                                <td>
                                    <a href="editar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn-modern btn-warning" style="padding: 5px 12px; font-size: 0.8rem; display: inline-block;"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="eliminar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn-modern btn-danger" style="padding: 5px 12px; font-size: 0.8rem; display: inline-block;" onclick="return confirm('¿Eliminar esta tarea?')"><i class="fas fa-trash"></i> Eliminar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>¡No tienes tareas!</h3>
                    <p>Haz clic en el botón "Nueva Tarea" para comenzar a organizar tu día.</p>
                    <a href="crear_tarea.php" class="btn-modern btn-primary">Crear mi primera tarea</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>