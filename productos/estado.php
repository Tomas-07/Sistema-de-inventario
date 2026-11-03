<?php
// productos/estado.php  (activar / desactivar; nunca se elimina)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id_producto'] ?? 0);

$st = $pdo->prepare('SELECT estado, id_categoria FROM producto WHERE id_producto = ?');
$st->execute([$id]);
$prod = $st->fetch();

if (!$prod) {
    flash('danger', 'El producto no existe.');
    header('Location: index.php');
    exit;
}

// Para reactivar un producto, su categoría debe estar activa
if ($prod['estado'] == 0) {
    $st = $pdo->prepare('SELECT estado FROM categoria WHERE id_categoria = ?');
    $st->execute([$prod['id_categoria']]);
    if ($st->fetchColumn() != 1) {
        flash('warning', 'No se puede activar: su categoría está inactiva.');
        header('Location: index.php');
        exit;
    }
}

$st = $pdo->prepare('UPDATE producto SET estado = 1 - estado WHERE id_producto = ?');
$st->execute([$id]);

flash('success', 'Estado del producto actualizado.');
header('Location: index.php');
exit;
