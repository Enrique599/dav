<?php
require("conexiondos.php");
require("reporte/pdf/fpdf.php");

date_default_timezone_set('America/Mexico_City');

// Fecha en español
$dias   = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
$meses  = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

$dia_semana     = $dias[date('w')];
$dia_numero     = date('d');
$mes            = $meses[date('n') - 1];
$anio           = date('Y');
$hora           = date('H:i:s');
$fecha_completa = "$dia_semana $dia_numero de $mes de $anio - $hora";

// Traer todos los clientes
$sql       = "SELECT Id_cliente, Nombre, Ap_paterno, Telefono, Direccion, CP FROM cliente";
$resultado = $mysqli->query($sql);

// Crear PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// ── ENCABEZADO ──
$pdf->SetFillColor(74, 0, 128);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 15, 'Reporte de Clientes', 0, 1, 'C', true);

// Fecha
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 8, 'Generado el: ' . $fecha_completa, 0, 1, 'C');
$pdf->Ln(4);

// ── ENCABEZADO DE TABLA ──
$pdf->SetFillColor(122, 0, 212);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 11);

$pdf->Cell(20, 10, 'ID',          1, 0, 'C', true);
$pdf->Cell(50, 10, 'Nombre',      1, 0, 'C', true);
$pdf->Cell(50, 10, 'Ap. Paterno', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Telefono',    1, 0, 'C', true);
$pdf->Cell(80, 10, 'Direccion',   1, 0, 'C', true);
$pdf->Cell(30, 10, 'CP',          1, 1, 'C', true);

// ── FILAS ──
$pdf->SetFont('Arial', '', 10);
$fill = false;

while($row = $resultado->fetch_assoc()){
    if($fill){
        $pdf->SetFillColor(230, 210, 255);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }
    $pdf->SetTextColor(0, 0, 0);

    $pdf->Cell(20, 9, $row['Id_cliente'], 1, 0, 'C', true);
    $pdf->Cell(50, 9, $row['Nombre'],     1, 0, 'L', true);
    $pdf->Cell(50, 9, $row['Ap_paterno'], 1, 0, 'L', true);
    $pdf->Cell(45, 9, $row['Telefono'],   1, 0, 'C', true);
    $pdf->Cell(80, 9, $row['Direccion'],  1, 0, 'L', true);
    $pdf->Cell(30, 9, $row['CP'],         1, 1, 'C', true);

    $fill = !$fill;
}

// ── TOTAL ──
$pdf->Ln(4);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(74, 0, 128);
$total = $mysqli->query("SELECT COUNT(*) as total FROM cliente")->fetch_assoc();
$pdf->Cell(0, 8, 'Total de clientes registrados: ' . $total['total'], 0, 1, 'R');

// ── PIE DE PÁGINA ──
$pdf->Ln(4);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 6, 'Sistema CRUD - Reporte generado automaticamente', 0, 1, 'C');

// Descargar PDF
$pdf->Output('D', 'reporte_clientes_' . date('Y-m-d') . '.pdf');
exit;
?>