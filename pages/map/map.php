<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("localhost", "root", "", "lugarlangdb");
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    echo "<script>alert('Database connection failed. Please try again later.');</script>";
    exit;
}

$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
    header("Location: ../login/login.php");
    exit();
}

$defaultCampus = "";


$stmt = $conn->prepare("SELECT def_campus FROM account_info WHERE user_id = ? AND has_default_destination = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($def_campus);
if ($stmt->fetch()) {
    $defaultCampus = $def_campus;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lugar Lang | Maps</title>
    <link rel="stylesheet" href="styles.css">
   
</head>
<body>
    <div class="map-container">
        <h1>Lugar Lang</h1>
        <form id="mapForm" method="POST">
            <label for="from">From</label>
            <input type="text" name="from" id="from" placeholder="Your current location">

            <label for="to">To</label>
            <input type="text" name="to" id="to" value="<?= htmlspecialchars($defaultCampus) ?>" placeholder="Destination">

            <button type="submit">Lugar Lang</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
        
            const form = document.getElementById("mapForm");
            form.addEventListener("submit", (e) => {
                e.preventDefault();
                const from = document.getElementById("from").value;
                const to = document.getElementById("to").value;

          
                console.log("Calculating route from:", from, "to:", to);
            });
        });
    </script>
</body>
</html>
