<?php
require("conexiondos.php");

$id = $_GET['id'];

$consulta = "DELETE FROM prendas WHERE Id_prendas=$id";
$mysqli->query($consulta);

header("Location: index.php");
?>