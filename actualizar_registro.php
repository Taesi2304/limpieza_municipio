<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    die('No autenticado');
}

require_once 'includes/db.php';
$pdo = getConnection();

try {
    $pdo->beginTransaction();

    $folio         = intval($_POST['folio']);
    $km_salida     = floatval($_POST['km_salida']     ?? 0);
    $km_entrada    = floatval($_POST['km_entrada']    ?? 0);
    $diesel_inicio = floatval($_POST['diesel_inicio'] ?? 0);
    $diesel_final  = floatval($_POST['diesel_final']  ?? 0);
    $diesel_cargado= floatval($_POST['diesel_cargado']?? 0);
    $total_km      = max(0, $km_entrada - $km_salida);
    $total_diesel  = max(0, ($diesel_inicio - $diesel_final) + $diesel_cargado);
    
    // ACTUALIZAR REGISTRO PRINCIPAL
    $stmt = $pdo->prepare("
        UPDATE registro_diario SET
            fecha_orden = ?,
            turno = ?,
            id_ruta = ?,
            id_despachador = ?,
            id_chofer = ?,
            id_tipo_unidad = ?,
            id_unidad = ?,
            cantidad_kg = ?,
            cantidad_puches = ?,
            km_salida = ?,
            km_entrada = ?,
            total_km = ?,
            diesel_inicio = ?,
            diesel_final = ?,
            diesel_cargado = ?,
            total_diesel = ?
        WHERE folio = ?
    ");
    
    $stmt->execute([
        $_POST['fecha_orden'],
        intval($_POST['turno']),
        intval($_POST['id_ruta']),
        intval($_POST['id_despachador']),
        intval($_POST['id_chofer']),
        intval($_POST['id_tipo_unidad']),
        intval($_POST['id_unidad']),
        floatval($_POST['cantidad_kg']),
        intval($_POST['cantidad_puches']),
        $km_salida,
        $km_entrada,
        $total_km,
        $diesel_inicio,
        $diesel_final,
        $diesel_cargado,
        $total_diesel,
        $folio
    ]);

    // ACTUALIZAR COLONIAS: Borrar anteriores y reinsertar
    $stmtDel = $pdo->prepare("DELETE FROM registro_detalle_colonias WHERE folio = ?");
    $stmtDel->execute([$folio]);
    
    if (isset($_POST['pct_colonia']) && is_array($_POST['pct_colonia'])) {
        $stmtCol = $pdo->prepare("INSERT INTO registro_detalle_colonias (folio, id_colonia, porcentaje_recolectado) VALUES (?, ?, ?)");
        foreach ($_POST['pct_colonia'] as $idCol => $pct) {
            $pct = floatval($pct);
            if ($pct > 0) {
                $stmtCol->execute([$_POST['folio'], $idCol, $pct]);
            }
        }
    }
    
    $pdo->commit();
    
    echo "<script>
        alert('Orden Actualizada \\nFolio: {$folio}');
        window.location='editar.php?folio={$folio}';
    </script>";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<script>
        alert('Error: {$e->getMessage()}');
        window.history.back();
    </script>";
}
?>
</body>
</html>
