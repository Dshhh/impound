<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once('../fpdf/fpdf.php');
require_once('../fpdi/src/autoload.php');

use setasign\Fpdi\Fpdi;

// Check if data is passed via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the POST request
    $record_id = $_POST['record_id'];
    $vehicle_type = $_POST['vehicle_type'];
    $chassis_number = $_POST['chassis_number'];
    $engine_number = $_POST['engine_number'];
    $plate_number = $_POST['plate_number'];
    $color = $_POST['color'];
    $orcr_number = $_POST['orcr_number'];
    $owner_last_name = $_POST['owner_last_name'];
    $owner_first_name = $_POST['owner_first_name'];
    $owner_middle_name = $_POST['owner_middle_name'];
    $owner_age = $_POST['owner_age'];
    $owner_gender = $_POST['owner_gender'];
    $owner_address = $_POST['owner_address'];
    $rider_last_name = $_POST['rider_last_name'];
    $rider_first_name = $_POST['rider_first_name'];
    $rider_middle_name = $_POST['rider_middle_name'];
    $rider_age = $_POST['rider_age'];
    $rider_gender = $_POST['rider_gender'];
    $rider_address = $_POST['rider_address'];
    $violation = $_POST['violation'];
    $officer_name = $_POST['officer_name'];

    // Create instance of FPDI
    $pdf = new Fpdi();

    // Load the existing PDF
    $pdf->AddPage();
    $pageCount = $pdf->setSourceFile('Receipt.pdf'); // Replace with your existing PDF file path
    $templateId = $pdf->importPage(1); // Import the first page of the existing PDF
    $pdf->useTemplate($templateId);

    // Set font for adding new content
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetTextColor(0, 0, 0);

    date_default_timezone_set('Asia/Manila');

    $currentAddress = "Brgy. Diversion";
    // Current Date and Time
    $currentDate = date('m/d/Y'); // Format: MM/DD/YYYY
    $currentTime = date('h:i A'); // Format: HH:MM AM/PM

    // Add new content to the existing PDF
    $pdf->SetXY(150, 39);
    $pdf->Write(10, $currentDate);
    $pdf->SetXY(62, 57.1);
    $pdf->Write(10, $vehicle_type);
    $pdf->SetXY(62, 64.6);
    $pdf->Write(10, $chassis_number);
    $pdf->SetXY(62, 71.8);
    $pdf->Write(10, $engine_number);
    $pdf->SetXY(62, 79.2);
    $pdf->Write(10, $plate_number);
    $pdf->SetXY(62, 86.4);
    $pdf->Write(10, $color);
    $pdf->SetXY(62, 93.8);
    $pdf->Write(10, $orcr_number);
    $pdf->SetXY(62, 101);
    $pdf->Write(10, $owner_last_name);
    $pdf->SetXY(89, 101);
    $pdf->Write(10, $owner_first_name);
    $pdf->SetXY(118, 101);
    $pdf->Write(10, $owner_middle_name);
    $pdf->SetXY(75, 112);
    $pdf->Write(10, $owner_age);
    $pdf->SetXY(111, 112);
    $pdf->Write(10, $owner_gender);
    $pdf->SetXY(62, 123);
    $pdf->Write(10, $owner_address);

    $pdf->SetXY(62, 133.8);
    $pdf->Write(10, $rider_last_name);
    $pdf->SetXY(89, 133.8);
    $pdf->Write(10, $rider_first_name);
    $pdf->SetXY(118, 133.8);
    $pdf->Write(10, $rider_middle_name);
    $pdf->SetXY(75, 144.9);
    $pdf->Write(10, $rider_age);
    $pdf->SetXY(111, 144.9);
    $pdf->Write(10, $rider_gender);
    $pdf->SetXY(62, 155.8);
    $pdf->Write(10, $rider_address);

    $pdf->SetXY(50, 173.9);
    $pdf->Write(10, $violation);


    // Add Date and Time separately
    $pdf->SetXY(34, 188.5); // Position for the Date
    $pdf->Write(10, $currentDate);

    $pdf->SetXY(73, 188.5); // Position for the Time (adjust as needed)
    $pdf->Write(10, $currentTime);

    $pdf->SetXY(127, 188.5);
    $pdf->Write(10, $currentAddress);

    $pdf->SetXY(120, 225);
    $pdf->Write(10, $officer_name);

    // Send the PDF to the browser
    $pdf->Output('I', 'impound_receipt_' . $record_id . '.pdf'); // Display in browser with a download option
} else {
    // Handle error if no data is provided
    echo "Error: No data received.";
}
?>
