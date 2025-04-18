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
    <style>
        :root {
            --primary: #FF7F2A;
            --primary-light: #FFD6BC;
            --secondary: #2EA355;
            --secondary-light: #C5EACD;
            --neutral-dark: #333333;
            --neutral-medium: #9E9E9E;
            --neutral-light: #F4F4F4;
            --white: #FFFFFF;
            --error: #EF5350;
            --shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --transition: all 0.3s ease;
            --primary-blue: #1e3a8a;
            --accent-orange: #ff6b35;
            --accent-green: #4caf50;
            --light-green: rgba(76, 175, 80, 0.1);
            --light-orange: rgba(255, 107, 53, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body,
        html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
        }

        body {
            background: linear-gradient(135deg, #121212 0%, var(--primary-blue) 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(white 1px, transparent 0);
            background-size: 50px 50px;
            background-position: -25px -25px;
            opacity: 0.1;
            z-index: -1;
        }

        /* Campus Selection Page Styles */
        .page-container {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 20px;
            backdrop-filter: blur(10px);
            border-top: 4px solid var(--accent-orange);
            transition: filter 0.8s ease;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary-blue);
            position: relative;
            display: inline-block;
        }

        .header h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-orange), var(--accent-green));
            border-radius: 3px;
        }

        .header p {
            color: #666;
            margin: 0 auto;
            max-width: 90%;
        }

        .campus-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .campus-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: white;
            height: 100%;
            border-left: 3px solid var(--accent-green);
            position: relative;
        }

        .campus-card:nth-child(even) {
            border-left: 3px solid var(--accent-orange);
        }

        .campus-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .campus-image-container {
            position: relative;
            height: 180px;
            overflow: hidden;
        }

        .campus-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: filter 0.3s ease;
        }

        .campus-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.8), rgba(76, 175, 80, 0.6));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .campus-image-container:hover .campus-image {
            filter: blur(3px);
        }

        .campus-image-container:hover .campus-overlay {
            opacity: 1;
        }

        .campus-details {
            padding: 15px;
            background: linear-gradient(to bottom, white, var(--light-green));
        }

        .campus-card:nth-child(even) .campus-details {
            background: linear-gradient(to bottom, white, var(--light-orange));
        }

        .campus-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary-blue);
        }

        .campus-location {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .campus-location::before {
            content: '•';
            color: var(--accent-orange);
            margin-right: 5px;
            font-size: 1.2rem;
        }

        .campus-card:nth-child(even) .campus-location::before {
            color: var(--accent-green);
        }

        .campus-description {
            color: #555;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .set-destination-btn {
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-green));
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        .campus-card:nth-child(even) .set-destination-btn {
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-orange));
        }

        .set-destination-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .pin-destination {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background-color: white;
            border: 2px solid var(--accent-green);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .campus-card:nth-child(even) .pin-destination {
            border-color: var(--accent-orange);
        }

        .pin-destination:hover {
            transform: scale(1.1);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        .pin-destination svg {
            width: 18px;
            height: 18px;
            fill: var(--accent-green);
            transition: fill 0.3s ease;
        }

        .campus-card:nth-child(even) .pin-destination svg {
            fill: var(--accent-orange);
        }

        .pin-destination.active {
            background-color: var(--accent-green);
        }

        .campus-card:nth-child(even) .pin-destination.active {
            background-color: var(--accent-orange);
        }

        .pin-destination.active svg {
            fill: white;
        }

        .pin-tooltip {
            position: absolute;
            bottom: 50px;
            right: 0;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            white-space: nowrap;
        }

        .pin-destination:hover .pin-tooltip {
            opacity: 1;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background-color: var(--accent-green);
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(120%);
            transition: transform 0.4s ease;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification-icon {
            width: 20px;
            height: 20px;
        }

        /* Profile Setup Overlay Styles */
        .overlay-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            background-color: rgba(0, 0, 0, 0.4);
            transition: opacity 0.8s ease;
        }

        .setup-container {
            width: 100%;
            max-width: 420px;
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 32px 24px;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
            transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .setup-container.slide-down {
            transform: translateY(150vh);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background-color: var(--neutral-light);
            margin-bottom: 24px;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 70%;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 2px;
        }

        .setup-container h2 {
            color: var(--neutral-dark);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .setup-container p {
            color: var(--neutral-medium);
            font-size: 16px;
            margin-bottom: 28px;
        }

        .form-item {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            color: var(--neutral-dark);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--neutral-light);
            border-radius: var(--radius);
            font-size: 16px;
            color: var(--neutral-dark);
            transition: var(--transition);
            background-color: var(--white);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        input::placeholder,
        select::placeholder {
            color: var(--neutral-medium);
            opacity: 0.7;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239e9e9e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
        }

        .photo-upload {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .photo-preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--neutral-light);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid var(--primary-light);
        }

        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            color: var(--neutral-medium);
            font-size: 24px;
        }

        .upload-btn {
            padding: 10px 16px;
            background-color: var(--primary-light);
            color: var(--primary);
            border: none;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .upload-btn:hover {
            background-color: var(--primary);
            color: var(--white);
        }

        .upload-btn:active {
            transform: translateY(1px);
        }

        .submit-button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: var(--radius);
            background-color: var(--primary);
            color: var(--white);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }

        .submit-button:hover {
            background-color: #E86C1A;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(255, 127, 42, 0.3);
        }

        .submit-button:active {
            transform: translateY(1px);
            box-shadow: 0 2px 8px rgba(255, 127, 42, 0.3);
        }

        .setup-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        }

        /* Responsive adjustments */
        @media (min-width: 576px) {
            .page-container {
                padding: 30px;
                max-width: 540px;
            }

            .header p {
                max-width: 80%;
            }
        }

        @media (min-width: 768px) {
            .page-container {
                padding: 35px;
                max-width: 720px;
            }

            .campus-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }

            .campus-card {
                display: flex;
                flex-direction: column;
            }

            .campus-image-container {
                height: 200px;
            }

            .setup-container {
                padding: 40px 32px;
                max-width: 480px;
            }

            .setup-container h2 {
                font-size: 28px;
            }
        }

        @media (min-width: 992px) {
            .page-container {
                padding: 40px;
                max-width: 960px;
            }

            .campus-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }

            .campus-card {
                flex-direction: row;
                height: 220px;
            }

            .campus-image-container {
                width: 40%;
                height: 100%;
            }

            .campus-details {
                width: 60%;
                padding: 20px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .pin-destination {
                bottom: 20px;
                right: 20px;
            }
        }

        @media (min-width: 1024px) {
            .setup-container {
                max-width: 520px;
            }
        }

        @media (min-width: 1200px) {
            .page-container {
                max-width: 1140px;
            }

            .campus-card {
                height: 240px;
            }
        }

        /* Blurred state for when overlay is showing */
        .page-container.blurred {
            filter: blur(5px);
        }
    </style>
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
    <script src="setup.js"></script>
    <script src="choose_campus.js"></script>
</body>

</html>