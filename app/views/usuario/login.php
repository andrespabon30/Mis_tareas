<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../config/seguridad.php';
$csrf_token = Seguridad::generarTokenCSRF();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Gestor de Tareas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { max-width: 450px; width: 100%; }
        .card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .card-header { background: linear-gradient(135deg, #4361ee, #3f37c9); color: white; padding: 30px; text-align: center; }
        .card-header h2 { margin: 0; font-size: 1.8rem; }
        .card-body { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #2d3748; }
        .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; }
        .form-control:focus { outline: none; border-color: #4361ee; }
        .btn { width: 100%; padding: 12px; border-radius: 50px; border: none; background: linear-gradient(135deg, #4361ee, #3f37c9); color: white; font-size: 1rem; cursor: pointer; font-weight: 500; }
        .btn:hover { transform: translateY(-2px); }
        .register-link { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .register-link a { color: #4361ee; text-decoration: none; }
        .alert { padding: 12px; border-radius: 10px; margin-bottom: 20px; }
        .alert-error { background: #fee2e2; color: #dc2626; border-left: 4px solid #dc2626; }
        .alert-success { background: #e6f4ea; color: #2e7d32; border-left: 4px solid #2e7d32; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-tasks"></i> Gestor de Tareas</h2>
                <p>Inicia sesión en tu cuenta</p>
            </div>
            <div class="card-body">
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        <?php 
                            if($_GET['error'] == 'credenciales') echo "Credenciales incorrectas.";
                            elseif($_GET['error'] == 'bloqueado') echo "Demasiados intentos. Cuenta bloqueada 15 minutos.";
                            elseif($_GET['error'] == 'email_invalido') echo "Email inválido.";
                        ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['registro']) && $_GET['registro'] == 'exitoso'): ?>
                    <div class="alert alert-success">¡Registro exitoso! Ahora puedes iniciar sesión.</div>
                <?php endif; ?>

                <form method="POST" action="../../controllers/UsuarioController.php">
                    <input type="hidden" name="accion" value="login">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" placeholder="tu@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn">Iniciar Sesión</button>
                </form>
                
                <div class="register-link">
                    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>