<?php
// actualizar.php (Dentro de la carpeta usuarios)
require("conexiondos.php");

if (isset($_POST["id"], $_POST["nombre"], $_POST["ap_paterno"], $_POST["telefono"], $_POST["direccion"], $_POST["rol"])) {
    $id         = intval($_POST["id"]);
    $nombre     = $mysqli->real_escape_string(trim($_POST["nombre"]));
    $ap_paterno = $mysqli->real_escape_string(trim($_POST["ap_paterno"]));
    $telefono   = $mysqli->real_escape_string(trim($_POST["telefono"]));
    $direccion  = $mysqli->real_escape_string(trim($_POST["direccion"]));
    $rol        = $mysqli->real_escape_string($_POST["rol"]);

    // Verificar si se capturó una nueva contraseña
    if (!empty($_POST["contrasena"])) {
        $nueva_pass = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
        $consulta = "UPDATE empleado 
                     SET Nombre='$nombre', Ap_paterno='$ap_paterno', Telefono='$telefono', 
                         Direccion='$direccion', Contrasena='$nueva_pass', rol='$rol' 
                     WHERE Id_empleado = $id";
    } else {
        // No se modifica la contraseña si el campo llegó vacío
        $consulta = "UPDATE empleado 
                     SET Nombre='$nombre', Ap_paterno='$ap_paterno', Teléfono='$telefono', 
                         Direccion='$direccion', rol='$rol' 
                     WHERE Id_empleado = $id";
    }

    $mysqli->query($consulta);
}

// Redireccionar al panel principal de usuarios
header("Location: index.php");
exit();
?>