<?php
// productos/index.php  (listado; la búsqueda y la alerta se agregan el Día 3)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

$titulo = 'Productos';

$sql = 'SELECT p.*, c.nombre AS categoria
        FROM producto p
        JOIN categoria c ON c.id_categoria = p.id_categoria
        ORDER BY p.estado DESC, p.nombre';
$productos = $pdo->query($sql)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Productos</h1>
    <a class="btn btn-dark" href="form.php">Nuevo producto</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th><th>Nombre</th><th>Categoría</th>
                    <th class="text-end">Precio</th><th class="text-end">Stock</th>
                    <th class="text-end">Mínimo</th><th>Estado</th><th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $p): ?>
                <tr>
                    <td><?= e($p['codigo']) ?></td>
                    <td><?= e($p['nombre']) ?></td>
                    <td><?= e($p['categoria']) ?></td>
                    <td class="text-end">$<?= number_format($p['precio'], 0, ',', '.') ?></td>
                    <td class="text-end"><?= (int)$p['stock_actual'] ?></td>
                    <td class="text-end"><?= (int)$p['stock_minimo'] ?></td>
                    <td>
                        <?php if ($p['estado']): ?>
                            <span class="badge text-bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">Desactivado</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="form.php?id=<?= (int)$p['id_producto'] ?>">Editar</a>
                        <form class="d-inline" method="post" action="estado.php"
                              onsubmit="return confirm('¿Cambiar el estado de este producto?');">
                            <input type="hidden" name="id_producto" value="<?= (int)$p['id_producto'] ?>">
                            <button class="btn btn-sm btn-outline-<?= $p['estado'] ? 'danger' : 'success' ?>">
                                <?= $p['estado'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$productos): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No hay productos registrados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
