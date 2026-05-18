<?php
require("conexiondos.php");

$id         = $_POST["id"];
$prendas    = $_POST["prendas"];
$num_piezas = $_POST["num_piezas"];

$consulta = "UPDATE prendas SET Prendas='$prendas', Num_piezas='$num_piezas' WHERE Id_prendas=$id";
$mysqli->query($consulta);

header("Location: index.php");
?>