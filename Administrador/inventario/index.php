<?php
require("../Cliente/conexiondos.php");
date_default_timezone_set('America/Mexico_City');

$hora = (int)date('H');
if($hora >= 6 && $hora < 12)       $saludo = "☀️ ¡Buenos días!";
elseif($hora >= 12 && $hora < 19)  $saludo = "🌤️ ¡Buenas tardes!";
else                                $saludo = "🌙 ¡Buenas noches!";

// Ajuste manual de stock
if (isset($_POST['id_producto'], $_POST['nuevo_stock'])) {
    $id = (int)$_POST['id_producto'];
    $st = (int)$_POST['nuevo_stock'];
    $mysqli->query("UPDATE registroproductos SET Stock=$st WHERE Id_producto=$id");
    $actualizado = true;
}

$inventario = $mysqli->query("
    SELECT r.Id_producto, r.Nombre, r.Stock, r.Precioneto, c.Nombre as categoria
    FROM registroproductos r
    LEFT JOIN categoria c ON r.Id_categoria = c.Id_categoria
    ORDER BY r.Stock ASC
");

$resumen = $mysqli->query("SELECT COUNT(*) as total, SUM(Stock) as stock_total, SUM(Stock * Precioneto) as valor FROM registroproductos")->fetch_assoc();
$bajo_stock = $mysqli->query("SELECT COUNT(*) as t FROM registroproductos WHERE Stock < 10")->fetch_assoc();

$page_id    = 'inventario';
$page_title = 'Inventario';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Inventario</div>
<div class="psub"><?php echo $saludo; ?> — Visualización del inventario actual</div>

<?php if(isset($actualizado)): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> Stock actualizado.</div>
<?php endif; ?>

<!-- Tarjetas resumen -->
<div class="stat-grid" style="margin-bottom:20px">
  <div class="sc sc-teal">
    <div><div class="sc-val"><?php echo $resumen['total']; ?></div><div class="sc-lbl">Productos distintos</div></div>
    <i class="fas fa-boxes sc-icon"></i>
  </div>
  <div class="sc sc-green">
    <div><div class="sc-val"><?php echo (int)$resumen['stock_total']; ?></div><div class="sc-lbl">Unidades totales</div></div>
    <i class="fas fa-warehouse sc-icon"></i>
  </div>
  <div class="sc sc-amber">
    <div><div class="sc-val">$<?php echo number_format($resumen['valor'],2); ?></div><div class="sc-lbl">Valor del inventario</div></div>
    <i class="fas fa-dollar-sign sc-icon"></i>
  </div>
  <div class="sc sc-red">
    <div><div class="sc-val"><?php echo $bajo_stock['t']; ?></div><div class="sc-lbl">Stock bajo (&lt;10)</div></div>
    <i class="fas fa-exclamation-triangle sc-icon"></i>
  </div>
</div>

<!-- TABLA INVENTARIO -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-warehouse" style="color:var(--accent3)"></i> Estado del inventario</div>
    <small style="color:var(--muted)">Ordenado: menor stock primero</small>
  </div>
  <table>
    <thead>
      <tr><th>ID</th><th>Producto</th><th>Categoría</th><th>Stock actual</th><th>Nivel</th><th>Precio venta</th><th>Valor stock</th><th>Ajustar</th></tr>
    </thead>
    <tbody>
    <?php
    $num = 0;
    while($row = $inventario->fetch_assoc()):
        $num++;
        if ($row['Stock'] <= 0) { $nivel_c = '#ef4444'; $nivel_t = '🔴 Sin stock'; }
        elseif ($row['Stock'] < 5) { $nivel_c = '#f75e5e'; $nivel_t = '🔴 Crítico'; }
        elseif ($row['Stock'] < 10) { $nivel_c = '#f97316'; $nivel_t = '🟠 Bajo'; }
        elseif ($row['Stock'] < 30) { $nivel_c = '#f7c948'; $nivel_t = '🟡 Medio'; }
        else { $nivel_c = '#22c55e'; $nivel_t = '🟢 Bueno'; }
        $valor_stock = $row['Stock'] * $row['Precioneto'];
    ?>
    <tr>
      <td><?php echo $row['Id_producto']; ?></td>
      <td><strong><?php echo htmlspecialchars($row['Nombre']); ?></strong></td>
      <td><?php echo htmlspecialchars($row['categoria'] ?? '—'); ?></td>
      <td>
        <span style="font-size:18px;font-weight:800;color:<?php echo $nivel_c; ?>"><?php echo $row['Stock']; ?></span>
      </td>
      <td>
        <span style="background:<?php echo $nivel_c; ?>22;color:<?php echo $nivel_c; ?>;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">
          <?php echo $nivel_t; ?>
        </span>
      </td>
      <td>$<?php echo number_format($row['Precioneto'],2); ?></td>
      <td>$<?php echo number_format($valor_stock,2); ?></td>
      <td>
        <form action="index.php" method="post" style="display:flex;gap:6px;align-items:center">
          <input type="hidden" name="id_producto" value="<?php echo $row['Id_producto']; ?>">
          <input class="fc" type="number" name="nuevo_stock" value="<?php echo $row['Stock']; ?>"
                 min="0" style="width:70px;padding:5px 8px;font-size:13px">
          <button type="submit" class="btn btn-sm btn-primary" title="Guardar stock">
            <i class="fas fa-save"></i>
          </button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
    <?php if($num===0): ?>
    <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:24px">No hay productos en inventario. Agrega productos primero.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include("../Cliente/layout_footer.php"); ?>