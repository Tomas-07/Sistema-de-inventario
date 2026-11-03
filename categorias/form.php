<?php
// categorias/form.php  (crear y editar con el mismo formulario)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

$id  = (int)($_GET['id'] ?? 0);
$cat = ['nombre' => '', 'descripcion' => ''];

if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM categoria WHERE id_categoria = ?');
    $st->execute([$id]);
    $cat = $st->fetch();
    if (!$cat) {
        flash('danger', 'La categoría no existe.');
        header('Location: index.php');
        exit;
    }
}

// Si hubo un error al guardar, se recuperan los datos escritos
if (!empty($_SESSION['old'])) {
    $cat = array_merge($cat, $_SESSION['old']);
    unset($_SESSION['old']);
}

$titulo = $id > 0 ? 'Editar categoría' : 'Nueva categoría';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-3"><?= e($titulo) ?></h1>

<div class="card shadow-sm" style="max-width: 560px;">
    <div class="card-body">
        <form method="post" action="guardar.php">
            <input type="hidden" name="id_categoria" value="<?= $id ?>">

            <div class="mb-3">
                <label class="form-label" for="nombre">Nombre *</label>
                <input class="form-control" id="nombre" name="nombre" maxlength="80"
                       value="<?= e($cat['nombre']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="descripcion">Descripción</label>
                <input class="form-control" id="descripcion" name="descripcion" maxlength="255"
                       value="<?= e($cat['descripcion']) ?>">
            </div>

            <button class="btn btn-dark" type="submit">Guardar</button>
            <a class="btn btn-link" href="index.php">Cancelar</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
