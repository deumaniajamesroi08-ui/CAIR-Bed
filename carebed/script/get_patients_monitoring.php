<?php
include 'db_connection.php';
header('Content-Type: application/json');

$sql = "
    SELECT 
        p.profile_id,
        p.fullname,
        latest.patient_id,
        latest.heart_rate,
        latest.respiration,
        latest.movement,
        latest.sleep_quality,
        latest.timestamp
    FROM tbl_profile p
    LEFT JOIN (
        SELECT d1.*
        FROM tbl_patient_data d1
        INNER JOIN (
            SELECT patient_id, MAX(timestamp) AS latest_time
            FROM tbl_patient_data
            GROUP BY patient_id
        ) d2
        ON d1.patient_id = d2.patient_id AND d1.timestamp = d2.latest_time
    ) latest
    ON p.profile_id = latest.patient_id
    ORDER BY p.fullname ASC
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
    $fullname = !empty($row['fullname']) ? $row['fullname'] : 'Unknown Patient';

    $nameParts = explode(' ', trim($fullname));
    $initials = '';
    foreach ($nameParts as $part) {
        if (!empty($part)) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }
    $initials = substr($initials, 0, 2);
    if (empty($initials)) {
        $initials = 'PT';
    }

    $heartRate = $row['heart_rate'];
    $respiration = $row['respiration'];
    $movement = $row['movement'];
    $sleepQuality = $row['sleep_quality'];
    $timestamp = $row['timestamp'];

    $status = 'No Data';
    $statusClass = 'processing';

    if (!is_null($heartRate) || !is_null($respiration) || !empty($movement) || !empty($sleepQuality)) {
        $warning = false;
        $critical = false;

        if (!is_null($heartRate)) {
            if ($heartRate < 50 || $heartRate > 110) {
                $critical = true;
            } elseif ($heartRate < 60 || $heartRate > 100) {
                $warning = true;
            }
        }

        if (!is_null($respiration)) {
            if ($respiration < 10 || $respiration > 24) {
                $critical = true;
            } elseif ($respiration < 12 || $respiration > 20) {
                $warning = true;
            }
        }

        if (!empty($movement) && strtolower($movement) === 'low') {
            $warning = true;
        }

        if (!empty($sleepQuality) && strtolower($sleepQuality) === 'poor') {
            $warning = true;
        }

        if ($critical) {
            $status = 'Critical';
            $statusClass = 'processing';
        } elseif ($warning) {
            $status = 'Warning';
            $statusClass = 'pending';
        } else {
            $status = 'Normal';
            $statusClass = 'completed';
        }
    }

    $bedNumber = str_pad($row['profile_id'], 3, '0', STR_PAD_LEFT);

    $data[] = [
        'profile_id' => $row['profile_id'],
        'fullname' => $fullname,
        'initials' => $initials,
        'patient_code' => 'PT-' . $bedNumber,
        'bed_id' => 'BED-' . $bedNumber,
        'heart_rate' => !is_null($heartRate) ? $heartRate . ' BPM' : '--',
        'respiration' => !is_null($respiration) ? $respiration . ' RPM' : '--',
        'movement' => !empty($movement) ? $movement : '--',
        'sleep_quality' => !empty($sleepQuality) ? $sleepQuality : '--',
        'status' => $status,
        'statusClass' => $statusClass,
        'timestamp_raw' => $timestamp,
        'last_updated' => !empty($timestamp) ? date('M d, Y h:i A', strtotime($timestamp)) : 'No data'
    ];
}

echo json_encode($data);

$conn->close();
?>