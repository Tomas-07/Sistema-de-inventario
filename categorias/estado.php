<?php
// categorias/estado.php  (activar / desactivar; nunca se elimina)
require_once __DIR__ . '/../includes/auth.php';
requiere_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id_categoria'] ?? 0);

$st = $pdo->prepare('SELECT estado FROM categoria WHERE id_categoria = ?');
$st->execute([$id]);
$cat = $st->fetch();

if (!$cat) {
    flash('danger', 'La categoría no existe.');
    header('Location: index.php');
    exit;
}

// No se puede desactivar una categoría que aún tiene productos activos
if ($cat['estado'] == 1) {
    $st = $pdo->prepare('SELECT COUNT(*) FROM producto WHERE id_categoria = ? AND estado = 1');
    $st->execute([$id]);
    if ($st->fetchColumn() > 0) {
        flash('warning', 'No se puede desactivar: la categoría tiene productos activos.');
        header('Location: index.php');
        exit;
    }
}

$st = $pdo->prepare('UPDATE categoria SET estado = 1 - estado WHERE id_categoria = ?');
$st->execute([$id]);

flash('success', 'Estado de la categoría actualizado.');
header('Location: index.php');
exit;
