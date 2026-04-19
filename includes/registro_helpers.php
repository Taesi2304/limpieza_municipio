<?php
/**
 * Catálogo de colonias por número de ruta (tabla rutas).
 */
function catalogo_colonias_por_ruta(PDO $pdo, int $numero_ruta): array
{
    $stmt = $pdo->prepare('SELECT id_registro, colonia, habitantes FROM rutas WHERE id_ruta = ? ORDER BY id_registro');
    $stmt->execute([$numero_ruta]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Rellena colonia_1..11, pct_colonia_1..11 y habitantes_1..11 solo con las colonias de la ruta elegida.
 * Los slots restantes quedan en NULL. Los % vienen de $_POST['pct_colonia'][slot] (1..N).
 *
 * @param  array $pctPost  $_POST['pct_colonia'] por slot 1..N
 * @return array{colonia: array<int,string|null>, pct: array<int,float|null>, hab: array<int,float|null>, suma: float, n: int, pct_efectividad: float}
 */
function construir_slots_colonias(PDO $pdo, int $numero_ruta, array $pctPost): array
{
    $filasRuta = catalogo_colonias_por_ruta($pdo, $numero_ruta);

    $colonia = array_fill(1, 11, null);
    $pct = array_fill(1, 11, null);
    $hab = array_fill(1, 11, null);
    $suma = 0.0;

    $n = count($filasRuta);

    foreach ($filasRuta as $idx => $f) {
        $slot = $idx + 1;
        if ($slot > 11) {
            break;
        }
        $colonia[$slot] = $f['colonia'];
        $hab[$slot] = isset($f['habitantes']) ? (float) $f['habitantes'] : null;

        $raw = $pctPost[$slot] ?? $pctPost[(string) $slot] ?? '';
        if ($raw === '' || $raw === null) {
            $pct[$slot] = null;
        } else {
            $pv = round((float) $raw, 2);
            $pct[$slot] = $pv;
            $suma += $pv;
        }
    }

    $pct_efectividad = $n > 0 ? round($suma / $n, 2) : 0.0;

    return [
        'colonia' => $colonia,
        'pct' => $pct,
        'hab' => $hab,
        'suma' => $suma,
        'n' => $n,
        'pct_efectividad' => $pct_efectividad,
    ];
}
