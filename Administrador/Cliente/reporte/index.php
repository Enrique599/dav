<?php
// Incluir la librería FPDF
require('pdf/fpdf.php');

// Definir la zona horaria (opcional)
date_default_timezone_set('America/Mexico_City');

// Crear una clase extendida para personalizar el Encabezado y Pie de página
class PDF extends FPDF {
    // Encabezado de página
    function Header() {
        // Logotipo (X, Y, Ancho) - Asegúrate de tener una imagen o comenta esta línea
        // $this->Image('logo.png', 10, 8, 33);
        
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(60);
        // Título (Ancho, Alto, Texto, Borde, Posición Siguiente, Alineación)
        $this->Cell(70, 10, 'REPORTE DE VENTAS', 1, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        
        // Fecha y hora del reporte
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Fecha de generacion: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
        $this->Ln(5);
    }

    // Pie de página
    function Footer() {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial itálica 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// --- Simulación de Datos (En la realidad, esto vendría de una base de datos MySQL) ---
$productos = [
    ["id" => 1, "nombre" => "Laptop HP Pavilion", "cantidad" => 2, "precio" => 750.00],
    ["id" => 2, "nombre" => "Mouse Optico Inalambrico", "cantidad" => 5, "precio" => 15.50],
    ["id" => 3, "nombre" => "Monitor Gamer 24''", "cantidad" => 1, "precio" => 210.00],
    ["id" => 4, "nombre" => "Teclado Mecanico RGB", "cantidad" => 3, "precio" => 45.00]
];

// Instanciar la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages(); // Define el total de páginas para {nb}
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// --- ENCABEZADO DE LA TABLA ---
// Colores, ancho de línea y fuente en negrita
$pdf->SetFillColor(232, 232, 232); // Fondo gris claro
$pdf->SetFont('Arial', 'B', 12);

// Celdas del encabezado (Ancho, Alto, Texto, Borde, Siguiente, Alineación, Relleno)
$pdf->Cell(15, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(85, 10, 'Producto', 1, 0, 'L', true);
$pdf->Cell(25, 10, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Precio Unit.', 1, 0, 'R', true);
$pdf->Cell(35, 10, 'Subtotal', 1, 1, 'R', true); // El '1' al final indica salto de línea

// --- CUERPO DE LA TABLA ---
$pdf->SetFont('Arial', '', 11);
$totalGeneral = 0;

foreach ($productos as $row) {
    $subtotal = $row['cantidad'] * $row['precio'];
    $totalGeneral += $subtotal;

    $pdf->Cell(15, 8, $row['id'], 1, 0, 'C');
    $pdf->Cell(85, 8, utf8_decode($row['nombre']), 1, 0, 'L'); // utf8_decode evita problemas con acentos/eñes
    $pdf->Cell(25, 8, $row['cantidad'], 1, 0, 'C');
    $pdf->Cell(30, 8, '$' . number_format($row['precio'], 2), 1, 0, 'R');
    $pdf->Cell(35, 8, '$' . number_format($subtotal, 2), 1, 1, 'R');
}

// --- FILA DE TOTAL ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(155, 10, 'Total General: ', 1, 0, 'R');
$pdf->Cell(35, 10, '$' . number_format($totalGeneral, 2), 1, 1, 'R');

// Enviar el PDF al navegador (I = Visualizar en el navegador, D = Forzar descarga)
$pdf->Output('I', 'Reporte_Ventas.pdf');
?>