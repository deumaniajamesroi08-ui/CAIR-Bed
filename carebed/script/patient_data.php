<?php
session_start(); 

header('Content-Type: application/json');
include 'db_connection.php';


$user_id = $_SESSION['user_id'] ?? 0;

$user_sql = "SELECT full_name, username FROM tbl_user WHERE id = ?";
$stmt = $conn->prepare($user_sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();
    $stmt->close();
} else {
    $user = null;
}


$overview_sql = "SELECT * FROM tbl_patient_overview ORDER BY id DESC LIMIT 1";
$overview_result = mysqli_query($conn, $overview_sql);


$data_sql = "SELECT * FROM tbl_patient_data ORDER BY id DESC LIMIT 1";
$data_result = mysqli_query($conn, $data_sql);

if (
    $overview_result && mysqli_num_rows($overview_result) > 0 &&
    $data_result && mysqli_num_rows($data_result) > 0
) {
    $overview = mysqli_fetch_assoc($overview_result);
    $data = mysqli_fetch_assoc($data_result);

    $heart_rate = isset($data['heart_rate']) ? (int)$data['heart_rate'] : 0;
    $respiration = isset($data['respiration']) ? (int)$data['respiration'] : 0;
    $movement = isset($data['movement']) ? strtolower(trim($data['movement'])) : '';
    $sleep_quality = isset($data['sleep_quality']) ? strtolower(trim($data['sleep_quality'])) : '';

    $patient_status = "NORMAL";
    $patient_status_class = "normal";

    
    if (
        $heart_rate < 60 || $heart_rate > 100 ||
        $respiration < 10 || $respiration > 24 ||
        $movement == "abnormal" ||
        $movement == "no movement" ||
        $sleep_quality == "poor"
    ) {
        $patient_status = "CRITICAL";
        $patient_status_class = "critical";
    }
    
    elseif (
        ($heart_rate >= 60 && $heart_rate < 70) || ($heart_rate > 90 && $heart_rate <= 100) ||
        ($respiration >= 10 && $respiration < 12) || ($respiration > 20 && $respiration <= 24) ||
        $movement == "low" ||
        $movement == "moderate" ||
        $sleep_quality == "fair"
    ) {
        $patient_status = "WARNING";
        $patient_status_class = "warning";
    }

    $last_updated = !empty($data['updated_at']) ? $data['updated_at'] : $data['created_at'];

    
    $display_name = "Unknown User";
    if ($user) {
        $display_name = !empty($user['full_name']) 
            ? $user['full_name'] 
            : $user['username'];
    }

    echo json_encode([
        "status" => "success",
        "patient_name" => $display_name, 
        "bed_id" => $overview['bed_id'],
        "age" => $overview['age'],
        "patient_status" => $patient_status,
        "patient_status_class" => $patient_status_class,
        "last_updated" => $last_updated
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "No patient overview or patient data found"
    ]);
}

mysqli_close($conn);
?>
