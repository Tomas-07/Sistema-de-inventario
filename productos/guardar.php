<?php
// productos/guardar.php  (INSERT o UPDATE según venga el id)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id           = (int)($_POST['id_producto'] ?? 0);
$codigo       = strtoupper(trim($_POST['codigo'] ?? ''));
$nombre       = trim($_POST['nombre'] ?? '');
$descripcion  = trim($_POST['descripcion'] ?? '');
$precio       = $_POST['precio'] ?? '';
$stock_actual = $_POST['stock_actual'] ?? '';
$stock_minimo = $_POST['stock_minimo'] ?? '';
$id_categoria = (int)($_POST['id_categoria'] ?? 0);

// ---- Validaciones del servidor (se repiten aunque el navegador ya valide) ----
$errores = [];

if ($id === 0 && ($codigo === '' || mb_strlen($codigo) > 30)) {
    $errores[] = 'El código es obligatorio (máx. 30 caracteres).';
}
if ($nombre === '' || mb_strlen($nombre) > 120) {
    $errores[] = 'El nombre es obligatorio (máx. 120 caracteres).';
}
if (mb_strlen($descripcion) > 255) {
    $errores[] = 'La descripción admite hasta 255 caracteres.';
}
if (!is_numeric($precio) || $precio < 0) {
    $errores[] = 'El precio debe ser un número mayor o igual a 0.';
}
if (filter_var($stock_actual, FILTER_VALIDATE_INT) === false || $stock_actual < 0) {
    $errores[] = 'El stock actual debe ser un entero mayor o igual a 0.';
}
if (filter_var($stock_minimo, FILTER_VALIDATE_INT) === false || $stock_minimo < 0) {
    $errores[] = 'El stock mínimo debe ser un entero mayor o igual a 0.';
}

// La categoría debe existir y estar activa
$st = $pdo->prepare('SELECT 1 FROM categoria WHERE id_categoria = ? AND estado = 1');
$st->execute([$id_categoria]);
if (!$st->fetch()) {
    $errores[] = 'Selecciona una categoría válida.';
}

$old    = $_POST;
$volver = 'form.php' . ($id > 0 ? '?id=' . $id : '');

if ($errores) {
    flash('danger', implode(' ', $errores));
    $_SESSION['old'] = $old;
    header('Location: ' . $volver);
    exit;
}

try {
    if ($id > 0) {
        // El código no se modifica
        $st = $pdo->prepare(
            'UPDATE producto
             SET nombre = ?, descripcion = ?, precio = ?, stock_actual = ?, stock_minimo = ?, id_categoria = ?
             WHERE id_producto = ?'
        );
        $st->execute([$nombre, $descripcion ?: null, $precio, (int)$stock_actual, (int)$stock_minimo, $id_categoria, $id]);
        flash('success', 'Producto actualizado.');
    } else {
        $st = $pdo->prepare(
            'INSERT INTO producto (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([$codigo, $nombre, $descripcion ?: null, $precio, (int)$stock_actual, (int)$stock_minimo, $id_categoria]);
        flash('success', 'Producto registrado.');
    }
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    // 1062 = valor duplicado (el código tiene UNIQUE)
    if (($e->errorInfo[1] ?? 0) == 1062) {
        flash('danger', 'Ya existe un producto con ese código.');
    } else {
        flash('danger', 'No se pudo guardar el producto.');
    }
    $_SESSION['old'] = $old;
    header('Location: ' . $volver);
    exit;
}
