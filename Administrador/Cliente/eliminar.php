<?php
require("conexiondos.php");

$id = $_GET['id'];

$consulta = "DELETE FROM cliente WHERE Id_cliente=$id";
$mysqli->query($consulta);

header("Location: index.php");
?>