<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "dav";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}
?>
