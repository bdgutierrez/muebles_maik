<?php
require_once '../../cn.php';
session_start();

// --------------------------------------------------------------
// Verificación de sesión y rol
// --------------------------------------------------------------
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_rol'])) {
    header("Location: ../../inicio.php");
    exit();
}

$stmt = $pdo->prepare("SELECT nombre_rol FROM roles WHERE id_rol = :id_rol LIMIT 1");
$stmt->bindParam(':id_rol', $_SESSION['id_rol']);
$stmt->execute();
$nombre_rol = $stmt->fetchColumn();

if (!$nombre_rol || strcasecmp($nombre_rol, 'Administrador') !== 0) {
    header("Location: ../../inicio.php");
    exit();
}

// --------------------------------------------------------------
// Consultar pedidos con relaciones
// --------------------------------------------------------------
$stmt = $pdo->query("
    SELECT p.*, e.nombre as estado,
           tp.nombre AS tipo_producto, 
           t.nombre AS tela, 
           s.nombre AS silla, 
           b.nombre AS base
    FROM pedidos p
    LEFT JOIN estados e on p.id_estado = e.id
    LEFT JOIN tipos_producto tp ON p.id_tipo_producto = tp.id_tipo_producto
    LEFT JOIN telas t ON p.id_tela = t.id_tela
    LEFT JOIN sillas s ON p.id_silla = s.id_silla
    LEFT JOIN bases b ON p.id_base = b.id_base
    ORDER BY p.id_pedido DESC
");
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tProductos = $pdo->query("SELECT * FROM tipos_producto")->fetchAll(PDO::FETCH_ASSOC);
$telas = $pdo->query("SELECT * FROM telas")->fetchAll(PDO::FETCH_ASSOC);
$sillas = $pdo->query("SELECT * FROM sillas")->fetchAll(PDO::FETCH_ASSOC);
$estados = $pdo->query("SELECT * FROM estados")->FetchAll(PDO::FETCH_ASSOC);
$bases = $pdo->query("SELECT * FROM bases")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Pedidos</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-[Poppins]">

<?php include('../layouts/Admin_menu.php'); ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Pedidos</h1>
        <button onclick="openModal('addModal')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">➕ Nuevo Pedido</button>
    </div>

    <!-- Mensajes -->
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">Pedido agregado con éxito ✅</div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">Pedido eliminado 🗑️</div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded mb-4">Pedido actualizado 💾</div>
    <?php endif; ?>

    <!-- Tabla -->
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Cliente</th>
                    <th class="p-2">Cantidad</th>
                    <th class="p-2">puestos</th>
                    <th class="p-2">Tipo</th>
                    <th class="p-2">Color</th>
                    <th class="p-2">Tela</th>
                    <th class="p-2">Silla</th>
                    <th class="p-2">Base</th>
                    <th class="p-2">Pedido</th>
                    <th class="p-2">Observacion</th>
                    <th class="p-2">Entrega</th>
                    <th class="p-2">Estado</th>
                    <th class="p-2 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $p): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-2 text-center"><?= $p['id_pedido'] ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['nombre_cliente']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['cantidad']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['puestos']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['tipo_producto']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['color']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['tela']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['silla']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['base']) ?></td>
                    <td class="p-2"><?= date('d/m/Y', strtotime($p['fecha_pedido'])) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['observaciones']) ?></td>
                    <td class="p-2"><?= $p['fecha_entrega'] ? date('d/m/Y', strtotime($p['fecha_entrega'])) : '-' ?></td>
                    <td class="p-2 text-center">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                            <?= htmlspecialchars($p['estado']) ?>
                        </span>
                    </td>
                    <td class="p-2 text-center flex justify-center gap-2">
                        <button onclick="openEditModal(<?= htmlspecialchars(json_encode($p)) ?>)" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">✏️</button>
                        <a href="detallepedido.php?id=<?=$p['id_pedido'] ?>" class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600">👁️</a>
                        <a href="../../app/controllers/pedidocontroller.php?action=delete&id=<?= $p['id_pedido'] ?>" onclick="return confirm('¿Eliminar este pedido?')" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (count($pedidos) == 0): ?>
                <tr><td colspan="11" class="p-4 text-center text-gray-500">No hay pedidos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 🔵 MODAL AGREGAR -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl shadow-lg transform transition-all scale-95 overflow-hidden">
        <div class="p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4 text-center">Agregar Pedido</h2>

            <form action="../../app/controllers/pedidocontroller.php?action=add" method="POST" class="grid gap-4 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Cliente</label>
                    <input type="text" name="nombre_cliente" required placeholder="Ingrese el nombre del cliente" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                    <input type="number" name="cantidad" required placeholder="Ingrese la cantidad" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Producto</label>
                    <select name="id_tipo_producto" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                        <option value="">Seleccione...</option>
                        <?php foreach ($tProductos as $tp): ?>
                            <option value="<?= $tp['id_tipo_producto'] ?>"><?= htmlspecialchars($tp['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Puestos</label>
                    <input type="number" name="puestos" value="0" min="0" placeholder="Ej: 0, 2, 4" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Silla o butaco</label>
                    <select name="id_silla" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                        <option value="">Seleccione...</option>
                        <?php foreach ($sillas as $s): ?>
                            <option value="<?= $s['id_silla'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Base</label>
                    <select name="id_base" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                        <option value="">Seleccione...</option>
                        <?php foreach ($bases as $b): ?>
                            <option value="<?= $b['id_base'] ?>"><?= htmlspecialchars($b['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="text" name="color" placeholder="Ingrese el color" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tela</label>
                    <select name="id_tela" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                        <option value="">Seleccione...</option>
                        <?php foreach ($telas as $t): ?>
                            <option value="<?= $t['id_tela'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" placeholder="Notas u observaciones del pedido" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Entrega(opcional)</label>
                    <input type="date" name="fecha_entrega" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div class="sm:col-span-2 flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeModal('addModal')" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Cancelar</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Guardar</button>
                </div>

            </form>
        </div>
    </div>
</div>


<!-- 🟡 MODAL EDITAR -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl shadow-lg transform transition-all scale-95 overflow-hidden">
        <div class="p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4 text-center">Editar Pedido</h2>

            <form id="editForm" action="../../app/controllers/pedidocontroller.php?action=update" method="POST" class="grid gap-4 sm:grid-cols-2">
                <input type="hidden" name="id_pedido" id="edit_id_pedido">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Cliente</label>
                    <input type="text" name="nombre_cliente" id="edit_nombre_cliente" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                    <input type="number" name="cantidad" id="edit_cantidad" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Puestos</label>
                    <input type="number" name="puestos" id="edit_puestos" min="0" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="text" name="color" id="edit_color" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Silla</label>
                    <select name="id_silla" id="edit_id_silla" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                        <option value="">Seleccione...</option>
                        <?php foreach ($sillas as $s): ?>
                            <option value="<?= $s['id_silla'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Base</label>
                    <select name="id_base" id="edit_id_base" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                        <option value="">Seleccione...</option>
                        <?php foreach ($bases as $b): ?>
                            <option value="<?= $b['id_base'] ?>"><?= htmlspecialchars($b['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Entrega</label>
                    <input type="date" name="fecha_entrega" id="edit_fecha_entrega" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="edit_estado" name="estado" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none">
                        <?php foreach ($estados as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" id="edit_observaciones" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 outline-none"></textarea>
                </div>

                <div class="sm:col-span-2 flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeModal('editModal')" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Cancelar</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Actualizar</button>
                </div>

            </form>
        </div>
    </div>
</div>


<script>
function openModal(id) { 
    document.getElementById(id).classList.remove('hidden'); 
}

function closeModal(id) { 
    document.getElementById(id).classList.add('hidden'); 
}

function openEditModal(data) {
    document.getElementById('edit_id_pedido').value = data.id_pedido;
    document.getElementById('edit_nombre_cliente').value = data.nombre_cliente;
    document.getElementById('edit_color').value = data.color;
    document.getElementById('edit_fecha_entrega').value = data.fecha_entrega;
    document.getElementById('edit_cantidad').value = data.cantidad;
    document.getElementById('edit_puestos').value = data.puestos;
    document.getElementById('edit_id_silla').value = data.id_silla ?? "";
    document.getElementById('edit_id_base').value = data.id_base ?? "";
    document.getElementById('edit_observaciones').value = data.observaciones ?? "";

    const selectEstado = document.getElementById('edit_estado');
    selectEstado.value = data.id_estado;
    if (!selectEstado.value) {
        selectEstado.selectedIndex = 0;
    }

    openModal('editModal');
}

// Cerrar modal al hacer clic fuera del contenido
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('fixed')) {
        e.target.classList.add('hidden');
    }
});
</script>
</body>
</html>
