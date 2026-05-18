<?php
require("conexiondos.php");
date_default_timezone_set('America/Mexico_City');

$hora = (int)date('H');
if($hora >= 6 && $hora < 12)       $saludo = "☀️ ¡Buenos días!";
elseif($hora >= 12 && $hora < 19)  $saludo = "🌤️ ¡Buenas tardes!";
else                                $saludo = "🌙 ¡Buenas noches!";

if(isset($_POST["prendas"], $_POST["num_piezas"])){
    $prendas    = $_POST["prendas"];
    $num_piezas = $_POST["num_piezas"];
    $consulta   = "INSERT INTO prendas(Prendas, Num_piezas) VALUES ('$prendas', '$num_piezas')";
    $mysqli->query($consulta);
    $guardado   = true;
}

$sql       = 'SELECT * FROM prendas';
$impresion = $mysqli->query($sql);

$page_id    = 'prendas';
$page_title = 'Registro de Prendas';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Registro de Prendas</div>
<div class="psub"><?php echo $saludo; ?></div>

<?php if(isset($guardado)): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> Prenda guardada correctamente.</div>
<?php endif; ?>

<!-- FORMULARIO -->
<div class="fcard">
  <form action="index.php" method="post">
    <div class="fgrid" style="grid-template-columns:1fr 1fr auto">
      <div class="fg"><label>Nombre de la Prenda *</label>
        <input class="fc" type="text" name="prendas" placeholder="Nombre de la prenda" required></div>
      <div class="fg"><label>Número de Piezas *</label>
        <input class="fc" type="number" name="num_piezas" placeholder="0" min="0" required></div>
      <div class="fg" style="justify-content:flex-end">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary" style="height:42px"><i class="fas fa-save"></i> Guardar</button>
      </div>
    </div>
  </form>
</div>

<!-- TABLA -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-tshirt" style="color:var(--accent3)"></i> Lista de Prendas</div>
  </div>
  <table>
    <thead>
      <tr>
        <th>ID</th><th>Prenda</th><th>Núm. Piezas</th><th>Eliminar</th><th>Modificar</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $impresion->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['Id_prendas']; ?></td>
        <td><?php echo $row['Prendas']; ?></td>
        <td><?php echo $row['Num_piezas']; ?></td>
        <td>
          <a href="eliminar.php?id=<?php echo $row['Id_prendas']; ?>"
             class="ib ib-del"
             onclick="return confirm('¿Eliminar esta prenda?')">
            <i class="fas fa-times"></i>
          </a>
        </td>
        <td>
          <a href="modificar.php?id=<?php echo $row['Id_prendas']; ?>" class="ib ib-edit">
            <i class="fas fa-pencil-alt"></i>
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include("../Cliente/layout_footer.php"); ?>
