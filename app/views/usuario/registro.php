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
    <title>Registro | Gestor de Tareas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { max-width: 500px; width: 100%; }
        .card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .card-header { background: linear-gradient(135deg, #4361ee, #3f37c9); color: white; padding: 30px; text-align: center; }
        .card-header h2 { margin: 0; font-size: 1.8rem; }
        .card-body { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #2d3748; }
        .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; }
        .form-control:focus { outline: none; border-color: #4361ee; }
        .btn { width: 100%; padding: 12px; border-radius: 50px; border: none; background: linear-gradient(135deg, #4cc9f0, #4895ef); color: white; font-size: 1rem; cursor: pointer; font-weight: 500; }
        .btn:hover { transform: translateY(-2px); }
        .login-link { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .login-link a { color: #4361ee; text-decoration: none; }
        .alert { padding: 12px; border-radius: 10px; margin-bottom: 20px; }
        .alert-error { background: #fee2e2; color: #dc2626; border-left: 4px solid #dc2626; }
        .password-requirements { font-size: 0.7rem; color: #6c757d; margin-top: 5px; }
        .valid { color: #2e7d32; }
        .invalid { color: #dc2626; }
        ul { margin-left: 20px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-user-plus"></i> Crear Cuenta</h2>
                <p>Regístrate de forma segura</p>
            </div>
            <div class="card-body">
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        <?php 
                            if($_GET['error'] == 'email_existe') echo "Este correo ya está registrado.";
                            elseif($_GET['error'] == 'password_debil') echo "La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.";
                            elseif($_GET['error'] == 'email_invalido') echo "Email inválido.";
                            elseif($_GET['error'] == 'nombre_corto') echo "El nombre debe tener al menos 3 caracteres.";
                        ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="../../controllers/UsuarioController.php">
                    <input type="hidden" name="accion" value="registro">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Tu nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" placeholder="tu@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                        <div class="password-requirements">
                            Requisitos:
                            <ul>
                                <li id="req-length" class="invalid">✗ Mínimo 8 caracteres</li>
                                <li id="req-mayus" class="invalid">✗ Al menos una mayúscula</li>
                                <li id="req-num" class="invalid">✗ Al menos un número</li>
                            </ul>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Registrarse</button>
                </form>
                
                <div class="login-link">
                    <p>¿Ya tienes cuenta? <a href="login.php">Inicia Sesión</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function validarPassword() {
            const password = document.getElementById('password').value;
            
            const lengthValid = password.length >= 8;
            const mayusValid = /[A-Z]/.test(password);
            const numValid = /[0-9]/.test(password);
            
            document.getElementById('req-length').innerHTML = (lengthValid ? '✓' : '✗') + ' Mínimo 8 caracteres';
            document.getElementById('req-length').className = lengthValid ? 'valid' : 'invalid';
            
            document.getElementById('req-mayus').innerHTML = (mayusValid ? '✓' : '✗') + ' Al menos una mayúscula';
            document.getElementById('req-mayus').className = mayusValid ? 'valid' : 'invalid';
            
            document.getElementById('req-num').innerHTML = (numValid ? '✓' : '✗') + ' Al menos un número';
            document.getElementById('req-num').className = numValid ? 'valid' : 'invalid';
            
            return lengthValid && mayusValid && numValid;
        }
        
        document.getElementById('password').addEventListener('keyup', validarPassword);
    </script>
</body>
</html>