<?php
require_once '../../cn.php';
session_start();

// --------------------------------------------------------------
// 🔒 Validar sesión
// --------------------------------------------------------------
if (
    empty($_SESSION['usuario']) ||
    empty($_SESSION['id_trabajador']) ||
    empty($_SESSION['id_area'])
) {
    echo "<script>
        alert('Sesión no válida. Por favor, inicia sesión nuevamente.');
        window.location='../../inicio.php';
    </script>";
    exit();
}

// --------------------------------------------------------------
// 📦 Validar parámetro id_pedido
// --------------------------------------------------------------
if (empty($_GET['id'])) {
    echo "<script>
        alert('No se recibió el pedido correctamente.');
        window.location='../../views/workers/pedidos.php';
    </script>";
    exit();
}

$id_pedido = (int) $_GET['id'];
$id_trabajador = (int) $_SESSION['id_trabajador'];
$id_area = (int) $_SESSION['id_area'];

// --------------------------------------------------------------
// 🕒 Fecha y hora actual
// --------------------------------------------------------------
date_default_timezone_set('America/Bogota');
$fecha = date('Y-m-d H:i:s');

try {
    // 🟡 Obtener estado actual del pedido
    $stmt = $pdo->prepare("
        SELECT e.id AS id_estado, e.nombre AS nombre_estado
        FROM pedidos p
        JOIN estados e ON p.id_estado = e.id
        WHERE p.id_pedido = :id_pedido
    ");
    $stmt->execute([':id_pedido' => $id_pedido]);
    $estado_actual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estado_actual) {
        throw new Exception("Pedido no encontrado o sin estado asignado.");
    }

    $id_estado_actual = (int) $estado_actual['id_estado'];
    $nombre_estado_actual = $estado_actual['nombre_estado'];

    // 🔍 Buscar el siguiente estado (por ID consecutivo)
    $stmt = $pdo->prepare("
        SELECT id, nombre 
        FROM estados 
        WHERE id > :id_actual 
        ORDER BY id ASC 
        LIMIT 1
    ");
    $stmt->execute([':id_actual' => $id_estado_actual]);
    $siguiente_estado = $stmt->fetch(PDO::FETCH_ASSOC);

    // 🧩 Si no hay siguiente estado, se marca como terminado
    if (!$siguiente_estado) {
        $siguiente_estado = ['id' => 5, 'nombre' => 'Terminado'];
    }

    $id_nuevo_estado = (int) $siguiente_estado['id'];
    $nombre_nuevo_estado = $siguiente_estado['nombre'];

    // 🧾 Crear registro en detalle_pedido
    $accion = "Avance de fase";
    $observacion = "El pedido avanzó de '$nombre_estado_actual' a '$nombre_nuevo_estado'.";

    $sql = "INSERT INTO detalle_pedido (id_pedido, id_trabajador, id_area, accion, observacion, fecha)
            VALUES (:id_pedido, :id_trabajador, :id_area, :accion, :observacion, :fecha)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_pedido' => $id_pedido,
        ':id_trabajador' => $id_trabajador,
        ':id_area' => $id_area,
        ':accion' => $accion,
        ':observacion' => $observacion,
        ':fecha' => $fecha
    ]);

    // 🔁 Actualizar estado del pedido
    $update = $pdo->prepare("
        UPDATE pedidos 
        SET id_estado = :nuevo_estado 
        WHERE id_pedido = :id_pedido
    ");
    $update->execute([
        ':nuevo_estado' => $id_nuevo_estado,
        ':id_pedido' => $id_pedido
    ]);

    // ✅ Éxito
    echo "<script>
        alert('✅ El pedido avanzó correctamente a la fase: $nombre_nuevo_estado');
        window.location='../../views/workers/Worker_Dashboard.php';
    </script>";

} catch (Exception $e) {
    echo "<script>
        alert('⚠️ Error: " . addslashes($e->getMessage()) . "');
        window.location='../../views/workers/Worker_Dashboard.php';
    </script>";
}
?>
