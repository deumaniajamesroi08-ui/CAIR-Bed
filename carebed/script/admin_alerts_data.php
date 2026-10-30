<?php
session_start();
require_once __DIR__ . '/admin_common.php';
if (!isset($_SESSION['user_id']) || strtolower((string)($_SESSION['role'] ?? '')) !== 'admin') {
    admin_json(['status' => 'error', 'message' => 'Unauthorized'], 403);
}
$alertTable = first_existing_table($conn, ['tbl_alert', 'tbl_alerts']);
if (!$alertTable) {
    admin_json(['status' => 'error', 'message' => 'Alert table not found.'], 500);
}
$timeColumn = column_exists($conn, $alertTable, 'created_at') ? 'created_at' : (column_exists($conn, $alertTable, 'timestamp') ? 'timestamp' : 'alert_id');
$alertTypeColumn = column_exists($conn, $alertTable, 'alert_type') ? 'alert_type' : (column_exists($conn, $alertTable, 'alert') ? 'alert' : "''");
$severityColumn = column_exists($conn, $alertTable, 'severity') ? 'severity' : "''";
$messageColumn = column_exists($conn, $alertTable, 'alert_message') ? 'alert_message' : "''";
$sensorColumn = column_exists($conn, $alertTable, 'sensor_value') ? 'sensor_value' : (column_exists($conn, $alertTable, 'bpm') ? 'bpm' : "''");
$query = "SELECT *, {$alertTypeColumn} AS resolved_alert_type, {$severityColumn} AS resolved_severity, {$messageColumn} AS resolved_message, {$sensorColumn} AS resolved_sensor, {$timeColumn} AS resolved_time FROM {$alertTable} ORDER BY {$timeColumn} DESC";
$res = $conn->query($query);
$rows = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        if (empty($row['resolved_severity'])) {
            $type = strtolower((string)$row['resolved_alert_type']);
            if (str_contains($type, 'abnormal') || str_contains($type, 'wetness') || str_contains($type, 'no movement')) {
                $row['resolved_severity'] = 'Critical';
            } elseif (str_contains($type, 'low movement') || str_contains($type, 'poor sleep')) {
                $row['resolved_severity'] = 'Warning';
            } else {
                $row['resolved_severity'] = 'Info';
            }
        }
        $rows[] = $row;
    }
}
admin_json(['status' => 'success', 'rows' => $rows, 'table_used' => $alertTable]);
