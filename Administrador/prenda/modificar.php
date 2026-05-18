<?php
require("conexiondos.php");

$id        = $_GET['id'];
$consulta  = "SELECT * FROM prendas WHERE Id_prendas=$id";
$resultado = $mysqli->query($consulta);
$row       = $resultado->fetch_assoc();

$page_id    = 'prendas';
$page_title = 'Modificar Prenda';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Modificar Prenda</div>
<div class="psub">Editando ID #<?php echo $id; ?></div>

<div class="fcard" style="max-width:500px">
  <form action="actualizar.php" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="fg" style="margin-bottom:13px"><label>Prenda</label>
      <input class="fc" type="text" name="prendas" value="<?php echo htmlspecialchars($row['Prendas']); ?>"></div>
    <div class="fg" style="margin-bottom:16px"><label>Núm. Piezas</label>
      <input class="fc" type="number" name="num_piezas" value="<?php echo $row['Num_piezas']; ?>" min="0"></div>
    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
      <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Cancelar</a>
    </div>
  </form>
</div>

<?php include("../Cliente/layout_footer.php"); ?>
