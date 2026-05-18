<?php
require("../Cliente/conexiondos.php");
date_default_timezone_set('America/Mexico_City');

// Insertar nuevo pedido
if (isset($_POST['id_cliente'], $_POST['fecha'], $_POST['total'], $_POST['total_piezas'], $_POST['prioridad'])) {
    $id_cliente   = (int)$_POST['id_cliente'];
    $fecha        = $_POST['fecha'];
    $total        = (float)$_POST['total'];
    $total_piezas = (int)$_POST['total_piezas'];
    $prioridad    = $_POST['prioridad']; // URGENTE / ALTA / NORMAL
    $stmt = $mysqli->prepare("INSERT INTO pedido (Id_cliente, Fecha, Total, Totalpiezas) VALUES (?,?,?,?)");
    $stmt->bind_param("isdi", $id_cliente, $fecha, $total, $total_piezas);
    $stmt->execute();
    $guardado = true;
}

// Eliminar
if (isset($_GET['del'])) {
    $mysqli->query("DELETE FROM pedido WHERE Id_pedido=" . (int)$_GET['del']);
    header("Location: index.php");
    exit;
}

// Traer pedidos con datos de cliente, ordenados por fecha ASC (más antiguos = más urgentes)
$pedidos = $mysqli->query("
    SELECT p.Id_pedido, p.Fecha, p.Total, p.Totalpiezas,
           c.Nombre, c.Ap_paterno, c.Telefono,
           DATEDIFF(CURDATE(), p.Fecha) as dias_espera
    FROM pedido p
    INNER JOIN cliente c ON p.Id_cliente = c.Id_cliente
    ORDER BY p.Fecha ASC
");

$clientes = $mysqli->query("SELECT Id_cliente, Nombre, Ap_paterno FROM cliente ORDER BY Nombre");

$page_id    = 'pedidos_clientes';
$page_title = 'Pedidos de Clientes';
include("../Cliente/layout_header.php");
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:10px">
  <div>
    <div class="ptitle">Pedidos de Clientes</div>
    <div class="psub">Ordenados por prioridad — alertas automáticas por tiempo de espera</div>
  </div>
</div>

<?php if(isset($guardado)): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> Pedido registrado correctamente.</div>
<?php endif; ?>

<!-- Leyenda de prioridades -->
<div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
  <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted)">
    <span style="width:12px;height:12px;border-radius:50%;background:#ef4444;display:inline-block"></span> Urgente (+7 días)
  </div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted)">
    <span style="width:12px;height:12px;border-radius:50%;background:#f97316;display:inline-block"></span> Alta prioridad (3–6 días)
  </div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted)">
    <span style="width:12px;height:12px;border-radius:50%;background:#22c55e;display:inline-block"></span> Normal (0–2 días)
  </div>
</div>

<!-- FORMULARIO AGREGAR -->
<div class="fcard" style="margin-bottom:20px">
  <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:14px"><i class="fas fa-plus-circle" style="color:var(--accent)"></i> Registrar nuevo pedido</div>
  <form action="index.php" method="post">
    <div class="fgrid">
      <div class="fg"><label>Cliente *</label>
        <select class="fc" name="id_cliente" required>
          <option value="">— Selecciona cliente —</option>
          <?php while($c = $clientes->fetch_assoc()): ?>
          <option value="<?php echo $c['Id_cliente']; ?>"><?php echo $c['Id_cliente'].' - '.$c['Nombre'].' '.$c['Ap_paterno']; ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="fg"><label>Fecha del pedido *</label>
        <input class="fc" type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
      </div>
      <div class="fg"><label>Total ($) *</label>
        <input class="fc" type="number" step="0.01" name="total" placeholder="0.00" required>
      </div>
      <div class="fg"><label>Total piezas *</label>
        <input class="fc" type="number" name="total_piezas" placeholder="0" required>
      </div>
      <div class="fg"><label>Prioridad</label>
        <select class="fc" name="prioridad">
          <option value="NORMAL">Normal</option>
          <option value="ALTA">Alta</option>
          <option value="URGENTE">Urgente</option>
        </select>
      </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar pedido</button>
  </form>
</div>

<!-- TABLA DE PEDIDOS -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-box" style="color:var(--accent)"></i> Lista de Pedidos</div>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th><th>Cliente</th><th>Teléfono</th>
        <th>Fecha</th><th>Días espera</th><th>Total</th><th>Piezas</th>
        <th>Prioridad</th><th>Acción</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $num = 0;
    while($row = $pedidos->fetch_assoc()):
        $dias = (int)$row['dias_espera'];
        if ($dias >= 7) { $color = '#ef4444'; $nivel = 'URGENTE'; $icon = '🚨'; }
        elseif ($dias >= 3) { $color = '#f97316'; $nivel = 'ALTA'; $icon = '⚠️'; }
        else { $color = '#22c55e'; $nivel = 'NORMAL'; $icon = '✅'; }
        $num++;
    ?>
    <tr style="border-left:3px solid <?php echo $color; ?>">
      <td><?php echo $row['Id_pedido']; ?></td>
      <td><strong><?php echo htmlspecialchars($row['Nombre'].' '.$row['Ap_paterno']); ?></strong></td>
      <td><?php echo htmlspecialchars($row['Telefono']); ?></td>
      <td><?php echo $row['Fecha']; ?></td>
      <td>
        <span style="background:<?php echo $color; ?>22;color:<?php echo $color; ?>;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">
          <?php echo $dias; ?> día<?php echo $dias != 1 ? 's' : ''; ?>
        </span>
      </td>
      <td>$<?php echo number_format($row['Total'], 2); ?></td>
      <td><?php echo $row['Totalpiezas']; ?></td>
      <td>
        <span style="background:<?php echo $color; ?>22;color:<?php echo $color; ?>;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.5px">
          <?php echo $icon; ?> <?php echo $nivel; ?>
        </span>
      </td>
      <td>
        <a href="index.php?del=<?php echo $row['Id_pedido']; ?>"
           class="ib ib-del"
           onclick="return confirm('¿Eliminar pedido #<?php echo $row['Id_pedido']; ?>?')">
          <i class="fas fa-times"></i>
        </a>
      </td>
    </tr>
    <?php endwhile; ?>
    <?php if($num === 0): ?>
    <tr><td colspan="9" style="text-align:center;color:var(--muted);padding:24px">No hay pedidos registrados.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include("../Cliente/layout_footer.php"); ?>