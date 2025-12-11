<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    die("Access Denied");
}

require_once '../config/config.php';
require_once '../src/Report.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'stock';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] . " 00:00:00" : date('Y-m-d 00:00:00');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] . " 23:59:59" : date('Y-m-d 23:59:59');

$database = new Database();
$db = $database->getConnection();
$report = new Report($db);

$pdf = new FPDF();
$pdf->AddPage('L'); // Landscape for better table fit
$pdf->SetFont('Arial', 'B', 16);

// Report Header
$report_title = ucfirst($type) . " Report";
$pdf->Cell(0, 10, 'Pharmacy IMS - ' . $report_title, 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Period: ' . substr($start_date, 0, 10) . ' to ' . substr($end_date, 0, 10), 0, 1, 'C');
$pdf->Ln(10);

if ($type == 'stock') {
    $data = $report->generateStockReport($start_date, $end_date); // Note: Stock report usually is "current state", but here acts as "Added between dates" based on existing Report.php logic logic. If "Current Stock" is needed, date filter might be irrelevant, but adhering to existing function signature.
    // Actually, checking Report.php, generateStockReport queries `created_at BETWEEN`. So it is "Inventory Added Report".
    // If we want CURRENT stock, we probably shouldn't filter by created_at. But user asked to separate buttons.
    // Let's assume user understands "Stock Report" as "Current Stock" usually. But strict adherence to Report.php uses created_at.
    // I will stick to what Report.php provides for now.

    $header = ['Name', 'Code', 'Quantity', 'Expiry Date', 'Location'];
    $w = [80, 40, 30, 40, 40];

    $pdf->SetFont('Arial', 'B', 12);
    for ($i = 0; $i < count($header); $i++)
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 11);
    foreach ($data as $row) {
        $pdf->Cell($w[0], 6, $row['name'], 1);
        $pdf->Cell($w[1], 6, $row['code'], 1);
        $pdf->Cell($w[2], 6, $row['quantity'], 1, 0, 'C');
        $pdf->Cell($w[3], 6, $row['expiry_date'], 1, 0, 'C');
        $pdf->Cell($w[4], 6, $row['location_code'], 1, 0, 'C');
        $pdf->Ln();
    }

} elseif ($type == 'sales') {
    $data = $report->generateSalesReport($start_date, $end_date);

    // Header for Sales
    $header = ['Date', 'TRX ID', 'Medicine', 'Qty', 'Total', 'Disc', 'Final', 'Pay Method'];
    // Widths: Total 277 available (A4 Landscape ~297mm - margins)
    $w = [40, 35, 60, 15, 25, 20, 25, 35];

    $pdf->SetFont('Arial', 'B', 10);
    for ($i = 0; $i < count($header); $i++)
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 9);
    $total_revenue = 0;

    foreach ($data as $row) {
        $pdf->Cell($w[0], 6, substr($row['sale_date'], 0, 10), 1);
        $pdf->Cell($w[1], 6, $row['transaction_id'], 1);
        $pdf->Cell($w[2], 6, substr($row['medicine_name'], 0, 25), 1);
        $pdf->Cell($w[3], 6, $row['quantity'], 1, 0, 'C');
        $pdf->Cell($w[4], 6, $row['total_price'], 1, 0, 'R');
        $pdf->Cell($w[5], 6, $row['discount'], 1, 0, 'R');
        $pdf->Cell($w[6], 6, number_format($row['final_price'], 2), 1, 0, 'R');
        $pdf->Cell($w[7], 6, $row['payment_method'], 1, 0, 'C');
        $pdf->Ln();

        $total_revenue += $row['final_price'];
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Total Revenue: ' . number_format($total_revenue, 2), 0, 1, 'R');

} elseif ($type == 'expiry') {
    $data = $report->generateExpiryReport($start_date, $end_date);

    $header = ['Name', 'Code', 'Quantity', 'Expiry Date', 'Location'];
    $w = [80, 40, 30, 40, 40];

    $pdf->SetFont('Arial', 'B', 12);
    for ($i = 0; $i < count($header); $i++)
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 11);
    foreach ($data as $row) {
        $pdf->Cell($w[0], 6, $row['name'], 1);
        $pdf->Cell($w[1], 6, $row['code'], 1);
        $pdf->Cell($w[2], 6, $row['quantity'], 1, 0, 'C');
        $pdf->Cell($w[3], 6, $row['expiry_date'], 1, 0, 'C');
        $pdf->Cell($w[4], 6, $row['location_code'], 1, 0, 'C');
        $pdf->Ln();
    }
}

$pdf->Output();
?>