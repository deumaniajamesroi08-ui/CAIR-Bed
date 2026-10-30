<?php
session_start();
include 'db_connection.php';


$stmt = $conn->prepare("SELECT timestamp, patient_name, alert, severity, status, bpm FROM tbl_alerts ORDER BY timestamp DESC");
$stmt->execute();

$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}


echo json_encode($data);

$stmt->close();
$conn->close();
?>