<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$pdo = getConnection();
$result = $pdo->query("SELECT ultimo_folio + 1 as siguiente FROM control_folios LIMIT 1")->fetch();
echo json_encode(['folio' => $result['siguiente']]);
