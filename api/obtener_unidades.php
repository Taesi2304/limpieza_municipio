<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$id_tipo = $_GET['id_tipo'] ?? 0;
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM unidades WHERE id_tipo = ?");
$stmt->execute([$id_tipo]);
echo json_encode($stmt->fetchAll());
