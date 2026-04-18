<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: inicio.php');
    exit;
}
require_once 'includes/db.php';
$pdo = getConnection();

$registro = null;
$colonias = [];
$error = '';

// Buscar registro por folio
if (isset($_GET['folio'])) {
    $folio = $_GET['folio'];

    // Obtener registro principal
    $stmt = $pdo->prepare("SELECT * FROM registro_diario WHERE folio = ?");
    $stmt->execute([$folio]);
    $registro = $stmt->fetch();

    if ($registro) {
        // Obtener colonias de la ruta
        $stmtCol = $pdo->prepare("SELECT * FROM colonias WHERE id_ruta = ? ORDER BY id_colonia");
        $stmtCol->execute([$registro['id_ruta']]);
        $colonias = $stmtCol->fetchAll();

        // Obtener porcentajes guardados: [id_colonia => porcentaje_recolectado]
        $stmtDet = $pdo->prepare("SELECT id_colonia, porcentaje_recolectado FROM registro_detalle_colonias WHERE folio = ?");
        $stmtDet->execute([$folio]);
        $detalle = $stmtDet->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($colonias as &$col) {
            $col['porcentaje'] = $detalle[$col['id_colonia']] ?? 0;
        }
    } else {
        $error = "No se encontró el folio: $folio";
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
    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">Editar Orden de Servicio</span>
            <div>
                <span class="text-white me-3"><i class="bi bi-person-circle"></i> <?php echo $_SESSION['usuario']; ?></span>
                <a href="captura.php" class="btn btn-light btn-sm me-2"><i class="bi bi-file-earmark-plus"></i> Nueva</a>
                <a href="inicio.php?logout=1" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow">
            <div class="card-header card-header-success">
                <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Buscar y Editar Orden</h4>
            </div>
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
                        <input type="hidden" name="folio" value="<?php echo $registro['folio']; ?>">

                        <h5 class="section-title"> Folio: <?php echo $registro['folio']; ?></h5>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-person"></i> Capturado por:</label>
                            <input type="text" class="form-control readonly-field" value="<?php echo $registro['usuario_captura']; ?>" readonly>
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
                                <select name="id_ruta" id="id_ruta_edit" class="form-select" required>
                                    <?php
                                    $rutas = $pdo->query("SELECT * FROM rutas ORDER BY id_ruta")->fetchAll();
                                    foreach ($rutas as $r) {
                                        $sel = ($r['id_ruta'] == $registro['id_ruta']) ? 'selected' : '';
                                        echo "<option value='{$r['id_ruta']}' $sel>{$r['nombre_ruta']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-person-badge"></i> Despachador</label>
                                <select name="id_despachador" class="form-select" required>
                                    <?php
                                    $desp = $pdo->query("SELECT * FROM personal WHERE puesto='Despachador' ORDER BY nombre_completo")->fetchAll();
                                    foreach ($desp as $d) {
                                        $sel = ($d['id_empleado'] == $registro['id_despachador']) ? 'selected' : '';
                                        echo "<option value='{$d['id_empleado']}' $sel>{$d['nombre_completo']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-person-vcard"></i> Chofer</label>
                                <select name="id_chofer" class="form-select" required>
                                    <?php
                                    $chof = $pdo->query("SELECT * FROM personal WHERE puesto='Chofer' ORDER BY nombre_completo")->fetchAll();
                                    foreach ($chof as $c) {
                                        $sel = ($c['id_empleado'] == $registro['id_chofer']) ? 'selected' : '';
                                        echo "<option value='{$c['id_empleado']}' $sel>{$c['nombre_completo']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <h5 class="section-title"> Cantidades</h5>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-box"></i> Cantidad KG</label>
                                <input type="number" name="cantidad_kg" class="form-control" step="0.01" value="<?php echo $registro['cantidad_kg']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cantidad Puches</label>
                                <input type="number" name="cantidad_puches" class="form-control" value="<?php echo $registro['cantidad_puches']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-truck"></i> Tipo de Unidad</label>
                                <select name="id_tipo_unidad" id="id_tipo_unidad_edit" class="form-select" required>
                                    <?php
                                    $tipos = $pdo->query("SELECT * FROM tipos_unidad ORDER BY nombre")->fetchAll();
                                    foreach ($tipos as $t) {
                                        $sel = ($t['id_tipo'] == $registro['id_tipo_unidad']) ? 'selected' : '';
                                        echo "<option value='{$t['id_tipo']}' $sel>{$t['nombre']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-truck-front"></i> N° de Unidad</label>
                                <select name="id_unidad" id="id_unidad_edit" class="form-select" required>
                                    <?php
                                    $unidades = $pdo->query("SELECT * FROM unidades WHERE id_tipo = {$registro['id_tipo_unidad']}")->fetchAll();
                                    foreach ($unidades as $u) {
                                        $sel = ($u['id_unidad'] == $registro['id_unidad']) ? 'selected' : '';
                                        echo "<option value='{$u['id_unidad']}' $sel>{$u['numero_unidad']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <h5 class="section-title"> Kilómetros</h5>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-speedometer2"></i>KM Salida</label>
                                <input type="number" id="km_salida" name="km_salida" class="form-control" step="0.01" value="<?php echo $registro['km_salida']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-speedometer2"></i>KM Entrada</label>
                                <input type="number" id="km_entrada" name="km_entrada" class="form-control" step="0.01" value="<?php echo $registro['km_entrada']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-speedometer"></i> Total KM</label>
                                <input type="number" id="total_km" name="total_km" class="form-control readonly-field" value="<?php echo $registro['total_km']; ?>" readonly>
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
                                <input type="number" id="total_diesel" name="total_diesel" class="form-control readonly-field" value="<?php echo $registro['total_diesel']; ?>" readonly>
                            </div>
                        </div>

                        <!-- TABLA 1: Colonias Editables -->
                        <?php if (!empty($colonias)): ?>
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
                                        <?php foreach ($colonias as $index => $col): ?>
                                            <tr>
                                                <td class="text-center fw-bold"><?php echo $index + 1; ?></td>
                                                <td><?php echo $col['nombre_colonia']; ?></td>
                                                <td class="text-center">
                                                    <input type="number"
                                                        name="pct_colonia[<?php echo $col['id_colonia']; ?>]"
                                                        class="form-control form-control-sm pct-input"
                                                        min="0" max="100" step="0.1"
                                                        value="<?php echo $col['porcentaje']; ?>"
                                                        placeholder="0-100">
                                                </td>
                                                <td class="text-center"><?php echo number_format($col['habitantes']); ?></td>
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
                                            <td class="text-center fw-bold" id="tabla2_fecha">-</td>
                                            <td class="text-center fw-bold" id="tabla2_hora">-</td>
                                            <td class="text-center fw-bold" id="tabla2_suma">0.0</td>
                                            <td class="text-center fw-bold" id="tabla2_colonias">0</td>
                                            <td class="text-center fw-bold" id="tabla2_efectividad">0.0%</td>
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
            </p>
        </div>
    </div>
</footer>

</html>