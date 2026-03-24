<?php
require_once '../config/database.php';
session_start();

echo "<h2>Sistema de Gestión de Tareas PRO</h2>";
echo "<a href='../app/views/usuario/login.php'>Iniciar Sesión</a> | ";
echo "<a href='../app/views/usuario/registro.php'>Registrarse</a>";
