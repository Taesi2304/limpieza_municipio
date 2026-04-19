<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$pdo = getConnection();
$row = $pdo->query('SELECT COALESCE(MAX(id_folio), 0) + 1 AS siguiente FROM folio')->fetch(PDO::FETCH_ASSOC);
echo json_encode(['folio' => (int) $row['siguiente']]);
