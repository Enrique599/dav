<?php
require("conexiondos.php");

$id           = $_POST["id"];
$fecha        = $_POST["fecha"];
$realizacion  = $_POST["realizacion"];
$total        = $_POST["total"];
$id_cliente   = $_POST["id_cliente"];
$total_piezas = $_POST["total_piezas"];

$consulta = "UPDATE venta 
             SET Fecha='$fecha', Realizacion='$realizacion', Total='$total',
                 Id_cliente='$id_cliente', Total_piezas='$total_piezas'
             WHERE Id_venta=$id";

$mysqli->query($consulta);
header("Location: index.php");
?>