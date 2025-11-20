<?php
require_once '../../cn.php';
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../inicio.php");
    exit();
}

// Acción solicitada
$action = $_GET['action'] ?? '';

switch ($action) {

    // --------------------------------------------------------------
    // AGREGAR BASE
    // --------------------------------------------------------------
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre = $_POST['nombre'];
            $material = $_POST['material'];
            $descripcion = $_POST['descripcion'] ?? '';

            $stmt = $pdo->prepare("INSERT INTO bases (nombre, material, descripcion, created_at) VALUES (:nombre, :material, :descripcion, NOW())");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':material', $material);
            $stmt->bindParam(':descripcion', $descripcion);

            if ($stmt->execute()) {
                header("Location: ../../views/admins/bases.php?success=1");
            } else {
                header("Location: ../../views/admins/bases.php?error=1");
            }
        }
        break;

    // --------------------------------------------------------------
    // ACTUALIZAR BASE
    // --------------------------------------------------------------
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id_base = $_POST['id_base'];
            $nombre = $_POST['nombre'];
            $material = $_POST['material'];
            $descripcion = $_POST['descripcion'] ?? '';

            $stmt = $pdo->prepare("UPDATE bases SET nombre = :nombre, material = :material, descripcion = :descripcion, updated_at = NOW() WHERE id_base = :id_base");
            $stmt->bindParam(':id_base', $id_base);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':material', $material);
            $stmt->bindParam(':descripcion', $descripcion);

            if ($stmt->execute()) {
                header("Location: ../../views/admins/bases.php?updated=1");
            } else {
                header("Location: ../../views/admins/bases.php?error=1");
            }
        }
        break;

    // --------------------------------------------------------------
    // ELIMINAR BASE
    // --------------------------------------------------------------
    case 'delete':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            $stmt = $pdo->prepare("DELETE FROM bases WHERE id_base = :id");
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: ../../views/admins/bases.php?deleted=1");
            } else {
                header("Location: ../../views/admins/bases.php?error=1");
            }
        }
        break;

    // --------------------------------------------------------------
    default:
        header("Location: ../../app/views/admin/bases.php");
        break;
}
