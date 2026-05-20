<?php
require("../Cliente/conexiondos.php");
date_default_timezone_set('America/Mexico_City');

$hora = (int)date('H');
if ($hora >= 6 && $hora < 12)       $saludo = "☀️ ¡Buenos días!";
elseif ($hora >= 12 && $hora < 19)  $saludo = "🌤️ ¡Buenas tardes!";
else                                $saludo = "🌙 ¡Buenas noches!";

$error   = "";
$guardado = false;

// INSERT empleado
if (isset($_POST['nombre'], $_POST['ap_paterno'], $_POST['telefono'], $_POST['direccion'], $_POST['contrasena'], $_POST['rol'])) {
  $nombre     = trim($_POST['nombre']);
  $ap_paterno = trim($_POST['ap_paterno']);
  $telefono   = trim($_POST['telefono']);
  $direccion  = trim($_POST['direccion']);
  $contrasena = $_POST['contrasena'];
  $rol        = $_POST['rol'];

  if (strlen($contrasena) < 6) {
    $error = "La contraseña debe tener al menos 6 caracteres.";
  } else {
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO empleado (Nombre, Ap_paterno, Telefono, Direccion, Contrasena, rol) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $nombre, $ap_paterno, $telefono, $direccion, $hash, $rol);
    if ($stmt->execute()) {
      $guardado = true;
    } else {
      $error = "Error al guardar. Verifica los datos.";
    }
  }
}

// ELIMINAR
if (isset($_GET['del'])) {
  $mysqli->query("DELETE FROM empleado WHERE Id_empleado=" . (int)$_GET['del']);
  header("Location: index.php");
  exit;
}

$empleados = $mysqli->query("SELECT Id_empleado, Nombre, Ap_paterno, Telefono, Direccion, rol FROM empleado ORDER BY Id_empleado DESC");

$page_id    = 'usuarios';
$page_title = 'Usuarios / Empleados';
include("../Cliente/layout_header.php");
?>

<div class="ptitle">Usuarios / Empleados</div>
<div class="psub"><?php echo $saludo; ?> — Gestiona los accesos al sistema</div>

<?php if ($guardado): ?>
  <div class="alert alert-ok"><i class="fas fa-check-circle"></i> Empleado registrado correctamente.</div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-err"><i class="fas fa-times-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- FORMULARIO -->
<div class="fcard" style="margin-bottom:20px">
  <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:14px">
    <i class="fas fa-user-plus" style="color:var(--accent2)"></i> Registrar nuevo usuario/empleado
  </div>
  <form action="index.php" method="post">
    <div class="fgrid">
      <div class="fg"><label>Nombre *</label>
        <input class="fc" type="text" name="nombre" placeholder="Nombre" required>
      </div>
      <div class="fg"><label>Apellido Paterno *</label>
        <input class="fc" type="text" name="ap_paterno" placeholder="Apellido" required>
      </div>
      <div class="fg"><label>Teléfono *</label>
        <input class="fc" type="text" name="telefono" placeholder="10 dígitos" maxlength="10" required>
      </div>
      <div class="fg"><label>Dirección *</label>
        <input class="fc" type="text" name="direccion" placeholder="Dirección" required>
      </div>
      <div class="fg"><label>Contraseña * (mín. 6 car.)</label>
        <input class="fc" type="password" name="contrasena" placeholder="••••••••" required>
      </div>
      <div class="fg"><label>Rol *</label>
        <select class="fc" name="rol" required>
          <option value="admin">Administrador</option>
          <option value="vendedor">Vendedor</option>
          <option value="almacen">Almacén</option>
          <option value="contador">Contador</option>
        </select>
      </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar empleado</button>
  </form>
</div>

<!-- TABLA -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-user-gear" style="color:var(--accent2)"></i> Empleados registrados</div>
  </div>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Teléfono</th>
        <th>Dirección</th>
        <th>Rol</th>
        <th>Editar</th>
        <th>Eliminar</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $num = 0;
      $roles_color = ['admin' => '#f75e5e', 'vendedor' => '#4f8ef7', 'almacen' => '#f7c948', 'contador' => '#22d3a5'];
      while ($row = $empleados->fetch_assoc()):
        $num++;
        $rc = $roles_color[$row['rol']] ?? '#7a8099';
      ?>
        <tr>
          <td><?php echo $row['Id_empleado']; ?></td>
          <td><?php echo htmlspecialchars($row['Nombre']); ?></td>
          <td><?php echo htmlspecialchars($row['Ap_paterno']); ?></td>
          <td><?php echo htmlspecialchars($row['Telefono']); ?></td>
          <td><?php echo htmlspecialchars($row['Direccion']); ?></td>
          <td>
            <span style="background:<?php echo $rc; ?>22;color:<?php echo $rc; ?>;padding:3px 12px;border-radius:20px;font-size:11px;font-weight:800;text-transform:uppercase">
              <?php echo htmlspecialchars($row['rol']); ?>
            </span>
          </td>
          <td>
            <a href="modificar.php?id=<?php echo $row['Id_empleado']; ?>" class="ib ib-edit">
              <i class="fas fa-pencil-alt"></i>
            </a>
          </td>
          <td>
            <a href="index.php?del=<?php echo $row['Id_empleado']; ?>" class="ib ib-del"
              onclick="return confirm('¿Eliminar a <?php echo addslashes($row['Nombre']); ?>? Esta acción revocará su acceso.')">
              <i class="fas fa-times"></i>
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
      <?php if ($num === 0): ?>
        <tr>
          <td colspan="7" style="text-align:center;color:var(--muted);padding:24px">No hay empleados registrados. Agrega el primero arriba.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include("../Cliente/layout_footer.php"); ?>