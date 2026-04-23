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

    $id_folio = (int) ($_POST['id_folio'] ?? 0);
    if ($id_folio < 1) {
        throw new Exception('Folio inválido');
    }

    $numero_ruta = (int) ($_POST['numero_ruta'] ?? 0);
    $pctPost = isset($_POST['pct_colonia']) && is_array($_POST['pct_colonia']) ? $_POST['pct_colonia'] : [];
    $slots = construir_slots_colonias($pdo, $numero_ruta, $pctPost);

    $km_inicio = (float) ($_POST['km_inicio'] ?? 0);
    $km_final = (float) ($_POST['km_final'] ?? 0);
    if ($km_final < $km_inicio) {
        throw new Exception('Los km al final deben ser mayores o iguales que los km al inicio.');
    }
    $total_km = max(0, $km_final - $km_inicio);

    $comentarios = trim((string) ($_POST['comentarios'] ?? ''));
    $comentarios = $comentarios === '' ? null : $comentarios;

    $sets = [
        'fecha_orden = ?',
        'turno = ?',
        'id_tipo_unidad = ?',
        'id_unidad = ?',
        'numero_ruta = ?',
        'id_chofer = ?',
        'cantidad = ?',
        'comentarios = ?',
        'num_puches = ?',
        'km_inicio = ?',
        'km_final = ?',
        'total_km = ?',
        'diesel_inicio = ?',
        'diesel_final = ?',
        'diesel_cargado = ?',
        'id_despachador = ?',
    ];
    for ($i = 1; $i <= 11; $i++) {
        $sets[] = 'colonia_' . $i . ' = ?';
    }
    for ($i = 1; $i <= 11; $i++) {
        $sets[] = 'pct_colonia_' . $i . ' = ?';
    }
    $sets[] = 'num_colonias_ruta = ?';
    $sets[] = 'suma_pct_atendida = ?';
    $sets[] = 'pct_efectividad = ?';
    for ($i = 1; $i <= 11; $i++) {
        $sets[] = 'habitantes_' . $i . ' = ?';
    }

    $params = [
        $_POST['fecha_orden'] ?? null,
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

    $params[] = $id_folio;

    $sql = 'UPDATE registro_diario SET ' . implode(', ', $sets) . ' WHERE id_folio = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $pdo->commit();

    echo "<script>
        alert('Orden actualizada \\nFolio: {$id_folio}');
        window.location='editar.php?folio={$id_folio}';
    </script>";
} catch (Exception $e) {
    $pdo->rollBack();
    $msg = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>
        alert('Error: {$msg}');
        window.history.back();
    </script>";
}
