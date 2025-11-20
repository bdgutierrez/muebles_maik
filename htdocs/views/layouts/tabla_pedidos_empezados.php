<?php 
$id_area = (int) $_SESSION['id_area'] ?? 0;
$id_trabajador = (int) $_SESSION['id_trabajador'] ?? 0;

// --------------------------------------------------------------
// ⚙️ Consultar trabajos aceptados por el trabajador
// --------------------------------------------------------------
$stmt = $pdo->prepare("
    SELECT 
        t.id AS id_trabajo,
        t.costo,
        t.id_elemento,
        t.id_area,
        t.estado,
        a.nombre AS area_nombre,
        pe.id_pedido,
        pe.tipo AS tipo_elemento,
        CASE 
            WHEN pe.tipo = 'Silla' THEN s.nombre
            WHEN pe.tipo = 'Base' THEN b.nombre
            ELSE '-'
        END AS nombre_elemento,
        m.nombre AS material_nombre
    FROM trabajos t
    
    LEFT JOIN areas a ON t.id_area = a.id_area
    LEFT JOIN pedido_elementos pe ON pe.id = t.id_elemento
    LEFT JOIN pedidos p ON p.id_pedido = pe.id_pedido
    LEFT JOIN materiales m ON m.id = p.id_material
    LEFT JOIN sillas s ON pe.id_silla = s.id_silla
    LEFT JOIN bases b ON pe.id_base = b.id_base
    
    WHERE t.estado = 'aceptado'
      AND t.id_trabajador = :trabajador
    
    ORDER BY t.fecha_creacion DESC
");

$stmt->bindParam(':trabajador', $id_trabajador, PDO::PARAM_INT);
$stmt->execute();
$trabajosAceptados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (count($trabajosAceptados) > 0): ?>
<div class="overflow-x-auto">
    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
        <thead class="bg-gray-200 text-gray-700 uppercase">
            <tr>
                <th class="py-3 px-4 text-left">Pedido</th>
                <th class="py-3 px-4 text-left">Elemento</th>
                <th class="py-3 px-4 text-left">Tipo</th>
                <th class="py-3 px-4 text-left">Material</th>
                <th class="py-3 px-4 text-left">Área</th>
                <th class="py-3 px-4 text-left">Precio</th>
                <th class="py-3 px-4 text-center">Acción</th>
            </tr>
        </thead>

        <tbody class="text-gray-600">
            <?php foreach ($trabajosAceptados as $t): ?>
                <tr class="border-t hover:bg-gray-50 transition">
                    <td class="py-2 px-4"><?= $t['id_pedido'] ?></td>
                    <td class="py-2 px-4"><?= $t['nombre_elemento'] ?></td>
                    <td class="py-2 px-4"><?= $t['tipo_elemento'] ?></td>
                    <td class="py-2 px-4"><?= $t['material_nombre'] ?></td>
                    <td class="py-2 px-4"><?= $t['area_nombre'] ?></td>
                    <td class="py-2 px-4">$<?= number_format($t['costo'], 2) ?></td>

                    <td class="py-2 px-4 text-center">
                        <a href="detalle_pedido.php?id=<?= $t['id_pedido'] ?>" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-full">
                            Ver
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php else: ?>
<p class="text-gray-500 text-center py-6">No tienes trabajos aceptados.</p>
<?php endif; ?>
