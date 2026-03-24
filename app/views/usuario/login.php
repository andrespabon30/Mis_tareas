<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Gestor de Tareas</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container-custom {
            max-width: 450px;
            width: 100%;
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
            padding: 30px;
            text-align: center;
        }

        .card-header-modern h2 {
            margin: 0;
            font-size: 1.8rem;
        }

        .card-header-modern p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
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

        .btn-modern {
            display: inline-block;
            width: 100%;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-family: inherit;
            text-align: center;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            color: white;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .register-link a {
            color: #4361ee;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background: #e6f4ea;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        @media (max-width: 768px) {
            .card-header-modern {
                padding: 25px;
            }
            
            .card-body-modern {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="card-modern">
            <div class="card-header-modern">
                <h2><i class="fas fa-tasks"></i> Gestor de Tareas</h2>
                <p>Organiza tus actividades diarias</p>
            </div>
            <div class="card-body-modern">
                <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php 
                        if($_GET['error'] == 'credenciales') {
                            echo "Credenciales incorrectas. Verifica tu email y contraseña.";
                        } elseif($_GET['error'] == 'sesion') {
                            echo "Por favor, inicia sesión para continuar.";
                        } else {
                            echo "Error al iniciar sesión. Intenta nuevamente.";
                        }
                    ?>
                </div>
                <?php endif; ?>

                <?php if(isset($_GET['registro']) && $_GET['registro'] == 'exitoso'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    ¡Registro exitoso! Ahora puedes iniciar sesión.
                </div>
                <?php endif; ?>

                <form method="POST" action="../../controllers/UsuarioController.php">
                    <input type="hidden" name="accion" value="login">
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" placeholder="tu@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </button>
                </form>
                
                <div class="register-link">
                    <p>¿No tienes cuenta? <a href="registro.php"><i class="fas fa-user-plus"></i> Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>