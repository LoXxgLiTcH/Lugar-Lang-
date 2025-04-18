<?php
session_start();

// Database connection
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("localhost", "root", "", "lugarlangdb");
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check user session
$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Handle AJAX request to set default destination
if (isset($_POST['set_default_campus']) && isset($_POST['campus'])) {
    $campus = $_POST['campus'];

    // Make sure campus is either 'talamban' or 'downtown'
    if ($campus !== 'talamban' && $campus !== 'downtown') {
        echo json_encode(['success' => false, 'message' => 'Invalid campus selection']);
        exit;
    }

    // Update the account_info table with the default campus
    $update_query = "UPDATE account_info SET def_campus = ?, has_default_destination = 1 WHERE user_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "si", $campus, $user_id);

    if (mysqli_stmt_execute($update_stmt)) {
        // Also set it in the session for immediate use
        $_SESSION['default_campus'] = $campus;
        echo json_encode(['success' => true, 'message' => 'Default destination updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update default destination']);
    }

    mysqli_stmt_close($update_stmt);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
?>