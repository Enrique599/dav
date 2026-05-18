<?php
require("../Cliente/conexiondos.php");
date_default_timezone_set('America/Mexico_City');

$hora = (int)date('H');
if($hora >= 6 && $hora < 12)       $saludo = "☀️ ¡Buenos días!";
elseif($hora >= 12 && $hora < 19)  $saludo = "🌤️ ¡Buenas tardes!";
else                                $saludo = "🌙 ¡Buenas noches!";

// INSERT
if (isset($_POST['nombre'], $_POST['ap_paterno'], $_POST['telefono'], $_POST['direccion'])) {
    $nombre     = trim($_POST['nombre']);
    $ap_paterno = trim($_POST['ap_paterno']);
    $telefono   = trim($_POST['telefono']);
    $direccion  = trim($_POST['direccion']);
    $stmt = $mysqli->prepare("INSERT INTO proovedor (Nombre, Ap_paterno, Telefono, Direccion) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $nombre, $ap_paterno, $telefono, $direccion);
    $stmt->execute();
    $guardado = true;
}

// ELIMINAR
if (isset($_GET['del'])) {
    $mysqli->query("DELETE FROM proovedor WHERE Id_proovedor=".(int)$_GET['del']);
    header("Location: index.php"); exit;
}

$proveedores = $mysqli->query("SELECT * FROM proovedor ORDER BY Id_proovedor DESC");

$page_id    = 'reg_proveedores';
$page_title = 'Registro de Proveedores';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Registro de Proveedores</div>
<div class="psub"><?php echo $saludo; ?> — Administra tu catálogo de proveedores</div>

<?php if(isset($guardado)): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> Proveedor registrado correctamente.</div>
<?php endif; ?>

<!-- FORMULARIO -->
<div class="fcard" style="margin-bottom:20px">
  <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:14px">
    <i class="fas fa-clipboard-list" style="color:var(--accent3)"></i> Agregar nuevo proveedor
  </div>
  <form action="index.php" method="post">
    <div class="fgrid">
      <div class="fg"><label>Nombre *</label>
        <input class="fc" type="text" name="nombre" placeholder="Nombre" required>
      </div>
      <div class="fg"><label>Apellido Paterno *</label>
        <input class="fc" type="text" name="ap_paterno" placeholder="Apellido" required>
      </div>
      <div class="fg"><label>Teléfono *</label>
        <input class="fc" type="text" name="telefono" placeholder="10 dígitos" maxlength="10" required>
      </div>
      <div class="fg"><label>Dirección *</label>
        <input class="fc" type="text" name="direccion" placeholder="Dirección completa" required>
      </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar proveedor</button>
  </form>
</div>

<!-- TABLA -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-clipboard-list" style="color:var(--accent3)"></i> Lista de Proveedores</div>
  </div>
  <table>
    <thead>
      <tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Teléfono</th><th>Dirección</th><th>Eliminar</th></tr>
    </thead>
    <tbody>
    <?php
    $num = 0;
    while($row = $proveedores->fetch_assoc()):
        $num++;
    ?>
    <tr>
      <td><?php echo $row['Id_proovedor']; ?></td>
      <td><?php echo htmlspecialchars($row['Nombre']); ?></td>
      <td><?php echo htmlspecialchars($row['Ap_paterno']); ?></td>
      <td><?php echo htmlspecialchars($row['Telefono']); ?></td>
      <td><?php echo htmlspecialchars($row['Direccion']); ?></td>
      <td>
        <a href="index.php?del=<?php echo $row['Id_proovedor']; ?>" class="ib ib-del"
           onclick="return confirm('¿Eliminar a <?php echo addslashes($row['Nombre']); ?>?')">
          <i class="fas fa-times"></i>
        </a>
      </td>
    </tr>
    <?php endwhile; ?>
    <?php if($num===0): ?>
    <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:24px">No hay proveedores registrados.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include("../Cliente/layout_footer.php"); ?>