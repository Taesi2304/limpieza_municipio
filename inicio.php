<?php
session_start();
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($usuario && $password) {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ? AND password = ?");
        $stmt->execute([$usuario, $password]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['usuario'] = $user['nombre_usuario'];
            $_SESSION['id_usuario'] = (int) $user['id_usuario'];
            $_SESSION['rol'] = $user['rol'];
            header('Location: captura.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: inicio.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Orden de Servicio </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="img/icono.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo2.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow card-form-panel">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="h5 mb-2 fw-semibold text-dark">Limpieza Pública Municipal</h3>
                            <p class="text-muted small mb-0">Ingresa los datos de inicio de sesión</p>
                        </div>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="POST" class="text-start">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" id="usuario" name="usuario" class="form-control" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success-custom w-100">Iniciar Sesión</button>
                        </form>

                        <!-- <div class="mt-3 p-3 bg-light rounded">
                            <small><strong>Usuarios de prueba:</strong></small><br>
                            <small>admin / 123456</small><br>
                            <small>capturista1 / 123456</small>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<footer class="footer">
    <div class="container footer-columns">
        <div class="footer-column">
            <p>PROGRAMACIÓN WEB 2026</p>
        </div>
        <div class="footer-column">
            <p>TRABAJO EN EQUIPO</p>
        </div>
        <div class="footer-column">
            <p>Integrantes: 
             <br> Jessica Gallegos Rodriguez
             <br> Stephany Chavez
             <br> Jan Karlo Armendariz 
             <br> Joel Garcia
             <br> Brandon Velazquez</p>
        </div>
    </div>
</footer>


</html>