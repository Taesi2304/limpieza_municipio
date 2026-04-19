<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$id_tipo = (int) ($_GET['id_tipo'] ?? 0);
$pdo = getConnection();
$stmt = $pdo->prepare('SELECT id_unidad, id_tipo, numero AS numero_unidad FROM numero_unidad WHERE id_tipo = ? ORDER BY numero');
$stmt->execute([$id_tipo]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
