<?php
include 'db_connection.php';
header('Content-Type: application/json');

$sql = "
    SELECT 
        p.profile_id,
        p.fullname,
        p.age,
        d.heart_rate,
        d.temperature,
        d.wetness,
        d.presence_detection,
        d.timestamp
    FROM tbl_profile p
    LEFT JOIN (
        SELECT d1.*
        FROM tbl_patient_data d1
        INNER JOIN (
            SELECT patient_id, MAX(id) AS latest_id
            FROM tbl_patient_data
            GROUP BY patient_id
        ) d2 ON d1.id = d2.latest_id
    ) d ON d.patient_id = p.profile_id
    WHERE LOWER(COALESCE(p.role, '')) != 'admin'
    ORDER BY p.profile_id DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        'status' => 'error',
        'message' => mysqli_error($conn),
        'patients' => []
    ]);
    exit;
}

$patients = [];

while ($row = mysqli_fetch_assoc($result)) {
    $patients[] = [
        'profile_id' => $row['profile_id'],
        'patient_name' => !empty($row['fullname']) ? $row['fullname'] : 'Unknown Patient',
        'bed_id' => 'BED-' . str_pad((string)$row['profile_id'], 3, '0', STR_PAD_LEFT),
        'age' => $row['age'] ?: 'N/A',
        'heart_rate' => $row['heart_rate'] ?: '--',
        'temperature' => $row['temperature'] ?: '--',
        'wetness' => $row['wetness'] ?: '--',
        'presence_detection' => $row['presence_detection'] ?: '--',
        'timestamp' => $row['timestamp'] ?: 'No data'
    ];
}

echo json_encode([
    'status' => 'success',
    'patients' => $patients
]);
?>
