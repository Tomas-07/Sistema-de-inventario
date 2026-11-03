<?php
// productos/form.php  (crear y editar con el mismo formulario)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

$id = (int)($_GET['id'] ?? 0);
$p  = [
    'codigo' => '', 'nombre' => '', 'descripcion' => '',
    'precio' => '0', 'stock_actual' => '0', 'stock_minimo' => '0', 'id_categoria' => '',
];

if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM producto WHERE id_producto = ?');
    $st->execute([$id]);
    $p = $st->fetch();
    if (!$p) {
        flash('danger', 'El producto no existe.');
        header('Location: index.php');
        exit;
    }
}

// Si hubo un error al guardar, se recuperan los datos escritos
if (!empty($_SESSION['old'])) {
    $p = array_merge($p, $_SESSION['old']);
    unset($_SESSION['old']);
}

// Solo categorías activas en el selector
$categorias = $pdo->query('SELECT id_categoria, nombre FROM categoria WHERE estado = 1 ORDER BY nombre')->fetchAll();

$titulo = $id > 0 ? 'Editar producto' : 'Nuevo producto';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-3"><?= e($titulo) ?></h1>

<div class="card shadow-sm" style="max-width: 720px;">
    <div class="card-body">
        <form method="post" action="guardar.php">
            <input type="hidden" name="id_producto" value="<?= $id ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="codigo">Código *</label>
                    <!-- El código no se puede cambiar una vez creado -->
                    <input class="form-control" id="codigo" name="codigo" maxlength="30"
                           value="<?= e($p['codigo']) ?>" <?= $id > 0 ? 'readonly' : 'required' ?>>
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="nombre">Nombre *</label>
                    <input class="form-control" id="nombre" name="nombre" maxlength="120"
                           value="<?= e($p['nombre']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label" for="descripcion">Descripción</label>
                    <input class="form-control" id="descripcion" name="descripcion" maxlength="255"
                           value="<?= e($p['descripcion']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="id_categoria">Categoría *</label>
                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                        <option value="">Selecciona...</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= (int)$c['id_categoria'] ?>"
                                <?= (int)$p['id_categoria'] === (int)$c['id_categoria'] ? 'selected' : '' ?>>
                                <?= e($c['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="precio">Precio *</label>
                    <input class="form-control" type="number" id="precio" name="precio"
                           min="0" step="0.01" value="<?= e($p['precio']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="stock_actual">Stock actual *</label>
                    <input class="form-control" type="number" id="stock_actual" name="stock_actual"
                           min="0" step="1" value="<?= e($p['stock_actual']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="stock_minimo">Stock mínimo *</label>
                    <input class="form-control" type="number" id="stock_minimo" name="stock_minimo"
                           min="0" step="1" value="<?= e($p['stock_minimo']) ?>" required>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-dark" type="submit">Guardar</button>
                <a class="btn btn-link" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
