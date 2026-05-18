<?php
require("conexiondos.php");

$id        = $_GET['id'];
$consulta  = "SELECT * FROM venta WHERE Id_venta=$id";
$resultado = $mysqli->query($consulta);
$row       = $resultado->fetch_assoc();
$clientes  = $mysqli->query("SELECT Id_cliente, Nombre, Ap_paterno FROM cliente");

$page_id    = 'ventas';
$page_title = 'Modificar Venta';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Modificar Venta</div>
<div class="psub">Editando venta ID #<?php echo $id; ?></div>

<div class="fcard" style="max-width:700px">
  <form action="actualizar.php" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="fgrid">
      <div class="fg"><label>Fecha</label>
        <input class="fc" type="date" name="fecha" value="<?php echo $row['Fecha']; ?>"></div>
      <div class="fg"><label>Realización</label>
        <input class="fc" type="text" name="realizacion" value="<?php echo htmlspecialchars($row['Realizacion']); ?>"></div>
      <div class="fg"><label>Total ($)</label>
        <input class="fc" type="number" step="0.01" name="total" value="<?php echo $row['Total']; ?>"></div>
      <div class="fg"><label>Cliente</label>
        <select class="fc" name="id_cliente">
          <?php while($c = $clientes->fetch_assoc()): ?>
          <option value="<?php echo $c['Id_cliente']; ?>" <?php if($c['Id_cliente']==$row['Id_cliente']) echo 'selected'; ?>>
            <?php echo $c['Nombre'].' '.$c['Ap_paterno']; ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="fg"><label>Total Piezas</label>
        <input class="fc" type="number" name="total_piezas" value="<?php echo $row['Total_piezas']; ?>"></div>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
      <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Cancelar</a>
    </div>
  </form>
</div>

<?php include("../Cliente/layout_footer.php"); ?>
