<?php
require_once '../../cn.php';
session_start();

if (!isset($_GET['id'])) {
    die("ID de pedido no proporcionado.");
}

$id_pedido = intval($_GET['id']);

// Consulta principal
$stmt = $pdo->prepare("
    SELECT p.*, e.nombre AS estado, s.nombre AS silla, t.nombre AS tela,tp.nombre as tipo_producto,
    b.nombre as base
    FROM pedidos p
    inner join bases b on b.id_base=p.id_base
    INNER JOIN tipos_producto tp ON tp.id_tipo_producto = p.id_tipo_producto
    LEFT JOIN estados e ON p.id_estado = e.id
    LEFT JOIN sillas s ON p.id_silla = s.id_silla
    LEFT JOIN telas t ON p.id_tela = t.id_tela
    WHERE p.id_pedido = :id
");
$stmt->execute(['id' => $id_pedido]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("Pedido no encontrado.");
}

// Traer detalle del pedido si tienes una tabla detalle_pedido
$stmtDetalle = $pdo->prepare("
    SELECT dp.*, t.nombre
    FROM detalle_pedido dp join trabajadores t on dp.id_trabajador = t.id_trabajador
    WHERE id_pedido = :id
");
$stmtDetalle->execute(['id' => $id_pedido]);
$detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido #<?= $id_pedido ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
<?php include('../layouts/Worker_menu.php'); ?>
<div class="max-w-5xl mx-auto mt-10 bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-bold text-blue-700 mb-4">Detalle del Pedido #<?= $id_pedido ?></h1>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div><strong>Cliente:</strong> <?= htmlspecialchars($pedido['nombre_cliente']) ?></div>
        <div><strong>Fecha Pedido:</strong> <?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></div>
        <div><strong>Fecha Entrega:</strong> <?= $pedido['fecha_entrega'] ? date('d/m/Y', strtotime($pedido['fecha_entrega'])) : '-' ?></div>
        <div><strong>Estado:</strong> 
            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs"><?= htmlspecialchars($pedido['estado']) ?></span>
        </div>
        <div><strong>Cantidad:</strong> <?= htmlspecialchars($pedido['cantidad'] ?? '-') ?></div>
        <div><strong>Tipo Producto:</strong> <?= htmlspecialchars($pedido['tipo_producto'] ?? '-') ?></div>
        
        <div><strong>Color:</strong> <?= htmlspecialchars($pedido['color'] ?? '-') ?></div>
        <div><strong>Base:</strong> <?= htmlspecialchars($pedido['base'] ?? '-') ?></div>
        <div><strong>Tela:</strong> <?= htmlspecialchars($pedido['tela'] ?? '-') ?></div>
        <div><strong>Silla:</strong> <?= htmlspecialchars($pedido['silla'] ?? '-') ?></div>
        <div><strong>Puestos:</strong> <?= htmlspecialchars($pedido['puestos'] ?? '-') ?></div>
    </div>

    <!-- Selección de elemento para trabajar -->
<div class="mt-10 border-t pt-6">
    <h2 class="text-xl font-semibold mb-3 text-gray-700">Selecciona el elemento del comedor para trabajar</h2>

    <form action="../../app/controllers/asignar_trabajo.php" method="POST" class="space-y-4">
        <input type="hidden" name="id_pedido" value="<?= $id_pedido ?>">

        <!-- Opción: Base -->
        <div class="flex items-center space-x-3">
            <input type="radio" id="base" name="elemento" value="base" required
                   class="text-blue-600 focus:ring-blue-500">
            <label for="base" class="text-gray-800">
                Base – <?= htmlspecialchars($pedido['base']) ?> (Color: <?= htmlspecialchars($pedido['color']) ?>)
            </label>
        </div>

        <!-- Sillas -->
        <h3 class="text-gray-700 font-medium mt-4">Sillas:</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <?php
            $total_sillas = intval($pedido['cantidad']) * intval($pedido['puestos']);
            for ($i = 1; $i <= $total_sillas; $i++): ?>
                <div class="flex items-center space-x-2">
                    <input type="radio" id="silla<?= $i ?>" name="elemento" 
                           value="Silla <?= $i ?>" class="text-blue-600 focus:ring-blue-500">
                    <label for="silla<?= $i ?>" class="text-gray-700 text-sm">
                        Silla <?= $i ?> – <?= htmlspecialchars($pedido['silla'] ?? 'Tipo no definido') ?> 
                        (Color: <?= htmlspecialchars($pedido['color'] ?? '-') ?>)
                    </label>
                </div>
            <?php endfor; ?>
        </div>

        <!-- Botón de envío -->
        <div class="mt-6">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded-lg">
                🔧 Iniciar trabajo
            </button>
        </div>
    </form>
</div>


    <?php if ($detalles && count($detalles) > 0): ?>
        <h2 class="text-xl font-semibold mb-3">Movimientos del Pedido</h2>
        <div class="border rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="py-2 px-4 text-left">Fecha</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $mov): ?>
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="py-2 px-4"><?= date('d/m/Y', strtotime($mov['fecha'])) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($mov['accion']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($mov['nombre']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-500 italic">No hay movimientos registrados para este pedido.</p>
    <?php endif; ?>

    <div class="mt-6 text-right">
        <div class="mt-6 text-right">
    <a href="../../app/controllers/fase_completada.php?id=<?= $id_pedido ?>"
       class="bg-green-600 hover:bg-green-800 text-white font-semibold py-2 px-4 rounded-lg">
       ✅ Fase Completada
    </a>
    <a href="Worker_Dashboard.php"
       class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded-lg ml-2">
       ← Volver
    </a>
</div>


    </div>
</div>

</body>
</html>


