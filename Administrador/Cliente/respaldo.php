<?php
require("conexiondos.php");

// Datos de conexión
$host     = "localhost";
$usuario  = "root";
$password = "";
$base     = "dav";

// Nombre del archivo con fecha y hora
$fecha    = date('Y-m-d_H-i-s');
$archivo  = "respaldo_$fecha.sql";

// Encabezado para descargar el archivo
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=$archivo");
header('Content-Transfer-Encoding: binary');

// Inicio del archivo SQL
echo "-- Respaldo de base de datos: $base\n";
echo "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

// Obtener todas las tablas
$tablas = $mysqli->query("SHOW TABLES");

while($tabla = $tablas->fetch_array()){
    $nombre_tabla = $tabla[0];

    // DROP TABLE
    echo "DROP TABLE IF EXISTS `$nombre_tabla`;\n";

    // CREATE TABLE
    $create = $mysqli->query("SHOW CREATE TABLE `$nombre_tabla`");
    $row    = $create->fetch_array();
    echo $row[1] . ";\n\n";

    // INSERT datos
    $datos = $mysqli->query("SELECT * FROM `$nombre_tabla`");

    while($fila = $datos->fetch_row()){
        $valores = array_map(function($val) use ($mysqli){
            if($val === null) return "NULL";
            return "'" . $mysqli->real_escape_string($val) . "'";
        }, $fila);

        echo "INSERT INTO `$nombre_tabla` VALUES (" . implode(", ", $valores) . ");\n";
    }
    echo "\n";
}

echo "SET FOREIGN_KEY_CHECKS=1;\n";
exit;
?>