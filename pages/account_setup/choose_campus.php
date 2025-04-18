<?php
session_start();


$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}


if (isset($_POST['campus'])) {
    $campus = $_POST['campus'];

    // Make sure campus is either 'talamban' or 'downtown'
    if ($campus !== 'talamban' && $campus !== 'downtown') {
        echo json_encode(['success' => false, 'message' => 'Invalid campus selection']);
        exit;
    }


    $_SESSION['current_campus'] = $campus;
    echo json_encode(['success' => true, 'message' => 'Campus set for this session']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
?>