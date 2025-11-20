<?php
session_start();
include('../../cn.php');

//verificacion de asistencia--------------------------
// Verificar sesión y rol
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../inicio.php");
    exit();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['id_rol'])) {
    header("Location: ../../inicio.php");
    exit();
}

// Consultar el nombre del rol desde la base de datos
$stmt = $pdo->prepare("SELECT nombre_rol FROM roles WHERE id_rol = :id_rol LIMIT 1");
$stmt->bindParam(':id_rol', $_SESSION['id_rol']);
$stmt->execute();
$nombre_rol = $stmt->fetchColumn();

// Si el rol no es Administrador, redirigir fuera
if (!$nombre_rol || strcasecmp($nombre_rol, 'Administrador') !== 0) {
    header("Location: ../../inicio.php");
    exit();
}
//Fin de verificacion-----------------------------------------
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- SIDEBAR + MAIN -->
    <div class="flex flex-1">
        <!-- SIDEBAR -->
      <?php include('../layouts/Admin_menu.php');?>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-6 mt-20">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Gestión del Sistema</h2>

            <!-- GRID DE BOTONES -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                <a href="areas.php" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    🏢 <span class="mt-2">Áreas</span>
                </a>
                <a href="trabajadores.php" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    👷‍♂️ <span class="mt-2">Trabajadores</span>
                </a>
                <a href="ver_asistencias.php" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    👥 <span class="mt-2">Asistencias</span>
                </a>
                <a href="bodegas.php" class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    🏭 <span class="mt-2">Bodegas</span>
                </a>
                <a href="contabilidad.php" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    💰 <span class="mt-2">Contabilidad</span>
                </a>
                <a href="pedidos.php" class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    📦 <span class="mt-2">Pedidos</span>
                </a>
                <a href="productos.php" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    🛒 <span class="mt-2">Productos</span>
                </a>
                <a href="roles.php" class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    🧩 <span class="mt-2">Roles</span>
                </a>
                <a href="sillas.php" class="bg-pink-500 hover:bg-pink-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    💺 <span class="mt-2">Sillas</span>
                </a>
                 <a href="bases.php" class="bg-pink-500 hover:bg-pink-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    🕋 <span class="mt-2">bases</span>
                </a>
                <a href="telas.php" class="bg-rose-500 hover:bg-rose-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    🧵 <span class="mt-2">Telas</span>
                </a>
                <a href="tiposProducto.php" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    🏷️ <span class="mt-2">Tipos de Producto</span>
                </a>
                <a href="trabajos.php" class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    🧰 <span class="mt-2">Trabajos</span>
                </a>
                <a href="ver_asistencias.php" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                    ⏱️ <span class="mt-2">Asistencias</span>
                </a>
                <a href="cargos.php" class="bg-red-400 hover:bg-red-700 text-white font-semibold py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition">
                📋 <span class="mt-2">Cargos</span>
                </a>
            </div>
        </main>
    </div>

</body>
</html>
