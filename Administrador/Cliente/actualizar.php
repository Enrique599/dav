<?php
require("conexiondos.php");

$id         = $_POST["id"];
$nombre     = $_POST["nombre"];
$ap_paterno = $_POST["ap_paterno"];
$telefono   = $_POST["telefono"];
$direccion  = $_POST["direccion"];
$cp         = $_POST["cp"];

if(!empty($_POST["contrasena"])){
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
    $consulta = "UPDATE cliente SET Nombre='$nombre', Ap_paterno='$ap_paterno', Telefono='$telefono',
                 Contrasena='$contrasena', Direccion='$direccion', CP='$cp' WHERE Id_cliente=$id";
} else {
    $consulta = "UPDATE cliente SET Nombre='$nombre', Ap_paterno='$ap_paterno', Telefono='$telefono',
                 Direccion='$direccion', CP='$cp' WHERE Id_cliente=$id";
}

$mysqli->query($consulta);
header("Location: index.php");
?>