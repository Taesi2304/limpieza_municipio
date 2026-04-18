<?php
function getConnection() {
    try {
        $pdo = new PDO('mysql:host=localhost;
        dbname=limpieza_municipio;
        charset=utf8mb4', 
        'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
?>