<?php
session_start();
include '../script/db_connection.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

function safe($v, $fallback = '') {
    return $v !== null && $v !== '' ? $v : $fallback;
}

$patients = [];

$sql = "
    SELECT
        p.profile_id,
        p.fullname,
        p.age,
        pd.heart_rate,
        pd.temperature,
        pd.wetness,
        pd.presence_detection,
        pd.timestamp
    FROM tbl_profile p
    LEFT JOIN tbl_patient_data pd ON pd.patient_id = p.profile_id
    WHERE LOWER(COALESCE(p.role,'')) NOT IN ('admin')
    ORDER BY p.profile_id DESC
";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $profile_id = (int)$row['profile_id'];

        
        if ($row['timestamp'] === null || $row['heart_rate'] === null) {
            $fallback = mysqli_query($conn, "
                SELECT heart_rate, temperature, wetness, presence_detection, created_at
                FROM tbl_records
                WHERE profile_id = {$profile_id}
                ORDER BY created_at DESC
                LIMIT 1
            ");
            if ($fallback && mysqli_num_rows($fallback) > 0) {
                $rec = mysqli_fetch_assoc($fallback);
                $row['heart_rate'] = $rec['heart_rate'];
                $row['temperature'] = $rec['temperature'];
                $row['wetness'] = $rec['wetness'];
                $row['presence_detection'] = $rec['presence_detection'];
                $row['timestamp'] = $rec['created_at'];
            }
        }

        $heart = is_numeric($row['heart_rate']) ? (int)$row['heart_rate'] : null;
        $temp = is_numeric($row['temperature']) ? (float)$row['temperature'] : null;
        $wet = safe($row['wetness'], '--');
        $presence = safe($row['presence_detection'], '--');
        $timestamp = $row['timestamp'];

        $status = 'OFFLINE';

        if ($timestamp) {
            $last = strtotime($timestamp);
            $diff = time() - $last;

            if ($diff > 30) {
                $status = 'OFFLINE';
            } else if (($heart !== null && ($heart < 50 || $heart > 100)) || ($temp !== null && $temp >= 38) || in_array(strtolower($wet), ['wet','moist','damp'])) {
                $status = 'CRITICAL';
            } else {
                $status = 'NORMAL';
            }
        }

        $patients[] = [
            'profile_id' => $profile_id,
            'patient_name' => safe($row['fullname'], 'Unknown Patient'),
            'bed_id' => 'BED-' . str_pad($profile_id, 3, '0', STR_PAD_LEFT),
            'age' => safe($row['age'], 'N/A'),
            'heart_rate' => $heart,
            'temperature' => $temp,
            'wetness' => $wet,
            'presence_detection' => $presence,
            'status' => $status,
            'updated' => $timestamp ?: 'No data'
        ];
    }
}

$alerts = [];
$alertSql = "
    SELECT 
        a.alert_id,
        a.patient_id,
        a.alert_type,
        a.severity,
        a.status,
        a.message,
        a.created_at,
        COALESCE(p.fullname, a.patient_name, 'Unknown Patient') AS patient_name
    FROM tbl_alert a
    LEFT JOIN tbl_profile p ON p.profile_id = a.patient_id
    ORDER BY a.created_at DESC
    LIMIT 10
";

$alertResult = mysqli_query($conn, $alertSql);
if ($alertResult) {
    while ($a = mysqli_fetch_assoc($alertResult)) {
        $alerts[] = [
            'patient_name' => safe($a['patient_name'], 'Unknown Patient'),
            'alert_type' => safe($a['alert_type'], 'Alert'),
            'severity' => safe($a['severity'], 'Info'),
            'status' => safe($a['status'], 'Unread'),
            'message' => safe($a['message'], ''),
            'created_at' => safe($a['created_at'], '')
        ];
    }
}

$totalPatients = count($patients);
$activeAlerts = 0;
$criticalPatients = 0;

foreach ($patients as $p) {
    if ($p['status'] === 'CRITICAL') $criticalPatients++;
}

$countAlert = mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_alert WHERE LOWER(COALESCE(status,'')) != 'read'");
if ($countAlert && $r = mysqli_fetch_assoc($countAlert)) {
    $activeAlerts = (int)$r['c'];
}

$userCount = 0;
$countUsers = mysqli_query($conn, "SELECT COUNT(*) AS c FROM tbl_user");
if ($countUsers && $r = mysqli_fetch_assoc($countUsers)) {
    $userCount = (int)$r['c'];
}

echo json_encode([
    'status' => 'success',
    'summary' => [
        'total_patients' => $totalPatients,
        'system_users' => $userCount,
        'active_alerts' => $activeAlerts,
        'critical_patients' => $criticalPatients
    ],
    'patients' => $patients,
    'alerts' => $alerts
]);
?>