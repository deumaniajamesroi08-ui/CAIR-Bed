<?php
function carebed_logged_profile($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    $profile = [
        'profile_id' => $user_id > 0 ? $user_id : 1,
        'fullname' => 'Current Patient',
        'email' => '',
        'age' => 'N/A'
    ];

    if ($user_id <= 0) {
        return $profile;
    }

    $stmt = $conn->prepare("
        SELECT profile_id, user_id, fullname, email, age
        FROM tbl_profile
        WHERE user_id = ? OR profile_id = ?
        LIMIT 1
    ");

    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            $profile['profile_id'] = (int)($row['profile_id'] ?? $user_id);
            $profile['fullname'] = trim((string)($row['fullname'] ?? 'Current Patient'));
            $profile['email'] = trim((string)($row['email'] ?? ''));
            $profile['age'] = trim((string)($row['age'] ?? 'N/A'));
        }
        $stmt->close();
    }

    return $profile;
}

function carebed_profile_by_id($conn, $profile_id) {
    $profile_id = (int)$profile_id;
    $profile = [
        'profile_id' => $profile_id,
        'fullname' => 'Current Patient',
        'email' => '',
        'age' => 'N/A'
    ];

    $stmt = $conn->prepare("
        SELECT profile_id, user_id, fullname, email, age
        FROM tbl_profile
        WHERE profile_id = ? OR user_id = ?
        LIMIT 1
    ");

    if ($stmt) {
        $stmt->bind_param("ii", $profile_id, $profile_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            $profile['profile_id'] = (int)($row['profile_id'] ?? $profile_id);
            $profile['fullname'] = trim((string)($row['fullname'] ?? 'Current Patient'));
            $profile['email'] = trim((string)($row['email'] ?? ''));
            $profile['age'] = trim((string)($row['age'] ?? 'N/A'));
        }
        $stmt->close();
    }

    return $profile;
}
?>