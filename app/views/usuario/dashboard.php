<?php
session_start();
if(!isset($_SESSION['usuario_id'])){ 
    header("Location: login.php"); 
    exit();
}

require_once __DIR__ . '/../../../config/database.php';
$db = (new Database())->connect();

$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

$stmt = $db->prepare("SELECT * FROM tareas WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$tareas = $stmt->fetchAll();

$stmt = $db->prepare("SELECT 
    COUNT(*) as total,
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
    <title>Dashboard | Gestor de Tareas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .card-header { background: linear-gradient(135deg, #4361ee, #3f37c9); color: white; padding: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .card-header h2 { margin: 0; }
        .card-body { padding: 30px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 50px; text-decoration: none; font-weight: 500; margin: 5px; }
        .btn-primary { background: linear-gradient(135deg, #4361ee, #3f37c9); color: white; }
        .btn-success { background: linear-gradient(135deg, #4cc9f0, #4895ef); color: white; }
        .btn-danger { background: linear-gradient(135deg, #f72585, #b5179e); color: white; }
        .btn-warning { background: linear-gradient(135deg, #f8961e, #f48c06); color: white; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: linear-gradient(135deg, #f8f9fa, #e9ecef); padding: 20px; border-radius: 15px; text-align: center; }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: #4361ee; }
        table { width: 100%; border-collapse: collapse; }
        th { background: linear-gradient(135deg, #4361ee, #3f37c9); color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #e9ecef; }
        tr:hover { background: #f8f9fa; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; color: white; }
        .badge-pendiente { background: #f8961e; }
        .badge-completada { background: #4cc9f0; }
        @media (max-width: 768px) { table { font-size: 0.8rem; } th, td { padding: 8px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2><i class="fas fa-tasks"></i> Mis Tareas</h2>
                    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                </div>
                <div>
                    <a href="crear_tarea.php" class="btn btn-success"><i class="fas fa-plus"></i> Nueva Tarea</a>
                    <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Salir</a>
                </div>
            </div>
            <div class="card-body">
                <div class="stats">
                    <div class="stat-card"><div class="stat-number"><?php echo $stats['total']; ?></div><div>Total Tareas</div></div>
                    <div class="stat-card"><div class="stat-number" style="color:#f8961e;"><?php echo $stats['pendientes']; ?></div><div>Pendientes</div></div>
                    <div class="stat-card"><div class="stat-number" style="color:#4cc9f0;"><?php echo $stats['completadas']; ?></div><div>Completadas</div></div>
                </div>
                
                <?php if(count($tareas) > 0): ?>
                <table>
                    <thead><tr><th>Título</th><th>Descripción</th><th>Prioridad</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach($tareas as $tarea): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($tarea['titulo']); ?></strong></td>
                            <td><?php echo htmlspecialchars(substr($tarea['descripcion'], 0, 50)); ?></td>
                            <td><?php echo $tarea['prioridad']; ?></td>
                            <td><span class="badge badge-<?php echo $tarea['estado']; ?>"><?php echo $tarea['estado']; ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($tarea['fecha_creacion'])); ?></td>
                            <td>
                                <a href="editar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn btn-warning" style="padding:5px 12px;">Editar</a>
                                <a href="eliminar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn btn-danger" style="padding:5px 12px;" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="text-align:center; padding:40px;">
                    <i class="fas fa-inbox" style="font-size:4rem; color:#4361ee;"></i>
                    <h3>¡No tienes tareas!</h3>
                    <a href="crear_tarea.php" class="btn btn-primary">Crear mi primera tarea</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>