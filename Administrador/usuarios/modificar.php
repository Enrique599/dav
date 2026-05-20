<?php
require("conexiondos.php");

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Consulta usando la llave primaria e información real de tus empleados
$consulta  = "SELECT * FROM empleado WHERE Id_empleado = $id";
$resultado = $mysqli->query($consulta);
$row       = $resultado->fetch_assoc();

if (!$row) {
    echo "El usuario/empleado no existe.";
    exit();
}

$page_id    = 'usuarios';
$page_title = 'Modificar Empleado';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Modificar Usuario</div>
<div class="psub">Editando el perfil del empleado ID #<?php echo $id; ?></div>

<div class="fcard" style="max-width:700px; margin: 0 auto;">
  <form action="actualizar.php" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    
    <div class="fgrid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
      <div class="fg">
        <label>Nombre(s) *</label>
        <input class="fc" type="text" name="nombre" value="<?php echo htmlspecialchars($row['Nombre']); ?>" required>
      </div>
      
      <div class="fg">
        <label>Apellido Paterno *</label>
        <input class="fc" type="text" name="ap_paterno" value="<?php echo htmlspecialchars($row['Ap_paterno']); ?>" required>
      </div>
      
      <div class="fg">
        <label>Teléfono *</label>
        <input class="fc" type="text" name="telefono" maxlength="10" value="<?php echo htmlspecialchars($row['Telefono']); ?>" required>
      </div>
      
      <div class="fg">
        <label>Dirección *</label>
        <input class="fc" type="text" name="direccion" value="<?php echo htmlspecialchars($row['Direccion']); ?>" required>
      </div>

      <div class="fg">
        <label>Rol de Usuario *</label>
        <select class="fc" name="rol" required>
          <option value="admin" <?php if($row['rol'] == 'admin') echo 'selected'; ?>>Administrador</option>
          <option value="empleado" <?php if($row['rol'] == 'empleado') echo 'selected'; ?>>Empleado</option>
        </select>
      </div>

      <div class="fg">
        <label>Nueva Contraseña (Dejar en blanco para no cambiar)</label>
        <input class="fc" type="password" name="contrasena" placeholder="******">
      </div>
    </div>

    <div style="display:flex; gap:15px; margin-top: 30px;">
      <button type="submit" class="btn btn-success" style="flex:1; padding: 12px;"><i class="fas fa-save"></i> Actualizar Usuario</button>
      <a href="index.php" class="btn" style="background:#555; color:white; padding: 12px 25px; border-radius:6px; text-decoration:none; text-align:center;">Cancelar</a>
    </div>
  </form>
</div>

<?php include("../Cliente/layout_footer.php"); ?>