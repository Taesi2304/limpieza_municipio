<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: inicio.php');
    exit;
}
require_once 'includes/db.php';
$pdo = getConnection();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captura - Orden de Servicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="img/icono.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo2.css">
</head>

<body>
    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container-fluid">
            <span class="navbar-brand"> Captura de Orden de Servicio
            </span>
            <div>
                <span class="text-white me-3"><i class="bi bi-person-circle"></i> <?php echo $_SESSION['usuario']; ?></span>
                <a href="editar.php" class="btn btn-light btn-sm me-2"><i class="bi bi-pencil-square"></i> Editar</a>
                <a href="inicio.php?logout=1" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow">
            <div class="card-header card-header-success">
                <h4 class="mb-0"><i class="bi bi-envelope-paper"></i> Rellenar Orden </h4>
            </div>
            <div class="card-body">
                <form id="formulario" action="guardar_registro.php" method="POST">

                    <!-- Datos Generales de la orden  -->
                    <h5 class="section-title">Datos Generales</h5>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Folio de Solicitud </label> <br>
                            <div class="folio-badge" id="folio_display">---</div>
                            <input type="hidden" name="folio" id="folio_hidden">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-calendar-event"></i> Fecha Orden</label>
                            <input type="date" name="fecha_orden" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-clock"></i> Fecha y Hora Captura</label>
                            <input type="datetime-local" name="fecha_captura" id="fecha_captura" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Turno</label>
                            <select name="turno" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <option value="1">1 - 7:00 AM - 2:00 PM</option>
                                <option value="2">2 - 2:00 PM - 9:00 PM</option>
                                <option value="3">3 - 9:00 PM - 5:00 AM</option>
                                <option value="4">4 - 5:00 AM - 12:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ruta y Personal -->
                    <h5 class="section-title">Ruta y Personal</h5>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-signpost-2"></i> Ruta</label>
                            <select name="id_ruta" id="id_ruta" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <?php
                                $rutas = $pdo->query("SELECT * FROM rutas ORDER BY id_ruta")->fetchAll();
                                foreach ($rutas as $r) {
                                    echo "<option value='{$r['id_ruta']}'>{$r['nombre_ruta']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-person-badge"></i> Despachador</label>
                            <select name="id_despachador" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <?php
                                $desp = $pdo->query("SELECT * FROM personal WHERE puesto='Despachador' ORDER BY nombre_completo")->fetchAll();
                                foreach ($desp as $d) {
                                    echo "<option value='{$d['id_empleado']}'>{$d['nombre_completo']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-person-vcard"></i> Chofer</label>
                            <select name="id_chofer" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <?php
                                $chof = $pdo->query("SELECT * FROM personal WHERE puesto='Chofer' ORDER BY nombre_completo")->fetchAll();
                                foreach ($chof as $c) {
                                    echo "<option value='{$c['id_empleado']}'>{$c['nombre_completo']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Cantidades -->
                    <h5 class="section-title">Cantidades</h5>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-box"></i> Cantidad KG</label>
                            <input type="number" name="cantidad_kg" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cantidad Puches</label>
                            <input type="number" name="cantidad_puches" class="form-control" min="0" placeholder="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-truck"></i> Tipo de Unidad</label>
                            <select name="id_tipo_unidad" id="id_tipo_unidad" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <?php
                                $tipos = $pdo->query("SELECT * FROM tipos_unidad ORDER BY nombre")->fetchAll();
                                foreach ($tipos as $t) {
                                    echo "<option value='{$t['id_tipo']}'>{$t['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-truck-front"></i> N° Unidad</label>
                            <select name="id_unidad" id="id_unidad" class="form-select" required disabled>
                                <option value="">Primero Seleccionar Tipo de Unidad</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kilómetros -->
                    <h5 class="section-title">Kilómetros</h5>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-speedometer2"></i> KM Salida</label>
                            <input type="number" id="km_salida" name="km_salida" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-speedometer2"></i> KM Entrada</label>
                            <input type="number" id="km_entrada" name="km_entrada" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-speedometer"></i> Total KM </label>
                            <input type="number" id="total_km" name="total_km" class="form-control readonly-field" readonly>
                        </div>
                    </div>

                    <!-- Diesel -->
                    <h5 class="section-title">Diesel</h5>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-fuel-pump-diesel-fill"></i> Diesel Inicio</label>
                            <input type="number" id="diesel_inicio" name="diesel_inicio" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-fuel-pump-diesel"></i> Diesel Final</label>
                            <input type="number" id="diesel_final" name="diesel_final" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-fuel-pump"></i> Diesel Cargado</label>
                            <input type="number" id="diesel_cargado" name="diesel_cargado" class="form-control" step="0.01" min="0" value="0" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-fuel-pump-fill"></i> Total Diesel </label>
                            <input type="number" id="total_diesel" name="total_diesel" class="form-control readonly-field" readonly>
                        </div>
                    </div>

                    <!-- TABLA 1: Detalle de Colonias -->
                    <div id="tabla_colonias_wrapper" style="display: none;">
                        <h5 class="section-title">Detalle de Colonias por Ruta</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="table-success">
                                    <tr>
                                        <th class="text-center" width="5%">#</th>
                                        <th width="40%">Nombre de Colonia</th>
                                        <th class="text-center" width="30%">% Recolectado</th>
                                        <th class="text-center" width="25%">Habitantes</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_colonias"></tbody>
                            </table>
                        </div>

                        <!-- TABLA 2: Cálculo de Beneficiados -->
                        <h5 class="section-title">Beneficiados</h5>
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
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" id="btn_guardar" class="btn btn-success-custom btn-lg" disabled>
                            <i class="bi bi-send-check-fill"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="js/calculos.js"></script>
    <script src="js/form_dinamico.js"></script>
    <script src="js/cascada_unidades.js"></script>
    <script src="js/carga_folio_fecha.js"></script>
    <!-- <script>
        // Cargar folio provisional
        fetch('api/obtener_folio_provisional.php')
            .then(r => r.json())
            .then(data => {
                document.getElementById('folio_display').textContent = data.folio;
                document.getElementById('folio_hidden').value = data.folio;
            });

        // Auto-completar fechas
        document.querySelector('[name="fecha_orden"]').valueAsDate = new Date();
        const now = new Date();
        const datetime = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0') + 'T' +
            String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0');
        document.getElementById('fecha_captura').value = datetime;
    </script> -->
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