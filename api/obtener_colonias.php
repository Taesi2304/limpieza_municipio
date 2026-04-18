<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$id_ruta = $_GET['id_ruta'] ?? 0;
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM colonias WHERE id_ruta = ? ORDER BY id_colonia");
$stmt->execute([$id_ruta]);
echo json_encode($stmt->fetchAll());
