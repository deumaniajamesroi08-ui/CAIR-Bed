<?php
include 'db_connection.php';
header('Content-Type: application/json');

$sql = "
    SELECT 
        d.id,
        d.patient_id,
        d.respiration,
        d.timestamp,
        p.fullname,
        p.email
    FROM tbl_patient_data d
    LEFT JOIN tbl_profile p ON d.patient_id = p.profile_id
    ORDER BY d.timestamp DESC
    LIMIT 5
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "error" => true,
        "message" => $conn->error
    ]);
    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $respiration = (int)$row['respiration'];

    if ($respiration < 12) {
        $trend = "Low";
        $trendClass = "pending";
        $alert = "Warning";
    } elseif ($respiration > 20) {
        $trend = "High";
        $trendClass = "processing";
        $alert = "Critical";
    } else {
        $trend = "Normal";
        $trendClass = "completed";
        $alert = "No Alert";
    }

    $fullname = !empty($row['fullname']) ? $row['fullname'] : 'Unknown Patient';
    $email = !empty($row['email']) ? $row['email'] : 'Patient ID: ' . $row['patient_id'];

    $nameParts = explode(' ', trim($fullname));
    $initials = '';

    foreach ($nameParts as $part) {
        if (!empty($part)) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }

    if (empty($initials)) {
        $initials = 'PT';
    }

    $initials = substr($initials, 0, 2);

    $data[] = [
        'patient_id' => $row['patient_id'],
        'fullname' => $fullname,
        'email' => $email,
        'initials' => $initials,
        'respiration_rate' => $row['respiration'],
        'recorded_at' => date('M d, Y h:i A', strtotime($row['timestamp'])),
        'trend' => $trend,
        'trendClass' => $trendClass,
        'alert' => $alert
    ];
}

echo json_encode($data);

$conn->close();
?>