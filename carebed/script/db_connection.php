<?php
/**
 * Cairbed database connection
 * Tries the normal XAMPP MySQL port (3306) first, then 3307.
 * The connection is intentionally non-fatal so the application can show
 * a useful error instead of a mysqli_sql_exception stack trace.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

mysqli_report(MYSQLI_REPORT_OFF);

$host = '127.0.0.1';
$user = 'root';
$password = '';
$dbname = 'carebed_db';

/* You can override this in .env or the environment with DB_PORT. */
$preferredPort = 0;
if (isset($_ENV['DB_PORT']) && ctype_digit((string)$_ENV['DB_PORT'])) {
    $preferredPort = (int)$_ENV['DB_PORT'];
} elseif (isset($_SERVER['DB_PORT']) && ctype_digit((string)$_SERVER['DB_PORT'])) {
    $preferredPort = (int)$_SERVER['DB_PORT'];
}

$ports = $preferredPort > 0
    ? [$preferredPort, 3306, 3307]
    : [3306, 3307];

$ports = array_values(array_unique($ports));
$conn = false;
$connectionErrors = [];

foreach ($ports as $port) {
    $test = @mysqli_connect($host, $user, $password, $dbname, $port);

    if ($test instanceof mysqli) {
        $conn = $test;
        mysqli_set_charset($conn, 'utf8mb4');
        $GLOBALS['carebed_db_port'] = $port;
        break;
    }

    $connectionErrors[] = "MySQL port {$port}: " . mysqli_connect_error();
}

if (!$conn) {
    http_response_code(500);
    $details = implode(' | ', $connectionErrors);
    die(
        "Database connection failed. Make sure MySQL is running in XAMPP " .
        "and that carebed_db exists. Tried ports " . implode(', ', $ports) .
        ". Details: " . htmlspecialchars($details, ENT_QUOTES, 'UTF-8')
    );
}
?>
