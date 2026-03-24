<?php
session_start();
if(!isset($_SESSION['usuario_id'])){ 
    header("Location: login.php"); 
    exit();
}

require_once '../../../config/database.php';
$db = (new Database())->connect();

$stmt = $db->prepare("SELECT `título`, `descripción`, `categoría`, prioridad, estado, fecha_creacion, fecha_vencimiento FROM tareas WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$tareas = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="mis_tareas_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, ['Título', 'Descripción', 'Categoría', 'Prioridad', 'Estado', 'Fecha Creación', 'Fecha Vencimiento']);

foreach($tareas as $tarea) {
    fputcsv($output, [
        $tarea['título'],
        $tarea['descripción'],
        $tarea['categoría'],
        $tarea['prioridad'],
        $tarea['estado'],
        $tarea['fecha_creacion'],
        $tarea['fecha_vencimiento']
    ]);
}

fclose($output);
exit();
?>