<?php
// config/conexion.php
// Conexión a MySQL con PDO (consultas preparadas, errores como excepciones).

// Carpeta del proyecto dentro de htdocs (cámbiala si usas otro nombre)
const BASE_URL = '/tecnostock';

$host = 'localhost';
$db   = 'tecnostock';
$user = 'root';   // usuario por defecto de XAMPP
$pass = '';       // XAMPP no trae contraseña por defecto

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // consultas preparadas reales
        ]
    );
} catch (PDOException $e) {
    die('No se pudo conectar con la base de datos. Revisa que MySQL esté encendido en XAMPP.');
}
