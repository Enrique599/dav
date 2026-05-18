<?php
require("conexiondos.php");
date_default_timezone_set('America/Mexico_City');

$hora = (int)date('H');
if($hora >= 6 && $hora < 12)       $saludo = "☀️ ¡Buenos días!";
elseif($hora >= 12 && $hora < 19)  $saludo = "🌤️ ¡Buenas tardes!";
else                                $saludo = "🌙 ¡Buenas noches!";

// Estadísticas — lógica original intacta
$total_clientes = $mysqli->query("SELECT COUNT(*) as total FROM cliente")->fetch_assoc()['total'];
$total_ventas   = $mysqli->query("SELECT COUNT(*) as total FROM venta")->fetch_assoc()['total'];
$total_prendas  = $mysqli->query("SELECT COUNT(*) as total FROM prendas")->fetch_assoc()['total'];
$monto_total    = $mysqli->query("SELECT SUM(Total) as suma FROM venta")->fetch_assoc()['suma'];
$monto_total    = $monto_total ? number_format($monto_total, 2) : '0.00';

// Ventas por mes
$ventas_mes = $mysqli->query("
    SELECT DATE_FORMAT(Fecha, '%b %Y') as mes,
           COUNT(*) as cantidad,
           SUM(Total) as total
    FROM venta
    GROUP BY DATE_FORMAT(Fecha, '%Y-%m'), DATE_FORMAT(Fecha, '%b %Y')
    ORDER BY MIN(Fecha) ASC
    LIMIT 6
");
$labels = []; $totales = [];
while($row = $ventas_mes->fetch_assoc()){
    $labels[]  = $row['mes'];
    $totales[] = $row['total'];
}

// Últimos 5 clientes
$ultimos = $mysqli->query("SELECT Id_cliente, Nombre, Ap_paterno, Telefono, Direccion FROM cliente ORDER BY Id_cliente DESC LIMIT 5");

// Últimas 5 ventas
$ult_ventas = $mysqli->query("
    SELECT v.Id_venta, v.Fecha, v.Total, v.Total_piezas, c.Nombre, c.Ap_paterno
    FROM venta v INNER JOIN cliente c ON v.Id_cliente=c.Id_cliente
    ORDER BY v.Id_venta DESC LIMIT 5
");

$page_id    = 'dashboard';
$page_title = 'Dashboard';
include("layout_header.php");
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <div>
    <div class="ptitle" style="margin-bottom:2px">Dashboard</div>
    <div class="psub" style="margin-bottom:0"><?php echo $saludo; ?> — Resumen general del sistema</div>
  </div>
  <div style="font-size:11.5px;color:var(--muted)">Home / <span style="color:var(--accent)">Dashboard v1</span></div>
</div>

<!-- STAT CARDS -->
<div class="stat-grid">
  <div class="sc sc-teal">
    <div>
      <div class="sc-val"><?php echo $total_clientes; ?></div>
      <div class="sc-lbl">Clientes Registrados</div>
      <a href="Cliente/index.php" class="sc-more" style="color:rgba(255,255,255,.85)">Ver clientes <i class="fas fa-arrow-right"></i></a>
    </div>
    <i class="fas fa-users sc-icon"></i>
  </div>
  <div class="sc sc-green">
    <div>
      <div class="sc-val"><?php echo $total_ventas; ?></div>
      <div class="sc-lbl">Ventas Totales</div>
      <a href="Venta/index.php" class="sc-more" style="color:rgba(255,255,255,.85)">Ver ventas <i class="fas fa-arrow-right"></i></a>
    </div>
    <i class="fas fa-chart-bar sc-icon"></i>
  </div>
  <div class="sc sc-amber">
    <div>
      <div class="sc-val"><?php echo $total_prendas; ?></div>
      <div class="sc-lbl">Prendas</div>
      <a href="prenda/index.php" class="sc-more" style="color:rgba(255,255,255,.85)">Ver prendas <i class="fas fa-arrow-right"></i></a>
    </div>
    <i class="fas fa-tshirt sc-icon"></i>
  </div>
  <div class="sc sc-red">
    <div>
      <div class="sc-val">$<?php echo $monto_total; ?></div>
      <div class="sc-lbl">Monto Total Ventas</div>
      <a href="Venta/index.php" class="sc-more" style="color:rgba(255,255,255,.85)">Más info <i class="fas fa-arrow-right"></i></a>
    </div>
    <i class="fas fa-dollar-sign sc-icon"></i>
  </div>
</div>

<!-- GRÁFICA + ÚLTIMOS CLIENTES -->
<div class="chart-row">
  <div class="chart-card">
    <div class="ch-hdr">
      <div class="ch-ttl"><i class="fas fa-chart-bar" style="color:var(--accent)"></i> Ventas por Mes</div>
      <div class="ch-btns">
        <button class="ch-btn act" onclick="switchChart('bar',this)">Barras</button>
        <button class="ch-btn" onclick="switchChart('line',this)">Línea</button>
      </div>
    </div>
    <canvas id="graficaVentas" height="200"></canvas>
  </div>
  <div class="chart-card">
    <div class="ch-hdr">
      <div class="ch-ttl"><i class="fas fa-pie-chart" style="color:var(--accent2)"></i> Resumen</div>
    </div>
    <canvas id="graficaDonut" height="160"></canvas>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:14px;text-align:center">
      <div>
        <div style="font-size:10px;color:var(--muted)">Clientes</div>
        <div style="font-size:20px;font-weight:800;color:var(--accent)"><?php echo $total_clientes; ?></div>
      </div>
      <div>
        <div style="font-size:10px;color:var(--muted)">Ventas</div>
        <div style="font-size:20px;font-weight:800;color:var(--accent2)"><?php echo $total_ventas; ?></div>
      </div>
      <div>
        <div style="font-size:10px;color:var(--muted)">Prendas</div>
        <div style="font-size:20px;font-weight:800;color:var(--accent3)"><?php echo $total_prendas; ?></div>
      </div>
    </div>
  </div>
</div>

<!-- TABLAS RECIENTES -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">

  <!-- Últimos Clientes -->
  <div class="tcard">
    <div class="tcard-hdr">
      <div class="tcard-ttl"><i class="fas fa-user-clock" style="color:var(--accent)"></i> Últimos 5 Clientes</div>
      <a href="Cliente/index.php" class="btn btn-sm btn-primary">Ver todos</a>
    </div>
    <table>
      <thead><tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Dirección</th></tr></thead>
      <tbody>
        <?php while($row = $ultimos->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['Id_cliente']; ?></td>
          <td><?php echo $row['Nombre'].' '.$row['Ap_paterno']; ?></td>
          <td><?php echo $row['Telefono']; ?></td>
          <td><?php echo $row['Direccion']; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Últimas Ventas -->
  <div class="tcard">
    <div class="tcard-hdr">
      <div class="tcard-ttl"><i class="fas fa-receipt" style="color:var(--accent2)"></i> Últimas 5 Ventas</div>
      <a href="Venta/index.php" class="btn btn-sm btn-success">Ver todas</a>
    </div>
    <table>
      <thead><tr><th>ID</th><th>Cliente</th><th>Total</th><th>Fecha</th></tr></thead>
      <tbody>
        <?php while($row = $ult_ventas->fetch_assoc()): ?>
        <tr>
          <td>#<?php echo $row['Id_venta']; ?></td>
          <td><?php echo $row['Nombre'].' '.$row['Ap_paterno']; ?></td>
          <td>$<?php echo number_format($row['Total'],2); ?></td>
          <td><?php echo $row['Fecha']; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</div>

<script>
const labelsData  = <?php echo json_encode($labels); ?>;
const totalesData = <?php echo json_encode(array_map('floatval', $totales)); ?>;
let salesChart;

const commonOpts = {
  responsive:true,
  plugins:{legend:{labels:{color:'#e4e8f0',font:{size:11}}}},
  scales:{
    x:{ticks:{color:'#7a8099'},grid:{color:'rgba(255,255,255,.05)'}},
    y:{ticks:{color:'#7a8099'},grid:{color:'rgba(255,255,255,.05)'}}
  }
};

function buildChart(type){
  const ctx = document.getElementById('graficaVentas').getContext('2d');
  if(salesChart) salesChart.destroy();
  salesChart = new Chart(ctx,{
    type,
    data:{
      labels: labelsData.length ? labelsData : ['Sin datos'],
      datasets:[{
        label:'Total Ventas ($)',
        data: totalesData.length ? totalesData : [0],
        backgroundColor: type==='bar' ? 'rgba(79,142,247,.55)' : 'rgba(79,142,247,.2)',
        borderColor:'#4f8ef7',borderWidth:2,
        borderRadius: type==='bar' ? 8 : 0,
        fill: type==='line',
        tension:.4
      }]
    },
    options: commonOpts
  });
}

function switchChart(type, btn){
  document.querySelectorAll('.ch-btn').forEach(b=>b.classList.remove('act'));
  btn.classList.add('act');
  buildChart(type);
}

buildChart('bar');

// Donut resumen
new Chart(document.getElementById('graficaDonut').getContext('2d'),{
  type:'doughnut',
  data:{
    labels:['Clientes','Ventas','Prendas'],
    datasets:[{
      data:[<?php echo $total_clientes; ?>,<?php echo $total_ventas; ?>,<?php echo $total_prendas; ?>],
      backgroundColor:['#4f8ef7','#22d3a5','#f7c948'],
      borderWidth:0
    }]
  },
  options:{plugins:{legend:{position:'bottom',labels:{color:'#e4e8f0',font:{size:11}}}}}
});
</script>

<?php include("layout_footer.php"); ?>
