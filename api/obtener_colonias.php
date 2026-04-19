<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$id_ruta = (int) ($_GET['id_ruta'] ?? 0);
$pdo = getConnection();
$stmt = $pdo->prepare('SELECT id_registro, id_ruta, colonia AS nombre_colonia, habitantes FROM rutas WHERE id_ruta = ? ORDER BY id_registro');
$stmt->execute([$id_ruta]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$slot = 1;
foreach ($rows as &$r) {
    $r['slot'] = $slot++;
}

echo json_encode($rows);
