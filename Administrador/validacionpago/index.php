<?php
require("../Cliente/conexiondos.php");
date_default_timezone_set('America/Mexico_City');

$hora = (int)date('H');
if($hora >= 6 && $hora < 12)       $saludo = "☀️ ¡Buenos días!";
elseif($hora >= 12 && $hora < 19)  $saludo = "🌤️ ¡Buenas tardes!";
else                                $saludo = "🌙 ¡Buenas noches!";

// Validar / cambiar estado de un pago
if (isset($_POST['id_venta'], $_POST['estado_pago'])) {
    $id_venta   = (int)$_POST['id_venta'];
    $estado     = $_POST['estado_pago'];
    $metodo     = $_POST['metodo_pago'] ?? 'EFECTIVO';
    $notas      = trim($_POST['notas'] ?? '');
    // Guardamos en Realizacion añadiendo tag de pago
    $realizacion = "PAGO:{$estado}:{$metodo}:" . substr($notas, 0, 30);
    $mysqli->query("UPDATE venta SET Realizacion='" . $mysqli->real_escape_string($realizacion) . "' WHERE Id_venta=$id_venta");
    $actualizado = true;
}

// Traer ventas (excluir pedidos proveedor)
$ventas = $mysqli->query("
    SELECT v.Id_venta, v.Fecha, v.Total, v.Realizacion,
           c.Nombre, c.Ap_paterno, c.Telefono
    FROM venta v
    INNER JOIN cliente c ON v.Id_cliente = c.Id_cliente
    WHERE v.Realizacion NOT LIKE 'PROV:%'
    ORDER BY v.Id_venta DESC
");

$page_id    = 'val_pago';
$page_title = 'Validación de Pago';
include("../Cliente/layout_header.php");
?>

<style>
.pago-badge { padding:4px 12px; border-radius:20px; font-size:11px; font-weight:800; letter-spacing:.5px; }
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:var(--card); border:1px solid var(--border); border-radius:16px; padding:32px; min-width:380px; max-width:480px; }
.modal-title { font-size:18px; font-weight:700; color:var(--text); margin-bottom:20px; }
</style>

<div class="ptitle">Validación de Pago</div>
<div class="psub"><?php echo $saludo; ?> — Gestiona y valida el estado de pago de las ventas</div>

<?php if(isset($actualizado)): ?>
<div class="alert alert-ok"><i class="fas fa-check-circle"></i> Estado de pago actualizado correctamente.</div>
<?php endif; ?>

<!-- Resumen tarjetas -->
<?php
$ventas->data_seek(0);
$tots = ['PENDIENTE'=>0,'PAGADO'=>0,'PARCIAL'=>0,'CANCELADO'=>0,'SIN_ESTADO'=>0,'monto_pendiente'=>0,'monto_pagado'=>0];
$all_rows = [];
while($r = $ventas->fetch_assoc()) {
    $partes = explode(':', $r['Realizacion']);
    if ($partes[0] === 'PAGO') { $est = $partes[1] ?? 'SIN_ESTADO'; }
    else { $est = 'SIN_ESTADO'; }
    $r['_estado'] = $est;
    $r['_metodo'] = $partes[2] ?? '—';
    $all_rows[] = $r;
    $tots[$est] = ($tots[$est] ?? 0) + 1;
    if ($est === 'PENDIENTE' || $est === 'SIN_ESTADO') $tots['monto_pendiente'] += $r['Total'];
    if ($est === 'PAGADO') $tots['monto_pagado'] += $r['Total'];
}
?>

<div class="stat-grid" style="margin-bottom:20px">
  <div class="sc sc-green">
    <div><div class="sc-val"><?php echo $tots['PAGADO']; ?></div><div class="sc-lbl">Pagados</div></div>
    <i class="fas fa-check-circle sc-icon"></i>
  </div>
  <div class="sc sc-amber">
    <div><div class="sc-val"><?php echo ($tots['PENDIENTE']+$tots['SIN_ESTADO']); ?></div><div class="sc-lbl">Pendientes</div></div>
    <i class="fas fa-clock sc-icon"></i>
  </div>
  <div class="sc sc-teal">
    <div><div class="sc-val"><?php echo $tots['PARCIAL']; ?></div><div class="sc-lbl">Pago parcial</div></div>
    <i class="fas fa-adjust sc-icon"></i>
  </div>
  <div class="sc sc-red">
    <div><div class="sc-val">$<?php echo number_format($tots['monto_pendiente'],2); ?></div><div class="sc-lbl">Monto pendiente</div></div>
    <i class="fas fa-dollar-sign sc-icon"></i>
  </div>
</div>

<!-- TABLA -->
<div class="tcard">
  <div class="tcard-hdr">
    <div class="tcard-ttl"><i class="fas fa-money-check-dollar" style="color:var(--accent)"></i> Ventas — Estado de pago</div>
  </div>
  <table>
    <thead>
      <tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Método</th><th>Estado pago</th><th>Validar</th></tr>
    </thead>
    <tbody>
    <?php foreach($all_rows as $row):
        $colores_est = ['PAGADO'=>'#22d3a5','PENDIENTE'=>'#f7c948','PARCIAL'=>'#4f8ef7','CANCELADO'=>'#f75e5e','SIN_ESTADO'=>'#7a8099'];
        $c = $colores_est[$row['_estado']] ?? '#7a8099';
        $icons_est = ['PAGADO'=>'✅','PENDIENTE'=>'⏳','PARCIAL'=>'🔶','CANCELADO'=>'❌','SIN_ESTADO'=>'❓'];
        $ic = $icons_est[$row['_estado']] ?? '❓';
    ?>
    <tr>
      <td>#<?php echo $row['Id_venta']; ?></td>
      <td><?php echo htmlspecialchars($row['Nombre'].' '.$row['Ap_paterno']); ?></td>
      <td><?php echo $row['Fecha']; ?></td>
      <td><strong>$<?php echo number_format($row['Total'],2); ?></strong></td>
      <td><?php echo htmlspecialchars($row['_metodo']); ?></td>
      <td>
        <span class="pago-badge" style="background:<?php echo $c; ?>22;color:<?php echo $c; ?>">
          <?php echo $ic; ?> <?php echo $row['_estado']; ?>
        </span>
      </td>
      <td>
        <button class="btn btn-sm btn-primary" onclick="abrirModal(<?php echo $row['Id_venta']; ?>,'<?php echo $row['_estado']; ?>','<?php echo $row['_metodo']; ?>')">
          <i class="fas fa-pen"></i> Validar
        </button>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if(count($all_rows)===0): ?>
    <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:24px">No hay ventas registradas.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- MODAL VALIDAR PAGO -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box">
    <div class="modal-title"><i class="fas fa-money-check-dollar" style="color:var(--accent)"></i> Validar pago — Venta #<span id="modalId"></span></div>
    <form action="index.php" method="post">
      <input type="hidden" name="id_venta" id="modalIdInput">
      <div class="fg" style="margin-bottom:14px"><label>Estado del pago</label>
        <select class="fc" name="estado_pago" id="modalEstado">
          <option value="PENDIENTE">⏳ Pendiente</option>
          <option value="PAGADO">✅ Pagado</option>
          <option value="PARCIAL">🔶 Pago parcial</option>
          <option value="CANCELADO">❌ Cancelado</option>
        </select>
      </div>
      <div class="fg" style="margin-bottom:14px"><label>Método de pago</label>
        <select class="fc" name="metodo_pago" id="modalMetodo">
          <option value="EFECTIVO">💵 Efectivo</option>
          <option value="TRANSFERENCIA">🏦 Transferencia</option>
          <option value="TARJETA">💳 Tarjeta</option>
          <option value="CHEQUE">📄 Cheque</option>
        </select>
      </div>
      <div class="fg" style="margin-bottom:18px"><label>Notas (opcional)</label>
        <input class="fc" type="text" name="notas" placeholder="Referencia, folio, etc.">
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
function abrirModal(id, estado, metodo) {
    document.getElementById('modalId').textContent = id;
    document.getElementById('modalIdInput').value = id;
    document.getElementById('modalEstado').value = estado === 'SIN_ESTADO' ? 'PENDIENTE' : estado;
    document.getElementById('modalMetodo').value = ['EFECTIVO','TRANSFERENCIA','TARJETA','CHEQUE'].includes(metodo) ? metodo : 'EFECTIVO';
    document.getElementById('modalOverlay').classList.add('open');
}
function cerrarModal() {
    document.getElementById('modalOverlay').classList.remove('open');
}
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if(e.target === this) cerrarModal();
});
</script>

<?php include("../Cliente/layout_footer.php"); ?>