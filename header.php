<?php
// includes/header.php
// Requiere: auth.php cargado y (opcional) la variable $titulo.
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Panel') ?> · TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">TecnoStock</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/productos/index.php">Productos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/categorias/index.php">Categorías</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/movimientos/index.php">Movimientos</a></li>
            </ul>
            <span class="navbar-text me-3"><?= e($_SESSION['nombre'] ?? '') ?></span>
            <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/logout.php">Cerrar sesión</a>
        </div>
    </div>
</nav>
<main class="container pb-5">
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['tipo']) ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>
