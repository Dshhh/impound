<?php
include '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $record_id = $_POST['record_id'];
    $color = $_POST['color'];
    $orcr_number = $_POST['orcr_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $chassis_number = $_POST['chassis_number'];
    $engine_number = $_POST['engine_number'];
    $plate_number = $_POST['plate_number'];
    $license_number = $_POST['license_number'];

    $sql = "
        UPDATE impound_records
        JOIN vehicles ON impound_records.vehicle_id = vehicles.vehicle_id
        JOIN riders ON impound_records.rider_id = riders.rider_id
        SET 
            vehicles.color = ?, 
            vehicles.orcr_number = ?, 
            vehicles.vehicle_type = ?, 
            vehicles.chassis_number = ?, 
            vehicles.engine_number = ?, 
            vehicles.plate_number = ?, 
            riders.license_number = ?
        WHERE impound_records.record_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssssi', $color, $orcr_number, $vehicle_type, $chassis_number, $engine_number, $plate_number, $license_number, $record_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
    $conn->close();
}
?>
