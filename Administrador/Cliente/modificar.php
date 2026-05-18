<?php
require("conexiondos.php");

$id = $_GET['id'];
$consulta  = "SELECT * FROM cliente WHERE Id_cliente=$id";
$resultado = $mysqli->query($consulta);
$row       = $resultado->fetch_assoc();

$page_id    = 'clientes';
$page_title = 'Modificar Cliente';
include("layout_header.php");
?>

<div class="ptitle">Modificar Cliente</div>
<div class="psub">Editando ID #<?php echo $id; ?></div>

<div class="fcard" style="max-width:700px">
  <form action="actualizar.php" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="fgrid">
      <div class="fg"><label>Nombre</label>
        <input class="fc" type="text" name="nombre" value="<?php echo htmlspecialchars($row['Nombre']); ?>"></div>
      <div class="fg"><label>Ap. Paterno</label>
        <input class="fc" type="text" name="ap_paterno" value="<?php echo htmlspecialchars($row['Ap_paterno']); ?>"></div>
      <div class="fg"><label>Teléfono</label>
        <input class="fc" type="text" name="telefono" value="<?php echo htmlspecialchars($row['Telefono']); ?>"></div>
      <div class="fg"><label>Nueva Contraseña <span style="color:var(--muted);font-size:11px">(vacío = no cambiar)</span></label>
        <input class="fc" type="password" name="contrasena" placeholder="Dejar vacío para no cambiar"></div>
      <div class="fg"><label>Dirección</label>
        <input class="fc" type="text" name="direccion" value="<?php echo htmlspecialchars($row['Direccion']); ?>"></div>
      <div class="fg"><label>Código Postal</label>
        <input class="fc" type="text" name="cp" value="<?php echo htmlspecialchars($row['CP']); ?>"></div>
    </div>
    <div style="display:flex;gap:10px;margin-top:4px">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
      <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Cancelar</a>
    </div>
  </form>
</div>

<?php include("layout_footer.php"); ?>
