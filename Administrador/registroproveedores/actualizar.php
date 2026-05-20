<?php
// actualizar.php (Dentro de la carpeta registroproveedores)
require("../Cliente/conexiondos.php");

if (isset($_POST["id"], $_POST["nombre"], $_POST["ap_paterno"], $_POST["telefono"], $_POST["direccion"])) {
    $id         = intval($_POST["id"]);
    $nombre     = $mysqli->real_escape_string(trim($_POST["nombre"]));
    $ap_paterno = $mysqli->real_escape_string(trim($_POST["ap_paterno"]));
    $telefono   = $mysqli->real_escape_string(trim($_POST["telefono"]));
    $direccion  = $mysqli->real_escape_string(trim($_POST["direccion"]));

    // Actualización directa respetando el nombre 'proovedor' de tu BD
    $consulta = "UPDATE proovedor 
                 SET Nombre = '$nombre', 
                     Ap_paterno = '$ap_paterno', 
                     Telefono = '$telefono', 
                     Direccion = '$direccion' 
                 WHERE Id_proovedor = $id";

    $mysqli->query($consulta);
}

// Redireccionar al index de proveedores
header("Location: index.php");
exit();
?>