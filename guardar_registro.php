<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    die('No autenticado');
}

require_once 'includes/db.php';
$pdo = getConnection();

try {
    // INICIAR TRANSACCIÓN
    $pdo->beginTransaction();

    // RESERVAR FOLIO (SOLO AQUÍ SE CONSUME)
    $pdo->exec("UPDATE control_folios SET ultimo_folio = ultimo_folio + 1");
    $folio = $pdo->query("SELECT ultimo_folio FROM control_folios LIMIT 1")->fetch()['ultimo_folio'];

    // Calcular totales
    $total_km = $_POST['km_entrada'] - $_POST['km_salida'];
    $total_diesel = ($_POST['diesel_inicio'] + $_POST['diesel_cargado']) - $_POST['diesel_final'];

    // INSERTAR REGISTRO PRINCIPAL
    $stmt = $pdo->prepare("
        INSERT INTO registro_diario (
            folio, fecha_orden, fecha_captura, turno,
            id_ruta, id_despachador, id_chofer, id_tipo_unidad, id_unidad,
            cantidad_kg, cantidad_puches,
            km_salida, km_entrada, total_km,
            diesel_inicio, diesel_final, diesel_cargado, total_diesel,
            usuario_captura
        ) VALUES (
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?,
            ?, ?, ?,
            ?, ?, ?, ?,
            ?
        )
    ");

    $stmt->execute([
        $folio,
        $_POST['fecha_orden'],
        $_POST['fecha_captura'],
        $_POST['turno'],
        $_POST['id_ruta'],
        $_POST['id_despachador'],
        $_POST['id_chofer'],
        $_POST['id_tipo_unidad'],
        $_POST['id_unidad'],
        $_POST['cantidad_kg'],
        $_POST['cantidad_puches'],
        $_POST['km_salida'],
        $_POST['km_entrada'],
        $total_km,
        $_POST['diesel_inicio'],
        $_POST['diesel_final'],
        $_POST['diesel_cargado'],
        $total_diesel,
        $_SESSION['usuario']
    ]);

    // INSERTAR DETALLE DE COLONIAS
    if (isset($_POST['pct_colonia']) && is_array($_POST['pct_colonia'])) {
        $stmtDetalle = $pdo->prepare("INSERT INTO registro_detalle_colonias (folio, id_colonia, porcentaje_recolectado) VALUES (?, ?, ?)");

        foreach ($_POST['pct_colonia'] as $idColonia => $porcentaje) {
            $pct = floatval($porcentaje);
            if ($pct > 0) {
                $stmtDetalle->execute([$folio, $idColonia, $pct]);
            }
        }
    }

    // CONFIRMAR TRANSACCIÓN
    $pdo->commit();

    echo "<script>
        alert('Orden guardada exitosamente\\n\\nFolio: $folio\\nUsuario: {$_SESSION['usuario']}');
        window.location='captura.php';
    </script>";
} catch (Exception $e) {
    // SI HAY ERROR, EL FOLIO NO SE CONSUME
    $pdo->rollBack();
    echo "<script>
        alert('Error al guardar:\\n{$e->getMessage()}');
        window.history.back();
    </script>";
}
?>

</body>
<footer class="footer">
    <div class="container footer-columns">
        <div class="footer-column">
            <p>PROGRAMACIÓN WEB 2026</p>
        </div>
        <div class="footer-column">
            <p>TRABAJO EN EQUIPO</p>
        </div>
        <div class="footer-column">
            <p>Integrantes: Juan, María, Pedro</p>
        </div>
    </div>
</footer>


</html>