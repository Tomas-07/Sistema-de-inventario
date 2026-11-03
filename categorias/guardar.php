<?php
// categorias/guardar.php  (INSERT o UPDATE según venga el id)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id          = (int)($_POST['id_categoria'] ?? 0);
$nombre      = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

// Validaciones del servidor
$errores = [];
if ($nombre === '' || mb_strlen($nombre) > 80) {
    $errores[] = 'El nombre es obligatorio y admite hasta 80 caracteres.';
}
if (mb_strlen($descripcion) > 255) {
    $errores[] = 'La descripción admite hasta 255 caracteres.';
}

$volver = 'form.php' . ($id > 0 ? '?id=' . $id : '');

if ($errores) {
    flash('danger', implode(' ', $errores));
    $_SESSION['old'] = ['nombre' => $nombre, 'descripcion' => $descripcion];
    header('Location: ' . $volver);
    exit;
}

try {
    if ($id > 0) {
        $st = $pdo->prepare('UPDATE categoria SET nombre = ?, descripcion = ? WHERE id_categoria = ?');
        $st->execute([$nombre, $descripcion ?: null, $id]);
        flash('success', 'Categoría actualizada.');
    } else {
        $st = $pdo->prepare('INSERT INTO categoria (nombre, descripcion) VALUES (?, ?)');
        $st->execute([$nombre, $descripcion ?: null]);
        flash('success', 'Categoría creada.');
    }
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    // 1062 = valor duplicado (UNIQUE)
    if (($e->errorInfo[1] ?? 0) == 1062) {
        flash('danger', 'Ya existe una categoría con ese nombre.');
    } else {
        flash('danger', 'No se pudo guardar la categoría.');
    }
    $_SESSION['old'] = ['nombre' => $nombre, 'descripcion' => $descripcion];
    header('Location: ' . $volver);
    exit;
}
