<?php
// movimientos/index.php  (historial de entradas y salidas)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

$titulo = 'Movimientos';

$idProducto = (int)($_GET['id_producto'] ?? 0);

$sql = 'SELECT m.*, p.codigo, p.nombre AS producto, u.nombre AS usuario
        FROM movimiento m
        JOIN producto p ON p.id_producto = m.id_producto
        JOIN usuario  u ON u.id_usuario  = m.id_usuario';
$params = [];
if ($idProducto > 0) {
    $sql .= ' WHERE m.id_producto = ?';
    $params[] = $idProducto;
}
$sql .= ' ORDER BY m.fecha_hora DESC, m.id_movimiento DESC LIMIT 100';

$st = $pdo->prepare($sql);
$st->execute($params);
$movimientos = $st->fetchAll();

$productos = $pdo->query('SELECT id_producto, codigo, nombre FROM producto ORDER BY nombre')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Movimientos de inventario</h1>
    <a class="btn btn-dark" href="form.php">Registrar movimiento</a>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-6">
        <select class="form-select" name="id_producto" onchange="this.form.submit()">
            <option value="0">Todos los productos</option>
            <?php foreach ($productos as $p): ?>
                <option value="<?= (int)$p['id_producto'] ?>" <?= $idProducto === (int)$p['id_producto'] ? 'selected' : '' ?>>
                    <?= e($p['codigo']) ?> · <?= e($p['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($idProducto > 0): ?>
        <div class="col-auto"><a class="btn btn-link" href="index.php">Limpiar</a></div>
    <?php endif; ?>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th><th>Producto</th><th>Tipo</th>
                    <th class="text-end">Cantidad</th><th>Observación</th><th>Usuario</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($movimientos as $m): ?>
                <tr>
                    <td><?= e(date('d-m-Y H:i', strtotime($m['fecha_hora']))) ?></td>
                    <td><?= e($m['codigo']) ?> · <?= e($m['producto']) ?></td>
                    <td>
                        <?php if ($m['tipo'] === 'ENTRADA'): ?>
                            <span class="badge text-bg-success">Entrada</span>
                        <?php else: ?>
                            <span class="badge text-bg-warning">Salida</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end"><?= $m['tipo'] === 'ENTRADA' ? '+' : '−' ?><?= (int)$m['cantidad'] ?></td>
                    <td><?= e($m['observacion']) ?></td>
                    <td><?= e($m['usuario']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$movimientos): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No hay movimientos registrados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<p class="text-muted small mt-2">Se muestran los 100 movimientos más recientes.</p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
