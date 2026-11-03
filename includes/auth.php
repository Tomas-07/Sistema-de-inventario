<?php
// includes/auth.php
// Inicia la sesión y define funciones de apoyo. Se incluye al inicio de cada página.

require_once __DIR__ . '/../config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escapa texto antes de mostrarlo en HTML (evita XSS)
function e($texto): string
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// Si no hay sesión iniciada, manda al login
function requiere_login(): void
{
    if (empty($_SESSION['id_usuario'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

// Mensaje que se muestra una sola vez en la siguiente página
// $tipo: success | danger | warning
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $mensaje];
}
