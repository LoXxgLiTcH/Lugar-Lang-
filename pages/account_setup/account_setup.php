<?php
session_start();

// Database connection
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("localhost", "root", "", "lugarlangdb");
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    echo "<script>alert('Database connection failed. Please try again later.');</script>";
    exit;
}

// Check user session
$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
    header("Location: ../map/map.html");
    exit();
}

// Flag to determine if we should show the profile setup
$showProfileSetup = false;
$hasDefaultCampus = false;
$defaultCampus = null;
$hasCompletedSetup = false;

// Check if user has completed profile setup and has a default campus
$check_query = "SELECT has_default_destination, def_campus, has_completed_setup FROM account_info WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_bind_result($stmt, $has_default, $def_campus, $has_completed_setup);
    mysqli_stmt_fetch($stmt);
    
    $hasDefaultCampus = ($has_default == 1);
    $defaultCampus = $def_campus;
    $hasCompletedSetup = ($has_completed_setup == 1);
    
    // If user hasn't completed setup, show profile setup
    if (!$hasCompletedSetup) {
        $showProfileSetup = true;
    }
    
    // If user has default destination and not changing it, redirect to home
    if ($hasDefaultCampus && !isset($_GET['change'])) {
        $_SESSION['current_campus'] = $defaultCampus; // Set current campus in session
        header("Location: ../splash/splash.html");
        exit();
    }
}

// Handle profile setup form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["username"])) {
    $name = $_POST["username"];
    $role = $_POST["role"];
    $photo = "default_profile.jpg"; // Default image in case upload fails

    // Check if photo was uploaded
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK) {
        $photo_tmp = $_FILES["photo"]["tmp_name"];
        $photo = $_FILES["photo"]["name"];
        $ext = pathinfo($photo, PATHINFO_EXTENSION);
        $allowed_types = ["jpg", "png", "jpeg", "gif"];
        $upload_dir = "../../profile_uploads/";
        $target_path = $upload_dir . $photo;
        
        // Check if directory exists and is writable
        if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
            echo "<script>alert('Upload directory does not exist or is not writable. Please contact the administrator.')</script>";
        } else if (in_array(strtolower($ext), $allowed_types)) {
            if (!move_uploaded_file($photo_tmp, $target_path)) {
                echo "<script>alert('Failed to move uploaded file. Using default profile picture.')</script>";
            }
        } else {
            echo "<script>alert('Invalid photo file type. Only JPG, PNG, JPEG, and GIF are allowed. Using default profile picture.')</script>";
        }
    }

    // Update user profile
    $stmt = $conn->prepare("UPDATE account_info SET photo = ?, username = ?, role = ?, has_completed_setup = 1 WHERE user_id = ?");
    $stmt->bind_param("sssi", $photo, $name, $role, $user_id);

    if ($stmt->execute()) {
        $showProfileSetup = false;
        $hasCompletedSetup = true;
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                completeSetup();
            }, 500);
        });
        </script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.')</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Lugar Lang!</title>
    <!-- Your existing styles here -->
</head>

<body>
    <div class="page-container" id="pageContainer" <?php if ($showProfileSetup) echo 'class="blurred"'; ?>>
        <div class="header">
            <h2>Choose Your Campus</h2>
            <p>Select a destination campus to continue your journey with Lugar Lang!</p>
        </div>

        <div class="campus-grid">
            <!-- Campus 1 -->
            <div class="campus-card">
                <div class="campus-image-container">
                    <img src="../../public/images/USC_Talamban_Campus.jpg" alt="Talamban Campus" class="campus-image">
                    <div class="campus-overlay">
                        <p>Set this campus as your destination</p>
                        <button class="set-destination-btn" data-campus="talamban">Set Destination</button>
                    </div>
                </div>
                <div class="campus-details">
                    <h3 class="campus-name">Talamban Campus</h3>
                    <p class="campus-location">University of San Carlos, Nasipit, Talamban, Cebu City</p>
                    <p class="campus-description">The University of San Carlos Talamban campus in Cebu combines nature and modern design, set on a hill with lush trees and singing birds. Students often ride jeepneys to reach higher levels of the campus, adding a fun twist to their day. With fresh air, grassy fields, and friendly wild dogs and cats, it offers a relaxing yet vibrant atmosphere for learning.</p>
                </div>

                <div class="pin-destination" data-campus="talamban" <?php if ($defaultCampus == 'talamban') echo 'class="active"'; ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path d="M32 32C32 14.3 46.3 0 64 0H320c17.7 0 32 14.3 32 32s-14.3 32-32 32H290.5l11.4 148.2c36.7 19.9 65.7 53.2 79.5 94.7l1 3c3.3 9.8 1.6 20.5-4.4 28.8s-15.7 13.3-26 13.3H32c-10.3 0-19.9-4.9-26-13.3s-7.7-19.1-4.4-28.8l1-3c13.8-41.5 42.8-74.8 79.5-94.7L93.5 64H64C46.3 64 32 49.7 32 32zM160 384h64v96c0 17.7-14.3 32-32 32s-32-14.3-32-32V384z" />
                    </svg>
                    <span class="pin-tooltip">Pin as default destination</span>
                </div>
            </div>

            <!-- Campus 2 -->
            <div class="campus-card">
                <div class="campus-image-container">
                    <img src="../../public/images/USC_Downtown_Campus.jpg" alt="Downtown Campus" class="campus-image">
                    <div class="campus-overlay">
                        <p>Set this campus as your destination</p>
                        <button class="set-destination-btn" data-campus="downtown">Set Destination</button>
                    </div>
                </div>
                <div class="campus-details">
                    <h3 class="campus-name">Downtown Campus</h3>
                    <p class="campus-location">University of San Carlos Main, Alcantara St, Cebu City</p>
                    <p class="campus-description">The University of San Carlos downtown campus in Cebu is located in the bustling heart of the city, surrounded by urban buildings and vibrant life. While it has a more traditional school setup with corridors and classrooms, there are still green spaces that add a touch of nature to the environment. Designed with business and law students in mind, the campus exudes a professional vibe, featuring uniform colors and a compact layout that fosters a focused and dynamic atmosphere for learning.</p>
                </div>

                <div class="pin-destination" data-campus="downtown" <?php if ($defaultCampus == 'downtown') echo 'class="active"'; ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path d="M32 32C32 14.3 46.3 0 64 0H320c17.7 0 32 14.3 32 32s-14.3 32-32 32H290.5l11.4 148.2c36.7 19.9 65.7 53.2 79.5 94.7l1 3c3.3 9.8 1.6 20.5-4.4 28.8s-15.7 13.3-26 13.3H32c-10.3 0-19.9-4.9-26-13.3s-7.7-19.1-4.4-28.8l1-3c13.8-41.5 42.8-74.8 79.5-94.7L93.5 64H64C46.3 64 32 49.7 32 32zM160 384h64v96c0 17.7-14.3 32-32 32s-32-14.3-32-32V384z" />
                    </svg>
                    <span class="pin-tooltip">Pin as default destination</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Setup Overlay -->
    <div class="overlay-container" id="overlayContainer" data-show="<?php echo $showProfileSetup ? 'true' : 'false'; ?>" style="<?php echo $showProfileSetup ? '' : 'display: none;'; ?>">
        <div class="setup-container" id="setupContainer">
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>

            <h2>Complete Your Profile</h2>
            <p>Let's personalize your Lugar Lang experience</p>

            <form method="POST" id="setupForm" action="" enctype="multipart/form-data">
                <div class="photo-upload">
                    <div class="photo-preview" id="photoPreview">
                        <div class="photo-placeholder">+</div>
                    </div>
                    <div>
                        <button type="button" class="upload-btn" id="uploadBtn">Upload Photo</button>
                        <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;">
                        <p style="font-size: 12px; margin-top: 6px; color: var(--neutral-medium);">For easier recognition by classmates :P</p>
                    </div>
                </div>

                <div class="form-item">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a unique username" required>
                </div>

                <div class="form-item">
                    <label for="role">Year Level / Role</label>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="1st">1st Year Student</option>
                        <option value="2nd">2nd Year Student</option>
                        <option value="3rd">3rd Year Student</option>
                        <option value="4th">4th Year Student</option>
                    </select>
                </div>

                <button type="submit" name="submit" class="submit-button" id="submitButton">Complete Setup</button>
            </form>
        </div>
    </div>

    <div class="notification" id="notification">
        <svg class="notification-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <path fill="white" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
        </svg>
        <span id="notification-message">Default Campus set successfully!</span>
    </div>
    <script src="setup.js" defer></script>
    <script src="choose_campus.js" defer></script>
</body>

</html>