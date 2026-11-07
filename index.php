<?php
// index.php (panel de inicio)
require_once __DIR__ . '/includes/auth.php';
requiere_login();

$titulo = 'Inicio';

$totalProductos  = $pdo->query('SELECT COUNT(*) FROM producto WHERE estado = 1')->fetchColumn();
$totalCategorias = $pdo->query('SELECT COUNT(*) FROM categoria WHERE estado = 1')->fetchColumn();

// Productos activos con stock bajo el mínimo (vista creada en el script SQL)
$stockBajo = $pdo->query('SELECT * FROM vista_stock_bajo ORDER BY nombre')->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Hola, <?= e($_SESSION['nombre']) ?></h1>
    <a class="btn btn-dark" href="<?= BASE_URL ?>/movimientos/form.php">Registrar movimiento</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <div class="text-muted">Productos activos</div>
            <div class="display-6"><?= (int)$totalProductos ?></div>
            <a href="<?= BASE_URL ?>/productos/index.php" class="stretched-link">Ver productos</a>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <div class="text-muted">Categorías activas</div>
            <div class="display-6"><?= (int)$totalCategorias ?></div>
            <a href="<?= BASE_URL ?>/categorias/index.php" class="stretched-link">Ver categorías</a>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm <?= $stockBajo ? 'border-danger' : '' ?>"><div class="card-body">
            <div class="text-muted">Con stock bajo</div>
            <div class="display-6 <?= $stockBajo ? 'text-danger' : '' ?>"><?= count($stockBajo) ?></div>
            <a href="<?= BASE_URL ?>/productos/index.php?bajo=1" class="stretched-link">Ver detalle</a>
        </div></div>
    </div>
</div>

<?php if ($stockBajo): ?>
<div class="card shadow-sm border-danger">
    <div class="card-header bg-danger text-white">Productos que necesitan reposición</div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th>Código</th><th>Nombre</th><th>Categoría</th>
                    <th class="text-end">Stock</th><th class="text-end">Mínimo</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($stockBajo as $p): ?>
                <tr>
                    <td><?= e($p['codigo']) ?></td>
                    <td><?= e($p['nombre']) ?></td>
                    <td><?= e($p['categoria']) ?></td>
                    <td class="text-end text-danger fw-bold"><?= (int)$p['stock_actual'] ?></td>
                    <td class="text-end"><?= (int)$p['stock_minimo'] ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-success"
                           href="<?= BASE_URL ?>/movimientos/form.php?id_producto=<?= (int)$p['id_producto'] ?>&tipo=ENTRADA">
                            Registrar entrada
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
    <div class="alert alert-success mb-0">Todos los productos activos están sobre su stock mínimo.</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
