<?php
// Configuración de la base de datos
$host = 'sql100.infinityfree.com'; // O tu servidor de base de datos
$dbname = 'if0_40357611_muebles_maik';
$username = 'if0_40357611';
$password = '29849071brianD';

// Establecer la conexión a la base de datos usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configurar PDO para que lance excepciones en caso de errores
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Mostrar el error si la conexión falla
    die("Conexión fallida: " . $e->getMessage());
}
?>