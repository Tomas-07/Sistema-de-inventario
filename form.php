<?php
// movimientos/form.php  (registrar una entrada o una salida)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

// Valores iniciales (pueden venir en la URL desde el panel de stock bajo)
$m = [
    'id_producto' => (int)($_GET['id_producto'] ?? 0),
    'tipo'        => in_array($_GET['tipo'] ?? '', ['ENTRADA', 'SALIDA'], true) ? $_GET['tipo'] : 'ENTRADA',
    'cantidad'    => '',
    'observacion' => '',
];

// Si hubo un error al guardar, se recuperan los datos escritos
if (!empty($_SESSION['old'])) {
    $m = array_merge($m, $_SESSION['old']);
    unset($_SESSION['old']);
}

// Solo productos activos pueden recibir movimientos
$productos = $pdo->query('SELECT id_producto, codigo, nombre, stock_actual FROM producto WHERE estado = 1 ORDER BY nombre')->fetchAll();

$titulo = 'Registrar movimiento';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-3">Registrar movimiento</h1>

<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form id="form-movimiento" method="post" action="guardar.php" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label" for="id_producto">Producto *</label>
                <select class="form-select" id="id_producto" name="id_producto" required>
                    <option value="">Selecciona...</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?= (int)$p['id_producto'] ?>"
                                data-stock="<?= (int)$p['stock_actual'] ?>"
                                <?= (int)$m['id_producto'] === (int)$p['id_producto'] ? 'selected' : '' ?>>
                            <?= e($p['codigo']) ?> · <?= e($p['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Selecciona un producto.</div>
                <div class="form-text" id="info-stock"></div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="tipo">Tipo *</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="ENTRADA" <?= $m['tipo'] === 'ENTRADA' ? 'selected' : '' ?>>Entrada (suma stock)</option>
                        <option value="SALIDA"  <?= $m['tipo'] === 'SALIDA'  ? 'selected' : '' ?>>Salida (resta stock)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="cantidad">Cantidad *</label>
                    <input class="form-control" type="number" id="cantidad" name="cantidad"
                           min="1" step="1" value="<?= e($m['cantidad']) ?>" required>
                    <div class="invalid-feedback" id="msg-cantidad">Ingresa una cantidad entera mayor que 0.</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="observacion">Observación</label>
                <input class="form-control" type="text" id="observacion" name="observacion" maxlength="255"
                       value="<?= e($m['observacion']) ?>" placeholder="Ej: compra a proveedor, venta en tienda">
            </div>

            <button class="btn btn-dark" type="submit">Registrar</button>
            <a class="btn btn-link" href="index.php">Cancelar</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
