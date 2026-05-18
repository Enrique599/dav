<?php
require("conexiondos.php");

$id = $_GET['id'];

$consulta = "DELETE FROM venta WHERE Id_venta=$id";
$mysqli->query($consulta);

header("Location: index.php");
?>