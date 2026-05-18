<?php
require("../Cliente/conexiondos.php");
date_default_timezone_set('America/Mexico_City');

$hora = (int)date('H');
if($hora >= 6 && $hora < 12)       $saludo = "☀️ ¡Buenos días!";
elseif($hora >= 12 && $hora < 19)  $saludo = "🌤️ ¡Buenas tardes!";
else                                $saludo = "🌙 ¡Buenas noches!";

// INSERT producto
if (isset($_POST['nombre'], $_POST['stock'], $_POST['id_categoria'], $_POST['preciopro'], $_POST['precioneto'])) {
    $nombre     = trim($_POST['nombre']);
    $stock      = (int)$_POST['stock'];
    $id_cat     = (int)$_POST['id_categoria'];
    $preciopro  = (float)$_POST['preciopro'];
    $precioneto = (float)$_POST['precioneto'];
    $stmt = $mysqli->prepare("INSERT INTO registroproductos (Nombre, Stock, Id_categoria, Preciopro, Precioneto) VALUES (?,?,?,?,?)");
    $stmt->bind_param("siids", $nombre, $stock, $id_cat, $preciopro, $precioneto);
    $stmt->execute();
    $guardado = true;
}

// ELIMINAR
if (isset($_GET['del'])) {
    $mysqli->query("DELETE FROM registroproductos WHERE Id_producto=".(int)$_GET['del']);
    header("Location: index.php"); exit;
}

// Traer datos
$productos   = $mysqli->query("
    SELECT r.*, c.Nombre as cat_nombre
    FROM registroproductos r
    LEFT JOIN categoria c ON r.Id_categoria = c.Id_categoria
    ORDER BY r.Id_producto DESC
");
$categorias  = $mysqli->query("SELECT * FROM categoria ORDER BY Nombre");

$page_id    = 'productos';
$page_title = 'Productos';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Productos</div>
<div class="psub"><?php echo $saludo; ?> — Catálogo y registro de productos</div>

<?php if(isset($guardado)): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> Producto guardado correctamente.</div>
<?php endif; ?>

<!-- Tarjetas resumen -->
<?php
$tot_productos = $mysqli->query("SELECT COUNT(*) as t, SUM(Stock) as s FROM registroproductos")->fetch_assoc();
$tot_bajo_stock = $mysqli->query("SELECT COUNT(*) as t FROM registroproductos WHERE Stock < 10")->fetch_assoc();
?>
<div class="stat-grid" style="margin-bottom:20px">
  <div class="sc sc-teal">
    <div><div class="sc-val"><?php echo $tot_productos['t']; ?></div><div class="sc-lbl">Productos registrados</div></div>
    <i class="fas fa-box-open sc-icon"></i>
  </div>
  <div class="sc sc-amber">
    <div><div class="sc-val"><?php echo (int)$tot_productos['s']; ?></div><div class="sc-lbl">Unidades en stock total</div></div>
    <i class="fas fa-warehouse sc-icon"></i>
  </div>
  <div class="sc sc-red">
    <div><div class="sc-val"><?php echo $tot_bajo_stock['t']; ?></div><div class="sc-lbl">Stock bajo (&lt;10)</div></div>
    <i class="fas fa-exclamation-triangle sc-icon"></i>
  </div>
</div>

<!-- FORMULARIO -->
<div class="fcard" style="margin-bottom:20px">
  <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:14px">
    <i class="fas fa-plus-circle" style="color:var(--accent)"></i> Agregar nuevo producto
  </div>
  <form action="index.php" method="post">
    <div class="fgrid">
      <div class="fg"><label>Nombre del producto *</label>
        <input class="fc" type="text" name="nombre" placeholder="Nombre" required>
      </div>
      <div class="fg"><label>Stock inicial *</label>
        <input class="fc" type="number" name="stock" placeholder="0" min="0" required>
      </div>
      <div class="fg"><label>Categoría *</label>
        <select class="fc" name="id_categoria" required>
          <option value="">— Categoría —</option>
          <?php if($categorias): while($c = $categorias->fetch_assoc()): ?>
          <option value="<?php echo $c['Id_categoria']; ?>"><?php echo htmlspecialchars($c['Nombre']); ?></option>
          <?php endwhile; endif; ?>
          <option value="1">General</option>
        </select>
      </div>
      <div class="fg"><label>Precio proveedor ($) *</label>
        <input class="fc" type="number" step="0.01" name="preciopro" placeholder="0.00" required>
      </div>
      <div class="fg"><label>Precio neto / venta ($) *</label>
        <input class="fc" type="number" step="0.01" name="precioneto" placeholder="0.00" required>
      </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar producto</button>
  </form>
</div>

<!-- TABLA -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-box-open" style="color:var(--accent)"></i> Catálogo de Productos</div>
  </div>
  <table>
    <thead>
      <tr><th>ID</th><th>Nombre</th><th>Categoría</th><th>Stock</th><th>Precio proveedor</th><th>Precio venta</th><th>Margen</th><th>Eliminar</th></tr>
    </thead>
    <tbody>
    <?php
    $num = 0;
    while($row = $productos->fetch_assoc()):
        $num++;
        $margen = $row['Precioneto'] - $row['Preciopro'];
        $margen_pct = $row['Preciopro'] > 0 ? round(($margen / $row['Preciopro']) * 100, 1) : 0;
        $stock_color = $row['Stock'] < 5 ? '#ef4444' : ($row['Stock'] < 10 ? '#f97316' : '#22c55e');
    ?>
    <tr>
      <td><?php echo $row['Id_producto']; ?></td>
      <td><strong><?php echo htmlspecialchars($row['Nombre']); ?></strong></td>
      <td><?php echo htmlspecialchars($row['cat_nombre'] ?? '—'); ?></td>
      <td>
        <span style="background:<?php echo $stock_color; ?>22;color:<?php echo $stock_color; ?>;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">
          <?php echo $row['Stock']; ?> uds
        </span>
      </td>
      <td>$<?php echo number_format($row['Preciopro'],2); ?></td>
      <td>$<?php echo number_format($row['Precioneto'],2); ?></td>
      <td>
        <span style="color:<?php echo $margen >= 0 ? '#22c55e' : '#ef4444'; ?>;font-weight:700">
          <?php echo $margen >= 0 ? '+' : ''; ?>$<?php echo number_format($margen,2); ?>
          <small style="opacity:.7">(<?php echo $margen_pct; ?>%)</small>
        </span>
      </td>
      <td>
        <a href="index.php?del=<?php echo $row['Id_producto']; ?>" class="ib ib-del"
           onclick="return confirm('¿Eliminar el producto <?php echo addslashes($row['Nombre']); ?>?')">
          <i class="fas fa-times"></i>
        </a>
      </td>
    </tr>
    <?php endwhile; ?>
    <?php if($num===0): ?>
    <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:24px">No hay productos registrados.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include("../Cliente/layout_footer.php"); ?>