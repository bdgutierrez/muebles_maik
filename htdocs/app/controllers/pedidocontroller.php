<?php
include('../../cn.php');
session_start();

class PedidoController {

    // ✅ Crear pedido
    public function crearPedido($nombre_cliente, $id_tipo_producto, $color, $id_tela, $id_silla, $id_base,$cantidad,$puestos,$observaciones) {
        global $pdo;

        $sql = "INSERT INTO pedidos (
                    nombre_cliente, id_tipo_producto, color, id_tela, id_silla, id_base,
                    fecha_pedido, id_estado, created_at, updated_at,cantidad,puestos,observaciones
                ) VALUES (
                    :nombre_cliente, :id_tipo_producto, :color, :id_tela, :id_silla, :id_base,
                    NOW(), 2, NOW(), NOW(),:cantidad,:puestos,:observaciones
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre_cliente', $nombre_cliente);
        $stmt->bindParam(':id_tipo_producto', $id_tipo_producto);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':id_tela', $id_tela);
        $stmt->bindParam(':id_silla', $id_silla);
        $stmt->bindParam(':id_base', $id_base);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':puestos', $puestos);
        $stmt->bindParam(':observaciones', $observaciones);

        if ($stmt->execute()) {
            header("Location: ../../views/admins/pedidos.php?success=1");
            exit();
        } else {
            header("Location: ../../views/admins/pedidos.php?error=1");
            exit();
        }
    }

    // ✅ Actualizar pedido
    public function actualizarPedido($id_pedido, $nombre_cliente, $color, $id_tela, $id_silla, $id_base, $fecha_pedido, $fecha_entrega, $estado) {
        global $pdo;

        $sql = "UPDATE pedidos 
                SET nombre_cliente = :nombre_cliente,
                    color = :color,
                    id_tela = :id_tela,
                    id_silla = :id_silla,
                    id_base = :id_base,
                    fecha_pedido = :fecha_pedido,
                    fecha_entrega = :fecha_entrega,
                    id_estado = :estado,
                    updated_at = NOW()
                WHERE id_pedido = :id_pedido";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_pedido', $id_pedido);
        $stmt->bindParam(':nombre_cliente', $nombre_cliente);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':id_tela', $id_tela);
        $stmt->bindParam(':id_silla', $id_silla);
        $stmt->bindParam(':id_base', $id_base);
        $stmt->bindParam(':fecha_pedido', $fecha_pedido);
        $stmt->bindParam(':fecha_entrega', $fecha_entrega);
        $stmt->bindParam(':estado', $estado);

        if ($stmt->execute()) {
            header("Location: ../../views/admins/pedidos.php?updated=1");
            exit();
        } else {
            header("Location: ../../views/admins/pedidos.php?error=1");
            exit();
        }
    }

    // ✅ Eliminar pedido
    public function eliminarPedido($id_pedido) {
        global $pdo;

        $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id_pedido = :id_pedido");
        $stmt->bindParam(':id_pedido', $id_pedido);

        if ($stmt->execute()) {
            header("Location: ../../views/admins/pedidos.php?deleted=1");
            exit();
        } else {
            header("Location: ../../views/admins/pedidos.php?error=1");
            exit();
        }
    }
}

// ----------------------------------------------------------
// ✅ Manejo de acciones según parámetro GET (?action=add|update|delete)
// ----------------------------------------------------------
$controller = new PedidoController();

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add':
            $nombre_cliente = $_POST['nombre_cliente'];
            $id_tipo_producto = $_POST['id_tipo_producto'];
            $color = $_POST['color'];
            $id_tela = !empty($_POST['id_tela']) ? $_POST['id_tela'] : null;
            $id_silla = !empty($_POST['id_silla']) ? $_POST['id_silla'] : null;
            $id_base = !empty($_POST['id_base']) ? $_POST['id_base'] : null;
            $cantidad = !empty($_POST['cantidad']) ? $_POST['cantidad'] : 0;
            $puestos = !empty($_POST['puestos']) ? $_POST['puestos'] : 0;
            $observaciones = !empty($_POST['observaciones']) ? $_POST['observaciones'] : null;

            $controller->crearPedido($nombre_cliente, $id_tipo_producto, $color, $id_tela, $id_silla, $id_base,$cantidad,$puestos,$observaciones);
            break;

        case 'update':
            $id_pedido = $_POST['id_pedido'];
            $nombre_cliente = $_POST['nombre_cliente'];
            $color = $_POST['color'];
            $id_tela = !empty($_POST['id_tela']) ? $_POST['id_tela'] : null;
            $id_silla = !empty($_POST['id_silla']) ? $_POST['id_silla'] : null;
            $id_base = !empty($_POST['id_base']) ? $_POST['id_base'] : null;
            $fecha_pedido = $_POST['fecha_pedido'];
            $fecha_entrega = $_POST['fecha_entrega'];
            $estado = $_POST['estado'];

            $controller->actualizarPedido($id_pedido, $nombre_cliente, $color, $id_tela, $id_silla, $id_base, $fecha_pedido, $fecha_entrega, $estado);
            break;

        case 'delete':
            $id_pedido = $_GET['id'];
            $controller->eliminarPedido($id_pedido);
            break;
    }
}
?>
