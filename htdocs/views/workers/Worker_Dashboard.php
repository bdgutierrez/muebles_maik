<?php
require_once '../../cn.php';
session_start();

// --------------------------------------------------------------
// 🔒 Verificar sesión
// --------------------------------------------------------------
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_cargo'])) {
    header("Location: ../../inicio.php");
    exit();
}

$id_trabajador = $_SESSION['id_trabajador'];
$id_cargo = $_SESSION['id_cargo'];

// --------------------------------------------------------------
// 🧩 Consultar datos del trabajador
// --------------------------------------------------------------
$stmt = $pdo->prepare("SELECT nombre, usuario, id_area FROM trabajadores WHERE usuario = :usuario LIMIT 1");
$stmt->bindParam(':usuario', $_SESSION['usuario']);
$stmt->execute();
$trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trabajador) {
    echo "<p class='text-center text-red-600 mt-10 font-semibold'>⚠️ Error: Trabajador no encontrado.</p>";
    exit();
}

// --------------------------------------------------------------
// 🏷️ Obtener nombre del área
// --------------------------------------------------------------
$stmt = $pdo->prepare("SELECT nombre FROM areas WHERE id_area = :area LIMIT 1");
$stmt->bindParam(':area', $trabajador['id_area']);
$stmt->execute();
$nombreArea = $stmt->fetchColumn();

// --------------------------------------------------------------
// ⚙️ Definir estado a mostrar según área
// --------------------------------------------------------------
if ($trabajador['id_area'] == 3) {
    $estado = 2;
} else {
    $estado = 4;
}
$stmt = $pdo->prepare("SELECT nombre FROM estados WHERE id = :estado LIMIT 1");
$stmt->bindParam(':estado', $estado);
$stmt->execute();
$nombreEstado = $stmt->fetchColumn();



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos - <?= htmlspecialchars($nombreArea) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-[Poppins]">

    <!-- Header -->
    <?php include('../layouts/Worker_menu.php'); ?>

    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto mt-10 bg-white shadow-md rounded-xl p-6">

        <!-- 📦 Tabla 1: Pedidos disponibles -->
        <h1 class="text-2xl font-bold text-gray-700 mb-6">
            📋 Trabajos disponibles - Área <?= htmlspecialchars($nombreArea) ?> (<?= htmlspecialchars($nombreEstado) ?>)
        </h1>
<?php include('../layouts/tabla_pedidos.php') ?>
  

        <!-- 🧰 Tabla 2: Pedidos ya iniciados -->
        <h2 class="text-xl font-bold text-gray-700 mb-4">🛠️ Tus trabajos en proceso</h2>

        <?php include('../layouts/tabla_pedidos_empezados.php') ?>
    </div>

</body>
</html>
