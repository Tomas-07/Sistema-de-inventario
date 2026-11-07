<?php
// login.php
// Valida correo y clave contra la tabla usuario y crea la sesión.
require_once __DIR__ . '/includes/auth.php';

// Si ya inició sesión, va directo al panel
if (!empty($_SESSION['id_usuario'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$error = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave  = $_POST['clave'] ?? '';

    if ($correo === '' || $clave === '') {
        $error = 'Ingresa tu correo y tu clave.';
    } else {
        // Consulta preparada: el correo nunca se concatena en el SQL
        $st = $pdo->prepare('SELECT id_usuario, nombre, clave FROM usuario WHERE correo = ? AND estado = 1');
        $st->execute([$correo]);
        $usuario = $st->fetch();

        // password_verify compara la clave escrita con el hash guardado
        if ($usuario && password_verify($clave, $usuario['clave'])) {
            session_regenerate_id(true); // evita fijación de sesión
            $_SESSION['id_usuario'] = (int)$usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre'];
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }
        // Mensaje genérico: no revela si falló el correo o la clave
        $error = 'Correo o clave incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 420px; margin-top: 12vh;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 text-center mb-1">TecnoStock</h1>
            <p class="text-center text-muted mb-4">Control de inventario</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label class="form-label" for="correo">Correo</label>
                    <input class="form-control" type="email" id="correo" name="correo"
                           value="<?= e($correo) ?>" required autofocus>
                    <div class="invalid-feedback">Ingresa un correo válido.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="clave">Clave</label>
                    <input class="form-control" type="password" id="clave" name="clave" required>
                    <div class="invalid-feedback">Ingresa tu clave.</div>
                </div>
                <button class="btn btn-dark w-100" type="submit">Ingresar</button>
            </form>
        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>/assets/js/validaciones.js"></script>
</body>
</html>
