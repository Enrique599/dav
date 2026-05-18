<?php
require("conexiondos.php");
date_default_timezone_set('America/Mexico_City');

$mensaje = "";
$error   = "";

if(isset($_FILES["archivo_sql"])){
    $archivo   = $_FILES["archivo_sql"];
    $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);

    if($extension !== "sql"){
        $error = "Solo se permiten archivos .sql";
    } elseif($archivo["error"] !== 0){
        $error = "Error al subir el archivo.";
    } else {
        $sql      = file_get_contents($archivo["tmp_name"]);
        $consultas = explode(";", $sql);
        $exitosas = 0; $fallidas = 0;
        foreach($consultas as $consulta){
            $consulta = trim($consulta);
            if(!empty($consulta)){
                if($mysqli->query($consulta)) $exitosas++;
                else $fallidas++;
            }
        }
        $mensaje = "Restauración completada — $exitosas consultas exitosas, $fallidas fallidas.";
    }
}

$page_id    = 'restaurar';
$page_title = 'Restaurar Base de Datos';
include("layout_header.php");
?>

<div class="ptitle">Restaurar Base de Datos</div>
<div class="psub">Importa un archivo <code>.sql</code> para restaurar los datos</div>

<?php if($mensaje): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> <?php echo $mensaje; ?></div>
<?php endif; ?>
<?php if($error): ?>
<div class="alert alert-err"><i class="fas fa-times-circle"></i> <?php echo $error; ?></div>
<?php endif; ?>

<div class="alert alert-warn" style="margin-bottom:18px">
  <i class="fas fa-exclamation-triangle"></i>
  <strong>Advertencia:</strong> Esta acción sobreescribirá la base de datos actual. No se puede deshacer.
</div>

<div class="fcard" style="max-width:500px">
  <form action="restaurar.php" method="post" enctype="multipart/form-data">
    <div class="fg" style="margin-bottom:16px">
      <label>Selecciona un archivo .sql</label>
      <input class="fc" type="file" name="archivo_sql" accept=".sql" required>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-orange"><i class="fas fa-upload"></i> Restaurar</button>
      <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Cancelar</a>
    </div>
  </form>
</div>

<?php include("layout_footer.php"); ?>
