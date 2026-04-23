<?php
session_start();
if (!isset($_SESSION['usuario']) || empty($_SESSION['id_usuario'])) {
    die('No autenticado');
}

require_once 'includes/db.php';
require_once 'includes/registro_helpers.php';
$pdo = getConnection();

try {
    $pdo->beginTransaction();

    $pdo->exec('INSERT INTO folio () VALUES ()');
    $id_folio = (int) $pdo->lastInsertId();

    $numero_ruta = (int) ($_POST['numero_ruta'] ?? 0);
    $pctPost = isset($_POST['pct_colonia']) && is_array($_POST['pct_colonia']) ? $_POST['pct_colonia'] : [];
    $slots = construir_slots_colonias($pdo, $numero_ruta, $pctPost);

    $km_inicio = (float) ($_POST['km_inicio'] ?? 0);
    $km_final = (float) ($_POST['km_final'] ?? 0);
    if ($km_final < $km_inicio) {
        throw new Exception('Los km al final deben ser mayores o iguales que los km al inicio.');
    }
    $total_km = max(0, $km_final - $km_inicio);

    $fecha_captura = str_replace('T', ' ', $_POST['fecha_captura'] ?? '');
    $comentarios = trim((string) ($_POST['comentarios'] ?? ''));
    $comentarios = $comentarios === '' ? null : $comentarios;

    $params = [
        $id_folio,
        $_POST['fecha_orden'] ?? null,
        $fecha_captura,
        (int) ($_POST['turno'] ?? 0),
        (int) ($_POST['id_tipo_unidad'] ?? 0),
        (int) ($_POST['id_unidad'] ?? 0),
        $numero_ruta,
        (int) ($_POST['id_chofer'] ?? 0),
        (float) ($_POST['cantidad'] ?? 0),
        $comentarios,
        (int) ($_POST['num_puches'] ?? 0),
        $km_inicio,
        $km_final,
        $total_km,
        (float) ($_POST['diesel_inicio'] ?? 0),
        (float) ($_POST['diesel_final'] ?? 0),
        (float) ($_POST['diesel_cargado'] ?? 0),
        (int) ($_POST['id_despachador'] ?? 0),
    ];

    for ($i = 1; $i <= 11; $i++) {
        $params[] = $slots['colonia'][$i];
    }
    for ($i = 1; $i <= 11; $i++) {
        $params[] = $slots['pct'][$i];
    }

    $params[] = $slots['n'];
    $params[] = $slots['suma'];
    $params[] = $slots['pct_efectividad'];

    for ($i = 1; $i <= 11; $i++) {
        $params[] = $slots['hab'][$i];
    }

    $params[] = (int) $_SESSION['id_usuario'];

    $cols = 'id_folio, fecha_orden, fecha_captura, turno, id_tipo_unidad, id_unidad, numero_ruta, id_chofer, cantidad, comentarios, num_puches, km_inicio, km_final, total_km, diesel_inicio, diesel_final, diesel_cargado, id_despachador';
    for ($i = 1; $i <= 11; $i++) {
        $cols .= ', colonia_' . $i;
    }
    for ($i = 1; $i <= 11; $i++) {
        $cols .= ', pct_colonia_' . $i;
    }
    $cols .= ', num_colonias_ruta, suma_pct_atendida, pct_efectividad';
    for ($i = 1; $i <= 11; $i++) {
        $cols .= ', habitantes_' . $i;
    }
    $cols .= ', id_usuario';

    $placeholders = implode(',', array_fill(0, count($params), '?'));
    $stmt = $pdo->prepare("INSERT INTO registro_diario ($cols) VALUES ($placeholders)");
    $stmt->execute($params);

    $pdo->commit();

    $u = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
    echo "<script>
        alert('Orden guardada exitosamente\\n\\nFolio: {$id_folio}\\nUsuario: {$u}');
        window.location='captura.php';
    </script>";
} catch (Exception $e) {
    $pdo->rollBack();
    $msg = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>
        alert('Error al guardar:\\n{$msg}');
        window.history.back();
    </script>";
}
