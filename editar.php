<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: inicio.php');
    exit;
}
require_once 'includes/db.php';
$pdo = getConnection();

$registro = null;
$colonias_edit = [];
$error = '';

if (isset($_GET['folio'])) {
    $folio = (int) $_GET['folio'];

    $stmt = $pdo->prepare('
        SELECT r.*, u.nombre_usuario AS capturador_nombre
        FROM registro_diario r
        INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE r.id_folio = ?
    ');
    $stmt->execute([$folio]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        for ($i = 1; $i <= 11; $i++) {
            $cn = $registro['colonia_' . $i] ?? null;
            if ($cn === null || $cn === '') {
                continue;
            }
            $colonias_edit[] = [
                'slot' => $i,
                'nombre_colonia' => $cn,
                'habitantes' => $registro['habitantes_' . $i],
                'porcentaje' => $registro['pct_colonia_' . $i],
            ];
        }
    } else {
        $error = 'No se encontró el folio: ' . $folio;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar - Orden de Servicio</title>
    <link rel="icon" href="img/icono.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo2.css">
</head>

<body>
    <nav class="navbar navbar-light bg-white border-bottom mb-4 app-navbar">
        <div class="container-fluid">
            <span class="navbar-brand text-dark mb-0">Editar Orden de Servicio</span>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted small me-1"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
                <a href="captura.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-plus"></i> Nueva</a>
                <a href="inicio.php?logout=1" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow card-form-panel">
            <div class="card-body">

                <!-- Buscador -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-search"></i> Buscar por Folio</label>
                            <input type="number" name="folio" class="form-control" placeholder="Ejemplo: 1001" value="<?php echo $_GET['folio'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
                        </div>
                    </div>
                </form>

                <?php if ($error): ?>
                    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($registro): ?>
                    <hr>

                    <form action="actualizar_registro.php" method="POST">
                        <input type="hidden" name="id_folio" value="<?php echo (int) $registro['id_folio']; ?>">

                        <h5 class="section-title"> Folio: <?php echo (int) $registro['id_folio']; ?></h5>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-person"></i> Capturado por:</label>
                            <input type="text" class="form-control readonly-field" value="<?php echo htmlspecialchars($registro['capturador_nombre']); ?>" readonly>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-calendar-event"></i> Fecha Orden</label>
                                <input type="date" name="fecha_orden" class="form-control" value="<?php echo $registro['fecha_orden']; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Turno</label>
                                <select name="turno" class="form-select" required>
                                    <?php
                                    $turnos = [
                                        1 => '1 - 7:00 AM - 2:00 PM',
                                        2 => '2 - 2:00 PM - 9:00 PM',
                                        3 => '3 - 9:00 PM - 5:00 AM',
                                        4 => '4 - 5:00 AM - 12:00 PM',
                                    ];
                                    foreach ($turnos as $val => $label):
                                        $sel = ($registro['turno'] == $val) ? 'selected' : '';
                                        echo "<option value='$val' $sel>$label</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-clock-history"></i> Fecha Captura</label>
                                <input type="text" class="form-control readonly-field" value="<?php echo date('d/m/Y H:i', strtotime($registro['fecha_captura'])); ?>" readonly>
                            </div>
                        </div>

                        <h5 class="section-title"> Ruta y Personal</h5>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-signpost-2"></i> Ruta</label>
                                <select name="numero_ruta" id="id_ruta_edit" class="form-select" required>
                                    <?php
                                    $nums = $pdo->query('SELECT DISTINCT id_ruta FROM rutas ORDER BY id_ruta')->fetchAll(PDO::FETCH_COLUMN);
                                    foreach ($nums as $nr) {
                                        $nr = (int) $nr;
                                        $sel = ((int) $registro['numero_ruta'] === $nr) ? 'selected' : '';
                                        echo "<option value='{$nr}' {$sel}>Ruta {$nr}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-person-badge"></i> Despachador</label>
                                <select name="id_despachador" class="form-select" required>
                                    <?php
                                    $desp = $pdo->query('SELECT * FROM despachador ORDER BY nombre')->fetchAll();
                                    foreach ($desp as $d) {
                                        $sel = ((int) $d['id_despachador'] === (int) $registro['id_despachador']) ? 'selected' : '';
                                        echo '<option value="' . (int) $d['id_despachador'] . '" ' . $sel . '>' . htmlspecialchars($d['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-person-vcard"></i> Chofer</label>
                                <select name="id_chofer" class="form-select" required>
                                    <?php
                                    $chof = $pdo->query('SELECT * FROM chofer ORDER BY nombre')->fetchAll();
                                    foreach ($chof as $c) {
                                        $sel = ((int) $c['id_chofer'] === (int) $registro['id_chofer']) ? 'selected' : '';
                                        echo '<option value="' . (int) $c['id_chofer'] . '" ' . $sel . '>' . htmlspecialchars($c['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <h5 class="section-title"> Cantidades</h5>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-box"></i> Cantidad</label>
                                <input type="number" name="cantidad" class="form-control" step="0.01" value="<?php echo htmlspecialchars($registro['cantidad']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cantidad Puches</label>
                                <input type="number" name="num_puches" class="form-control" value="<?php echo (int) $registro['num_puches']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-truck"></i> Tipo de Unidad</label>
                                <select name="id_tipo_unidad" id="id_tipo_unidad_edit" class="form-select" required>
                                    <?php
                                    $tipos = $pdo->query('SELECT * FROM tipo_unidad ORDER BY nombre')->fetchAll();
                                    foreach ($tipos as $t) {
                                        $sel = ($t['id_tipo'] == $registro['id_tipo_unidad']) ? 'selected' : '';
                                        echo "<option value='{$t['id_tipo']}' $sel>{$t['nombre']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label"><i class="bi bi-chat-left-text"></i> Comentarios</label>
                                <textarea name="comentarios" class="form-control" rows="2"><?php echo htmlspecialchars((string) ($registro['comentarios'] ?? '')); ?></textarea>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-truck-front"></i> N° de Unidad</label>
                                <select name="id_unidad" id="id_unidad_edit" class="form-select" required>
                                    <?php
                                    $stmtU = $pdo->prepare('SELECT id_unidad, numero FROM numero_unidad WHERE id_tipo = ? ORDER BY numero');
                                    $stmtU->execute([(int) $registro['id_tipo_unidad']]);
                                    $unidades = $stmtU->fetchAll();
                                    foreach ($unidades as $u) {
                                        $sel = ((int) $u['id_unidad'] === (int) $registro['id_unidad']) ? 'selected' : '';
                                        echo '<option value="' . (int) $u['id_unidad'] . '" ' . $sel . '>' . htmlspecialchars($u['numero']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <h5 class="section-title"> Kilómetros</h5>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-speedometer2"></i>KM Salida</label>
                                <input type="number" id="km_inicio" name="km_inicio" class="form-control" step="0.01" value="<?php echo htmlspecialchars($registro['km_inicio']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-speedometer2"></i> KM final (menor que inicio)</label>
                                <input type="number" id="km_final" name="km_final" class="form-control" step="0.01" value="<?php echo htmlspecialchars($registro['km_final']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-speedometer"></i> Total KM</label>
                                <input type="number" id="total_km" class="form-control readonly-field" value="<?php echo htmlspecialchars($registro['total_km']); ?>" readonly>
                            </div>
                        </div>

                        <h5 class="section-title"> Diesel</h5>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-fuel-pump-diesel-fill"></i> Diesel Inicio</label>
                                <input type="number" id="diesel_inicio" name="diesel_inicio" class="form-control" step="0.01" value="<?php echo $registro['diesel_inicio']; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-fuel-pump-diesel"></i> Diesel Final</label>
                                <input type="number" id="diesel_final" name="diesel_final" class="form-control" step="0.01" value="<?php echo $registro['diesel_final']; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-fuel-pump"></i> Diesel Cargado</label>
                                <input type="number" id="diesel_cargado" name="diesel_cargado" class="form-control" step="0.01" value="<?php echo $registro['diesel_cargado']; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-fuel-pump-fill"></i> Total Diesel</label>
                                <input type="number" id="total_diesel" class="form-control readonly-field" value="<?php
                                    $td = max(0, ((float) $registro['diesel_inicio'] - (float) $registro['diesel_final']) + (float) $registro['diesel_cargado']);
echo htmlspecialchars($td);
?>" readonly>
                            </div>
                        </div>

                        <!-- TABLA 1: Colonias Editables -->
                        <?php if (!empty($colonias_edit)): ?>
                            <h5 class="section-title">Detalle de Colonias</h5>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="table-success">
                                        <tr>
                                            <th class="text-center" width="5%">#</th>
                                            <th width="45%">Colonia</th>
                                            <th class="text-center" width="25%">% Recolectado</th>
                                            <th class="text-center" width="25%">Habitantes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($colonias_edit as $index => $col): ?>
                                            <tr>
                                                <td class="text-center fw-bold"><?php echo $index + 1; ?></td>
                                                <td><?php echo htmlspecialchars($col['nombre_colonia']); ?></td>
                                                <td class="text-center">
                                                    <input type="number"
                                                        name="pct_colonia[<?php echo (int) $col['slot']; ?>]"
                                                        class="form-control form-control-sm pct-input"
                                                        min="0" max="100" step="0.1"
                                                        value="<?php echo htmlspecialchars((string) ($col['porcentaje'] ?? '')); ?>"
                                                        placeholder="0-100">
                                                </td>
                                                <td class="text-center"><?php echo $col['habitantes'] !== null ? number_format((float) $col['habitantes']) : ''; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- TABLA 2: Cálculo de Beneficiados -->
                            <h5 class="section-title">Cálculo de Beneficiados</h5>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-primary">
                                        <tr>
                                            <th class="text-center">Fecha</th>
                                            <th class="text-center">Hora</th>
                                            <th class="text-center">Suma %</th>
                                            <th class="text-center">Colonias</th>
                                            <th class="text-center">% Efectividad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center fw-bold" id="tabla2_fecha" data-from-server="1"><?php echo date('d/m/Y', strtotime($registro['fecha_orden'])); ?></td>
                                            <td class="text-center fw-bold" id="tabla2_hora" data-from-server="1"><?php echo date('H:i', strtotime($registro['fecha_captura'])); ?></td>
                                            <td class="text-center fw-bold" id="tabla2_suma"><?php echo htmlspecialchars($registro['suma_pct_atendida']); ?></td>
                                            <td class="text-center fw-bold" id="tabla2_colonias"><?php echo (int) $registro['num_colonias_ruta']; ?></td>
                                            <td class="text-center fw-bold" id="tabla2_efectividad"><?php echo htmlspecialchars($registro['pct_efectividad']); ?>%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success-custom btn-lg">
                                <i class="bi bi-send-check-fill"></i> Actualizar Orden.
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>



    <script src="js/calculos.js"></script>
    <script src="js/bd_edit_tablas_unidades.js"></script>

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
            <p>Integrantes:
                <br> Jessica Gallegos Rodriguez
                <br> Stephany Chavez
                <br> Jan Karlo Armendariz
                <br> Joel Garcia
                <br> Brandon Velazquez
            </p>
        </div>
    </div>
</footer>

</html>