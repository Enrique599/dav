<?php
require("../Cliente/conexiondos.php");
date_default_timezone_set('America/Mexico_City');

// Insertar pedido a proveedor
if (isset($_POST['id_proovedor'], $_POST['fecha'], $_POST['total'], $_POST['total_piezas'])) {
    $id_prov      = (int)$_POST['id_proovedor'];
    $fecha        = $_POST['fecha'];
    $total        = (float)$_POST['total'];
    $total_piezas = (int)$_POST['total_piezas'];
    $estado       = $_POST['estado'] ?? 'PENDIENTE';

    // Reutilizamos la tabla pedido asociando al proveedor — guardamos como Id_cliente=0 placeholder
    // o mejor: insertamos en una tabla pedido_proveedor ficticia usando registroproductos
    // Como no hay tabla dedicada, usaremos un comentario en 'Realizacion' de venta con prefijo PROV:
    // Guardamos en tabla venta con Id_cliente=1 (admin/sistema) y campo Realizacion = "PROVEEDOR:{id_prov}:{estado}"
    $realizacion = "PROV:{$id_prov}:{$estado}";
    $stmt = $mysqli->prepare("INSERT INTO venta (Fecha, Realizacion, Total, Id_cliente, Total_piezas) VALUES (?,?,?,1,?)");
    $stmt->bind_param("ssdi", $fecha, $realizacion, $total, $total_piezas);
    $stmt->execute();
    $guardado = true;
}

// Eliminar
if (isset($_GET['del'])) {
    $mysqli->query("DELETE FROM venta WHERE Id_venta=".(int)$_GET['del']." AND Realizacion LIKE 'PROV:%'");
    header("Location: index.php"); exit;
}

// Leer pedidos a proveedores (los que tienen Realizacion LIKE 'PROV:%')
$pedidos = $mysqli->query("
    SELECT v.Id_venta, v.Fecha, v.Total, v.Total_piezas, v.Realizacion,
           p.Nombre, p.Ap_paterno, p.Telefono, p.Direccion
    FROM venta v
    LEFT JOIN proovedor p ON p.Id_proovedor = SUBSTRING_INDEX(SUBSTRING_INDEX(v.Realizacion,':',2),':',-1) + 0
    WHERE v.Realizacion LIKE 'PROV:%'
    ORDER BY v.Fecha DESC
");

$proveedores = $mysqli->query("SELECT Id_proovedor, Nombre, Ap_paterno FROM proovedor ORDER BY Nombre");

$page_id    = 'ped_proveedores';
$page_title = 'Pedidos a Proveedores';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Pedidos a Proveedores</div>
<div class="psub">Registro de pedidos realizados a proveedores</div>

<?php if(isset($guardado)): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> Pedido a proveedor registrado.</div>
<?php endif; ?>

<!-- FORMULARIO -->
<div class="fcard" style="margin-bottom:20px">
  <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:14px">
    <i class="fas fa-truck-loading" style="color:var(--accent2)"></i> Registrar pedido a proveedor
  </div>
  <form action="index.php" method="post">
    <div class="fgrid">
      <div class="fg"><label>Proveedor *</label>
        <select class="fc" name="id_proovedor" required>
          <option value="">— Selecciona proveedor —</option>
          <?php
          $proveedores->data_seek(0);
          while($p = $proveedores->fetch_assoc()): ?>
          <option value="<?php echo $p['Id_proovedor']; ?>"><?php echo $p['Id_proovedor'].' - '.$p['Nombre'].' '.$p['Ap_paterno']; ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="fg"><label>Fecha *</label>
        <input class="fc" type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
      </div>
      <div class="fg"><label>Total ($) *</label>
        <input class="fc" type="number" step="0.01" name="total" placeholder="0.00" required>
      </div>
      <div class="fg"><label>Total piezas *</label>
        <input class="fc" type="number" name="total_piezas" placeholder="0" required>
      </div>
      <div class="fg"><label>Estado</label>
        <select class="fc" name="estado">
          <option value="PENDIENTE">Pendiente</option>
          <option value="ENVIADO">Enviado</option>
          <option value="RECIBIDO">Recibido</option>
          <option value="CANCELADO">Cancelado</option>
        </select>
      </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar pedido</button>
  </form>
</div>

<!-- TABLA -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-truck-loading" style="color:var(--accent2)"></i> Pedidos realizados</div>
  </div>
  <table>
    <thead>
      <tr><th>ID</th><th>Proveedor</th><th>Teléfono</th><th>Fecha</th><th>Total</th><th>Piezas</th><th>Estado</th><th>Eliminar</th></tr>
    </thead>
    <tbody>
    <?php
    $num = 0;
    while($row = $pedidos->fetch_assoc()):
        $num++;
        // Parsear estado del campo Realizacion: PROV:{id}:{estado}
        $partes = explode(':', $row['Realizacion']);
        $estado = $partes[2] ?? 'PENDIENTE';
        $colores = ['PENDIENTE'=>'#f7c948','ENVIADO'=>'#4f8ef7','RECIBIDO'=>'#22d3a5','CANCELADO'=>'#f75e5e'];
        $c = $colores[$estado] ?? '#7a8099';
    ?>
    <tr>
      <td>#<?php echo $row['Id_venta']; ?></td>
      <td><?php echo $row['Nombre'] ? htmlspecialchars($row['Nombre'].' '.$row['Ap_paterno']) : '<em style="color:var(--muted)">Sin asignar</em>'; ?></td>
      <td><?php echo htmlspecialchars($row['Telefono'] ?? '—'); ?></td>
      <td><?php echo $row['Fecha']; ?></td>
      <td>$<?php echo number_format($row['Total'],2); ?></td>
      <td><?php echo $row['Total_piezas']; ?></td>
      <td><span style="background:<?php echo $c; ?>22;color:<?php echo $c; ?>;padding:3px 12px;border-radius:20px;font-size:11px;font-weight:800"><?php echo $estado; ?></span></td>
      <td>
        <a href="index.php?del=<?php echo $row['Id_venta']; ?>" class="ib ib-del" onclick="return confirm('¿Eliminar este pedido?')">
          <i class="fas fa-times"></i>
        </a>
      </td>
    </tr>
    <?php endwhile; ?>
    <?php if($num===0): ?>
    <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:24px">No hay pedidos a proveedores registrados.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include("../Cliente/layout_footer.php"); ?>