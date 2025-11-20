<?php
require '../../cn.php'; // Conexión a la BD

if (!isset($_GET['id_trabajo'])) {
    die("ID no válido.");
}

$id_trabajo = (int) $_GET['id_trabajo'];

// 1. Obtener datos del trabajo
$stmt = $pdo->prepare("
    SELECT id_trabajador, id_area, id_elemento,tipo
    FROM trabajos
    WHERE id = :id
");
$stmt->execute([':id' => $id_trabajo]);
$trabajo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trabajo) {
    die("Trabajo no encontrado.");
}

// --------------------------------------------------------------
// 1️Obtener datos del trabajador
// --------------------------------------------------------------

$stmt = $pdo->prepare("SELECT nombre, usuario, id_area FROM trabajadores WHERE usuario = :usuario LIMIT 1");
$stmt->bindParam(':usuario', $_SESSION['usuario']);
$stmt->execute();
$trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Obtener id_pedido desde pedido_elementos
$stmt = $pdo->prepare("
    SELECT id_pedido,tipo 
    FROM pedido_elementos
    WHERE id = :id_elemento
");
$stmt->execute([':id_elemento' => $trabajo['id_elemento']]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("No se encontró el pedido asociado al elemento.");
}

// 3. Actualizar el trabajo a estado 'terminado'
$stmt = $pdo->prepare("
    UPDATE trabajos 
    SET estado = 'terminado'
    WHERE id = :id
");
$stmt->execute([':id' => $id_trabajo]);

// 4. Insertar en detalle_pedido
$stmt = $pdo->prepare("
    INSERT INTO detalle_pedido 
    (id_pedido, id_trabajador, id_area, fecha, accion, observacion, created_at, updated_at)
    VALUES
    (:id_pedido, :id_trabajador, :id_area, CURDATE(), :accion, :observacion, NOW(), NOW())
");

$stmt->execute([
    ':id_pedido'    => $pedido['id_pedido'],
    ':id_trabajador'=> $trabajo['id_trabajador'],
    ':id_area'      => $trabajo['id_area'],
    ':accion'       => 'Trabajo terminado',
    ':observacion'  => "El trabajador {$trabajador['nombre']} terminó el trabajo $id_trabajo del elemento {$pedido['tipo']}-{$trabajo['id_elemento']} del pedido $id_pedido"
]);

// 5. Redirigir
header("Location: ../../views/workers/Worker_Dashboard.php");
exit;

?>
