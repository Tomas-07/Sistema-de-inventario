<?php
// categorias/index.php  (listado)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

$titulo = 'Categorías';
$categorias = $pdo->query('SELECT * FROM categoria ORDER BY estado DESC, nombre')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Categorías</h1>
    <a class="btn btn-dark" href="form.php">Nueva categoría</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nombre</th><th>Descripción</th><th>Estado</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($categorias as $c): ?>
                <tr>
                    <td><?= e($c['nombre']) ?></td>
                    <td><?= e($c['descripcion']) ?></td>
                    <td>
                        <?php if ($c['estado']): ?>
                            <span class="badge text-bg-success">Activa</span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">Inactiva</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="form.php?id=<?= (int)$c['id_categoria'] ?>">Editar</a>
                        <form class="d-inline" method="post" action="estado.php"
                              onsubmit="return confirm('¿Cambiar el estado de esta categoría?');">
                            <input type="hidden" name="id_categoria" value="<?= (int)$c['id_categoria'] ?>">
                            <button class="btn btn-sm btn-outline-<?= $c['estado'] ? 'danger' : 'success' ?>">
                                <?= $c['estado'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$categorias): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">No hay categorías registradas.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
