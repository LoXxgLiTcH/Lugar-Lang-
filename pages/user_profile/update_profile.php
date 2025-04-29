<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
  $conn = new mysqli("localhost", "root", "", "lugarlangdb");
  $conn->set_charset("utf8mb4");
} catch (Exception $e) {
  $_SESSION['update_success'] = false;
  header("Location: user-profile.php");
  exit;
}

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $year = $_POST['year'] ?? '';
  $default_campus = $_POST['default_campus'] ?? '';


  $stmt = $conn->prepare("UPDATE account_info SET username = ?, year = ?, def_campus = ? WHERE user_id = ?");
  $stmt->bind_param("sssi", $username, $year, $default_campus, $user_id);
  $stmt->execute();
  $stmt->close();


  if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['profile_photo']['tmp_name'];
    $photo_path = 'uploads/' . basename($_FILES['profile_photo']['name']);
    move_uploaded_file($tmp_name, $photo_path);

    $stmt2 = $conn->prepare("UPDATE account_info SET photo = ? WHERE user_id = ?");
    $stmt2->bind_param("si", $photo_path, $user_id);
    $stmt2->execute();
    $stmt2->close();
  }

  $_SESSION['update_success'] = true;
}

header("Location: user_profile.php");
exit;
