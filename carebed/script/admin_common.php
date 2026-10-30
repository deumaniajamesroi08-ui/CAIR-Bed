<?php
require_once __DIR__ . '/db_connection.php';

function admin_json($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function table_exists(mysqli $conn, string $table): bool {
    $safe = $conn->real_escape_string($table);
    $result = $conn->query("SHOW TABLES LIKE '{$safe}'");
    return $result && $result->num_rows > 0;
}

function column_exists(mysqli $conn, string $table, string $column): bool {
    $tableSafe = $conn->real_escape_string($table);
    $columnSafe = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `{$tableSafe}` LIKE '{$columnSafe}'");
    return $result && $result->num_rows > 0;
}

function first_existing_table(mysqli $conn, array $tables): ?string {
    foreach ($tables as $table) {
        if (table_exists($conn, $table)) {
            return $table;
        }
    }
    return null;
}

function fetch_scalar(mysqli $conn, string $sql): int {
    $result = $conn->query($sql);
    if (!$result) return 0;
    $row = $result->fetch_row();
    return (int)($row[0] ?? 0);
}

function patient_status_from_row(array $row): array {
    $heartRate = isset($row['heart_rate']) && $row['heart_rate'] !== '--' ? (int)$row['heart_rate'] : 0;
    $temperature = isset($row['temperature']) && is_numeric($row['temperature']) ? (float)$row['temperature'] : null;
    $wetness = strtolower(trim((string)($row['wetness'] ?? '')));
    $presence = strtolower(trim((string)($row['presence_detection'] ?? '')));

    $status = 'NORMAL';
    $class = 'normal';

    if (
        $heartRate < 60 || $heartRate > 100 ||
        ($temperature !== null && ($temperature < 35.5 || $temperature > 38.0)) ||
        in_array($wetness, ['wet', 'detected', 'yes', '1'], true) ||
        in_array($presence, ['not detected', 'none', 'absent', 'no', '0'], true)
    ) {
        $status = 'CRITICAL';
        $class = 'critical';
    } elseif (
        (($heartRate >= 60 && $heartRate < 70) || ($heartRate > 90 && $heartRate <= 100)) ||
        ($temperature !== null && (($temperature >= 35.5 && $temperature < 36.0) || ($temperature > 37.5 && $temperature <= 38.0)))
    ) {
        $status = 'WARNING';
        $class = 'warning';
    }

    return ['status' => $status, 'class' => $class];
}
?>
