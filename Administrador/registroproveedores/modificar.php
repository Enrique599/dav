<?php
require("../Cliente/conexiondos.php");

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Consulta usando el nombre exacto de tu tabla y su llave primaria
$consulta  = "SELECT * FROM proovedor WHERE Id_proovedor = $id";
$resultado = $mysqli->query($consulta);
$row       = $resultado->fetch_assoc();

if (!$row) {
    echo "El proveedor no existe.";
    exit();
}

$page_id    = 'proveedores';
$page_title = 'Modificar Proveedor';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Modificar Proveedor</div>
<div class="psub">Editando la información del proveedor ID #<?php echo $id; ?></div>

<div class="fcard" style="max-width:700px; margin: 0 auto;">
  <form action="actualizar.php" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    
    <div class="fgrid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
      <div class="fg">
        <label>Nombre de la Empresa / Proveedor *</label>
        <input class="fc" type="text" name="nombre" value="<?php echo htmlspecialchars($row['Nombre']); ?>" required>
      </div>
      
      <div class="fg">
        <label>Apellido Paterno (Contacto) *</label>
        <input class="fc" type="text" name="ap_paterno" value="<?php echo htmlspecialchars($row['Ap_paterno']); ?>" required>
      </div>
      
      <div class="fg">
        <label>Teléfono *</label>
        <input class="fc" type="text" name="telefono" maxlength="10" value="<?php echo htmlspecialchars($row['Telefono']); ?>" required>
      </div>
      
      <div class="fg">
        <label>Dirección *</label>
        <input class="fc" type="text" name="direccion" value="<?php echo htmlspecialchars($row['Direccion']); ?>" required>
      </div>
    </div>

    <div style="display:flex; gap:15px; margin-top: 30px;">
      <button type="submit" class="btn btn-success" style="flex:1; padding: 12px; background:var(--accent3); border:none;"><i class="fas fa-save"></i> Actualizar Proveedor</button>
      <a href="index.php" class="btn" style="background:#555; color:white; padding: 12px 25px; border-radius:6px; text-decoration:none; text-align:center;">Cancelar</a>
    </div>
  </form>
</div>

<?php include("../Cliente/layout_footer.php"); ?>