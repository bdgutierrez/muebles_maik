<?php 
//--------------------------------------------------------------
// ✅ Consultar trabajos disponibles
//--------------------------------------------------------------
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
    left join pedidos p on p.id_pedido=pe.id_pedido
    LEFT JOIN materiales m ON m.id = p.id_material
    LEFT JOIN sillas s ON pe.id_silla = s.id_silla
    LEFT JOIN bases b ON pe.id_base = b.id_base
    WHERE t.estado = 'espera' and t.id_trabajador= :trabajador
    ORDER BY t.fecha_creacion DESC
");

$stmt->bindParam(':trabajador', $id_trabajador);
$stmt->execute();
$trabajosDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

//--------------------------------------------------------------
?>

<?php if (count($trabajosDisponibles) > 0): ?>
    <div class="overflow-x-auto mb-10">
        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
            <thead class="bg-gray-200 text-gray-700 uppercase">
                <tr>
                    <th class="py-3 px-4 text-left">Pedido</th>
                    <th class="py-3 px-4 text-left">Código Elemento</th>
                    <th class="py-3 px-4 text-left">Trabajo</th>
                    <th class="py-3 px-4 text-left">Elemento</th>
                    <th class="py-3 px-4 text-left">Tipo</th>
                    <th class="py-3 px-4 text-left">Material</th>
                    <th class="py-3 px-4 text-left">Precio</th>
                    <th class="py-3 px-4 text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="text-gray-600">

                <?php foreach ($trabajosDisponibles as $t): ?>
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="py-2 px-4"><?= $t['id_pedido'] ?></td>
                        <td class="py-2 px-4"><?= $t['id_elemento'] ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($t['area_nombre']) ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($t['tipo_elemento']) ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($t['nombre_elemento']) ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($t['material_nombre'] ?? '-') ?></td>
                        <td class="py-2 px-4">$<?= number_format($t['costo'], 2) ?></td>

                        <td class="py-2 px-4 text-center">
                            <div class="flex space-x-3">
               <a href="../../app/controllers/aceptar_trabajo.php?id=<?= $t['id_trabajo'] ?>"
                  class="bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded-full">
                              Aceptar
                                </a>

                         <a href="../../app/controllers/rechazar_trabajo.php?id=<?= $t['id_trabajo'] ?>"
                          class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-full">
                               Rechazar
                                   </a>
                                   </div>


                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-gray-500 text-center py-6">No hay trabajos disponibles actualmente.</p>
<?php endif; ?>
