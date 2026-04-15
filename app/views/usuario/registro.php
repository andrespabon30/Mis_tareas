<?php 
session_start();
require_once '../../../config/seguridad.php';
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
        .container-custom { max-width: 450px; width: 100%; }
        .card-modern { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .card-header-modern { background: linear-gradient(135deg, #4361ee, #3f37c9); color: white; padding: 30px; text-align: center; }
        .card-header-modern h2 { margin: 0; font-size: 1.8rem; }
        .card-header-modern p { margin: 8px 0 0; opacity: 0.9; font-size: 0.9rem; }
        .card-body-modern { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #2d3748; }
        .form-group label i { margin-right: 8px; color: #4361ee; }
        .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; font-family: inherit; transition: all 0.3s ease; }
        .form-control:focus { outline: none; border-color: #4361ee; box-shadow: 0 0 0 3px rgba(67,97,238,0.1); }
        .btn-modern { display: inline-block; width: 100%; padding: 12px 25px; border-radius: 50px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border: none; cursor: pointer; font-size: 1rem; font-family: inherit; text-align: center; }
        .btn-modern:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .btn-success { background: linear-gradient(135deg, #4cc9f0, #4895ef); color: white; }
        .login-link { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .login-link a { color: #4361ee; text-decoration: none; font-weight: 500; }
        .login-link a:hover { text-decoration: underline; }
        .alert { padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-error { background: #fee2e2; color: #dc2626; border-left: 4px solid #dc2626; }
        .password-requirements { font-size: 0.7rem; color: #6c757d; margin-top: 5px; }
        .password-requirements ul { margin-left: 20px; margin-top: 5px; }
        .valid { color: #2e7d32; }
        .invalid { color: #dc2626; }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="card-modern">
            <div class="card-header-modern">
                <h2><i class="fas fa-user-plus"></i> Crear Cuenta</h2>
                <p>Regístrate de forma segura</p>
            </div>
            <div class="card-body-modern">
                <?php if(isset($_GET['error'])): ?>
                    <?php if($_GET['error'] == 'email_existe'): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        Este correo ya está registrado. <a href="login.php" style="color:#dc2626;">Inicia sesión</a>
                    </div>
                    <?php elseif($_GET['error'] == 'password_debil'): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-shield-alt"></i>
                        La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.
                    </div>
                    <?php elseif($_GET['error'] == 'email_invalido'): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-envelope"></i>
                        Email inválido. Por favor, verifica.
                    </div>
                    <?php elseif($_GET['error'] == 'nombre_corto'): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-user"></i>
                        El nombre debe tener al menos 3 caracteres.
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form method="POST" action="../../controllers/UsuarioController.php" onsubmit="return validarFormulario()">
                    <input type="hidden" name="accion" value="registro">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nombre Completo</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Tu nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="tu@email.com" required autocomplete="off">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                        <div class="password-requirements">
                            Requisitos:
                            <ul id="password-requirements">
                                <li id="req-length" class="invalid">✗ Mínimo 8 caracteres</li>
                                <li id="req-mayus" class="invalid">✗ Al menos una mayúscula</li>
                                <li id="req-num" class="invalid">✗ Al menos un número</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirmar Contraseña</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Repite tu contraseña" required>
                    </div>
                    
                    <button type="submit" class="btn-modern btn-success">
                        <i class="fas fa-check-circle"></i> Registrarse
                    </button>
                </form>
                
                <div class="login-link">
                    <p>¿Ya tienes cuenta? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Inicia Sesión</a></p>
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
        
        function validarFormulario() {
            if (!validarPassword()) {
                alert('La contraseña no cumple con los requisitos de seguridad');
                return false;
            }
            
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if (password !== confirm) {
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            const nombre = document.getElementById('nombre').value;
            if (nombre.length < 3) {
                alert('El nombre debe tener al menos 3 caracteres');
                return false;
            }
            
            return true;
        }
        
        document.getElementById('password').addEventListener('keyup', validarPassword);
        document.getElementById('confirm_password').addEventListener('keyup', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            if (password !== confirm) {
                this.style.borderColor = '#dc2626';
            } else {
                this.style.borderColor = '#2e7d32';
            }
        });
    </script>
</body>
</html>