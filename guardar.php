<?php
// movimientos/guardar.php
// Registra el movimiento y actualiza el stock dentro de UNA transacción:
// o se guardan las dos cosas, o no se guarda ninguna.
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id_producto = (int)($_POST['id_producto'] ?? 0);
$tipo        = $_POST['tipo'] ?? '';
$cantidad    = $_POST['cantidad'] ?? '';
$observacion = trim($_POST['observacion'] ?? '');

// ---- Validaciones del servidor ----
$errores = [];
if ($id_producto <= 0) {
    $errores[] = 'Selecciona un producto.';
}
if (!in_array($tipo, ['ENTRADA', 'SALIDA'], true)) {
    $errores[] = 'El tipo de movimiento no es válido.';
}
if (filter_var($cantidad, FILTER_VALIDATE_INT) === false || (int)$cantidad < 1) {
    $errores[] = 'La cantidad debe ser un número entero mayor que 0.';
}
if (mb_strlen($observacion) > 255) {
    $errores[] = 'La observación admite hasta 255 caracteres.';
}

$old = $_POST;

if ($errores) {
    flash('danger', implode(' ', $errores));
    $_SESSION['old'] = $old;
    header('Location: form.php');
    exit;
}

$cantidad = (int)$cantidad;

try {
    $pdo->beginTransaction();

    // FOR UPDATE bloquea la fila del producto mientras dura la transacción,
    // así dos movimientos simultáneos no pisan el stock del otro.
    $st = $pdo->prepare('SELECT stock_actual, stock_minimo, estado FROM producto WHERE id_producto = ? FOR UPDATE');
    $st->execute([$id_producto]);
    $prod = $st->fetch();

    if (!$prod || $prod['estado'] != 1) {
        throw new RuntimeException('El producto no existe o está desactivado.');
    }

    $stock = (int)$prod['stock_actual'];

    // Regla de negocio: una salida no puede dejar el stock bajo cero
    if ($tipo === 'SALIDA' && $cantidad > $stock) {
        throw new RuntimeException("Stock insuficiente: hay $stock unidades y se intentó sacar $cantidad.");
    }

    $nuevoStock = ($tipo === 'ENTRADA') ? $stock + $cantidad : $stock - $cantidad;

    // 1) Se guarda el movimiento (la fecha la pone la base de datos)
    $st = $pdo->prepare(
        'INSERT INTO movimiento (tipo, cantidad, observacion, id_producto, id_usuario)
         VALUES (?, ?, ?, ?, ?)'
    );
    $st->execute([$tipo, $cantidad, $observacion ?: null, $id_producto, $_SESSION['id_usuario']]);

    // 2) Se actualiza el stock del producto
    $st = $pdo->prepare('UPDATE producto SET stock_actual = ? WHERE id_producto = ?');
    $st->execute([$nuevoStock, $id_producto]);

    $pdo->commit();

    $mensaje = ($tipo === 'ENTRADA' ? 'Entrada' : 'Salida') . " registrada. Stock actual: $nuevoStock.";
    if ($nuevoStock < (int)$prod['stock_minimo']) {
        flash('warning', $mensaje . ' Atención: el producto quedó bajo el stock mínimo (' . (int)$prod['stock_minimo'] . ').');
    } else {
        flash('success', $mensaje);
    }
    header('Location: index.php');
    exit;

} catch (RuntimeException $e) {
    // Error de regla de negocio: se deshace todo y se muestra el motivo
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('danger', $e->getMessage());
    $_SESSION['old'] = $old;
    header('Location: form.php');
    exit;

} catch (PDOException $e) {
    // Error de base de datos: se deshace todo
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('danger', 'No se pudo registrar el movimiento.');
    $_SESSION['old'] = $old;
    header('Location: form.php');
    exit;
}
