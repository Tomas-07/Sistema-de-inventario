<?php
// index.php (panel de inicio)
require_once __DIR__ . '/includes/auth.php';
requiere_login();

$titulo = 'Inicio';

$totalProductos  = $pdo->query('SELECT COUNT(*) FROM producto WHERE estado = 1')->fetchColumn();
$totalCategorias = $pdo->query('SELECT COUNT(*) FROM categoria WHERE estado = 1')->fetchColumn();

require __DIR__ . '/includes/header.php';
?>
<h1 class="h3 mb-4">Hola, <?= e($_SESSION['nombre']) ?></h1>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm"><div class="card-body">
            <div class="text-muted">Productos activos</div>
            <div class="display-6"><?= (int)$totalProductos ?></div>
            <a href="<?= BASE_URL ?>/productos/index.php" class="stretched-link">Ver productos</a>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm"><div class="card-body">
            <div class="text-muted">Categorías activas</div>
            <div class="display-6"><?= (int)$totalCategorias ?></div>
            <a href="<?= BASE_URL ?>/categorias/index.php" class="stretched-link">Ver categorías</a>
        </div></div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
