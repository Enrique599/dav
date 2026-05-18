<?php
require("conexiondos.php");
date_default_timezone_set('America/Mexico_City');

$hora = (int)date('H');
if($hora >= 6 && $hora < 12)       $saludo = "☀️ ¡Buenos días!";
elseif($hora >= 12 && $hora < 19)  $saludo = "🌤️ ¡Buenas tardes!";
else                                $saludo = "🌙 ¡Buenas noches!";

// INSERT — lógica original intacta
if(isset($_POST["nombre"], $_POST["ap_paterno"], $_POST["telefono"],
         $_POST["contrasena"], $_POST["direccion"], $_POST["cp"])){
    $nombre     = $_POST["nombre"];
    $ap_paterno = $_POST["ap_paterno"];
    $telefono   = $_POST["telefono"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
    $direccion  = $_POST["direccion"];
    $cp         = $_POST["cp"];

    $consulta = "INSERT INTO cliente(Nombre, Ap_paterno, Telefono, Contrasena, Direccion, CP)
                 VALUES ('$nombre', '$ap_paterno', '$telefono', '$contrasena', '$direccion', '$cp')";
    $mysqli->query($consulta);
    $guardado = true;
}

$sql = 'SELECT * FROM cliente';
$impresion = $mysqli->query($sql);

// Variables para el layout
$page_id    = 'clientes';
$page_title = 'Registro de Clientes';
include("layout_header.php");
?>

<div class="ptitle">Registro de Clientes</div>
<div class="psub"><?php echo $saludo; ?></div>

<?php if(isset($guardado)): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> Cliente guardado correctamente.</div>
<?php endif; ?>

<!-- FORMULARIO -->
<div class="fcard">
  <form action="index.php" method="post">
    <div class="fgrid">
      <div class="fg"><label>Nombre *</label>
        <input class="fc" type="text" name="nombre" placeholder="Nombre" required></div>
      <div class="fg"><label>Apellido Paterno *</label>
        <input class="fc" type="text" name="ap_paterno" placeholder="Apellido Paterno" required></div>
      <div class="fg"><label>Teléfono *</label>
        <input class="fc" type="text" name="telefono" placeholder="Teléfono" required></div>
      <div class="fg"><label>Contraseña *</label>
        <input class="fc" type="password" name="contrasena" placeholder="Contraseña" required></div>
      <div class="fg"><label>Dirección *</label>
        <input class="fc" type="text" name="direccion" placeholder="Dirección" required></div>
      <div class="fg"><label>Código Postal *</label>
        <input class="fc" type="text" name="cp" placeholder="Código Postal" required></div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
  </form>
</div>



<!-- TABLA -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-users" style="color:var(--accent)"></i> Lista de Clientes</div>
  </div>
  <table>
    <thead>
      <tr>
        <th>ID</th><th>Nombre</th><th>Ap. Paterno</th>
        <th>Teléfono</th><th>Dirección</th><th>CP</th>
        <th>Eliminar</th><th>Modificar</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $impresion->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['Id_cliente']; ?></td>
        <td><?php echo $row['Nombre']; ?></td>
        <td><?php echo $row['Ap_paterno']; ?></td>
        <td><?php echo $row['Telefono']; ?></td>
        <td><?php echo $row['Direccion']; ?></td>
        <td><?php echo $row['CP']; ?></td>
        <td>
          <a href="eliminar.php?id=<?php echo $row['Id_cliente']; ?>"
             class="ib ib-del"
             onclick="return confirm('¿Eliminar a <?php echo addslashes($row['Nombre']); ?>?')">
            <i class="fas fa-times"></i>
          </a>
        </td>
        <td>
          <a href="modificar.php?id=<?php echo $row['Id_cliente']; ?>" class="ib ib-edit">
            <i class="fas fa-pencil-alt"></i>
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include("layout_footer.php"); ?>
