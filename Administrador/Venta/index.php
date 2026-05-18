<?php
require("conexiondos.php");

if(isset($_POST["fecha"], $_POST["realizacion"], $_POST["total"], $_POST["id_cliente"], $_POST["total_piezas"])){
    $fecha        = $_POST["fecha"];
    $realizacion  = $_POST["realizacion"];
    $total        = $_POST["total"];
    $id_cliente   = $_POST["id_cliente"];
    $total_piezas = $_POST["total_piezas"];

    $consulta = "INSERT INTO venta(Fecha, Realizacion, Total, Id_cliente, Total_piezas)
                 VALUES ('$fecha', '$realizacion', '$total', '$id_cliente', '$total_piezas')";
    $mysqli->query($consulta);
    $guardado = true;
}

$sql = 'SELECT v.*, c.Nombre, c.Ap_paterno
        FROM venta v
        INNER JOIN cliente c ON v.Id_cliente = c.Id_cliente';
$impresion = $mysqli->query($sql);
$clientes  = $mysqli->query("SELECT Id_cliente, Nombre, Ap_paterno FROM cliente");

$page_id    = 'ventas';
$page_title = 'Registro de Ventas';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Registro de Ventas</div>
<div class="psub">Registra y consulta las ventas realizadas</div>

<?php if(isset($guardado)): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> Venta registrada correctamente.</div>
<?php endif; ?>

<!-- FORMULARIO -->
<div class="fcard">
  <form action="index.php" method="post">
    <div class="fgrid">
      <div class="fg"><label>Fecha *</label>
        <input class="fc" type="date" name="fecha" required></div>
      <div class="fg"><label>Realización *</label>
        <input class="fc" type="text" name="realizacion" placeholder="Descripción" required></div>
      <div class="fg"><label>Total ($) *</label>
        <input class="fc" type="number" name="total" step="0.01" placeholder="0.00" required></div>
      <div class="fg"><label>Cliente *</label>
        <select class="fc" name="id_cliente" required>
          <option value="">-- Selecciona Cliente --</option>
          <?php while($c = $clientes->fetch_assoc()): ?>
          <option value="<?php echo $c['Id_cliente']; ?>">
            <?php echo $c['Id_cliente'].' - '.$c['Nombre'].' '.$c['Ap_paterno']; ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="fg"><label>Total Piezas *</label>
        <input class="fc" type="number" name="total_piezas" placeholder="0" required></div>
    </div>
    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Venta</button>
  </form>
</div>

<!-- TABLA -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-list" style="color:var(--green)"></i> Lista de Ventas</div>
  </div>
  <table>
    <thead>
      <tr>
        <th>ID Venta</th><th>Fecha</th><th>Realización</th>
        <th>Total</th><th>Cliente</th><th>Total Piezas</th>
        <th>Eliminar</th><th>Modificar</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $impresion->fetch_assoc()): ?>
      <tr>
        <td>#<?php echo $row['Id_venta']; ?></td>
        <td><?php echo $row['Fecha']; ?></td>
        <td><?php echo $row['Realizacion']; ?></td>
        <td>$<?php echo number_format($row['Total'], 2); ?></td>
        <td><?php echo $row['Nombre'].' '.$row['Ap_paterno']; ?></td>
        <td><?php echo $row['Total_piezas']; ?></td>
        <td>
          <a href="eliminar.php?id=<?php echo $row['Id_venta']; ?>"
             class="ib ib-del"
             onclick="return confirm('¿Eliminar esta venta?')">
            <i class="fas fa-times"></i>
          </a>
        </td>
        <td>
          <a href="modificar.php?id=<?php echo $row['Id_venta']; ?>" class="ib ib-edit">
            <i class="fas fa-pencil-alt"></i>
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include("../Cliente/layout_footer.php"); ?>
