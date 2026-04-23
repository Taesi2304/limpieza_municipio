<?php
// funcion para establecer la conexión a la base de datos
//utiliza PDO (PHP Data Objects) para conectarse a una base de datos
function getConnection() {
    try {
        //configurcion de los parametros de conexion 
        $pdo = new PDO(
            'mysql:host=localhost; 
        dbname=limpieza_municipio;
        charset=utf8mb4', 
        'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // Configura atributos de PDO:
        //PDO::ATTR_ERRMODE: Modo de errores (lanza excepciones).
        //PDO::ATTR_DEFAULT_FETCH_MODE: Modo de obtención de datos (asociativo).

        return $pdo;
    } catch(PDOException $e) {  
        //si ocurre un error durante la conexión, se captura la excepción y se muestra un mensaje de error.
        die("Error de conexión: " . $e->getMessage());
    } 

}
?> 