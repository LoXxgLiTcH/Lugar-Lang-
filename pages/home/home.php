
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
    echo "<script>alert('User session is not set. Please log in again.');</script>";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["username"];
    $role = $_POST["role"];
    $landmark = $_POST["landmark"];
    $photo_tmp = $_FILES["photo"]["tmp_name"];
    $photo = $_FILES["photo"]["name"];
    $ext = pathinfo($photo, PATHINFO_EXTENSION);
    $allowed_types = ["jpg", "png", "jpeg"];
    $target_path = "profile_uploads/" . $photo;

    if (in_array($ext, $allowed_types) && move_uploaded_file($photo_tmp, $target_path)) {
        $stmt = $conn->prepare("UPDATE account_setup SET photo = ?, username = ?, role = ?, address = ?, has_completed_setup = 1 WHERE user_id = ?");
        $stmt->bind_param("ssssi", $photo, $name, $role, $landmark, $user_id);

        if ($stmt->execute()) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(function() {
                        completeSetup();
                    }, 1000);
                });
            </script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.')</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Invalid photo file or upload failed.')</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Setup | Lugar Lang</title>
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

  
        .homepage-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('public/images/placeholder-map.png'); 
            background-size: cover;
            background-position: center;
            filter: blur(5px);
            transition: filter 0.8s ease;
        }

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

        /* Setup container */
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
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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

        h2 {
            color: var(--neutral-dark);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        p {
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

        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--neutral-light);
            border-radius: var(--radius);
            font-size: 16px;
            color: var(--neutral-dark);
            transition: var(--transition);
            background-color: var(--white);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        input::placeholder, select::placeholder {
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

        .optional-label {
            font-size: 12px;
            background-color: var(--neutral-light);
            color: var(--neutral-medium);
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 6px;
            font-weight: normal;
        }

        .checkbox-group {
            margin-top: 8px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
        }

        .checkbox-item label {
            margin-bottom: 0;
            font-weight: normal;
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

        .info-tooltip {
            display: inline-block;
            width: 16px;
            height: 16px;
            background-color: var(--neutral-medium);
            color: var(--white);
            border-radius: 50%;
            text-align: center;
            line-height: 16px;
            font-size: 12px;
            margin-left: 6px;
            cursor: pointer;
            position: relative;
        }

        .info-tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--neutral-dark);
            color: var(--white);
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 10;
        }

        @media (min-width: 768px) {
            .setup-container {
                padding: 40px 32px;
                max-width: 480px;
            }
            
            h2 {
                font-size: 28px;
            }
        }

        @media (min-width: 1024px) {
            .setup-container {
                max-width: 520px;
            }
        }
    </style>
</head>
<body>
  
    
    <!-- Homepage background -->
    <div class="homepage-background" id="homepageBackground"></div>
    
    <!-- Overlay with setup form -->
    <div class="overlay-container" id="overlayContainer">
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
                    <input type="text" id="username" name="username" placeholder="Choose a unique username">
                </div>
                
                
                <div class="form-item">
                    <label for="role">Year Level / Role</label>
                    <select id="role" name="role">
                        <option value="" disabled selected>Select your role</option>
                        <option value="1st">1st Year Student</option>
                        <option value="2nd">2nd Year Student</option>
                        <option value="3rd">3rd Year Student</option>
                        <option value="4th">4th Year Student</option>
                    </select>
                </div>
                
                <div class="form-item">
                    <label for="landmark">Home Barangay / Pickup Landmark <span class="optional-label">Optional</span></label>
                    <input type="text" id="landmark" name="landmark" placeholder="e.g., Colon, Carbon, Mabolo">
                </div>
                
                <button type="submit" name="submit" class="submit-button" id="submitButton">Complete Setup</button>
            </form>
        </div>
    </div>

    <script>
        // Upload photo functionality
        const uploadBtn = document.getElementById('uploadBtn');
        const photoInput = document.getElementById('photoInput');
        const photoPreview = document.getElementById('photoPreview');
        
        uploadBtn.addEventListener('click', function(event) {
            photoInput.click();
        });
        
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.innerHTML = ''; 
                    photoPreview.innerHTML = `<img src="${e.target.result}" alt="Profile Photo">`;
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Form validation
        document.getElementById("setupForm").addEventListener("submit", function(event) {
            const username = document.getElementById("username").value;
            const role = document.getElementById("role").value;
            
            let isValid = true;
            let errorMessage = "";
            
            if (!username) {
                event.preventDefault();
                errorMessage += "Username is required.\n";
                isValid = false;
            }
            

            
            
            if (!role) {
                event.preventDefault();
                errorMessage += "Please select your role.\n";
                isValid = false;
            }
            
            if (!isValid) {
                alert(errorMessage);
            }
            // If valid, form will submit normally and PHP will handle the rest
        });
        
        // Function to handle completion animation
        function completeSetup() {
            const setupContainer = document.getElementById('setupContainer');
            const overlayContainer = document.getElementById('overlayContainer');
            const homepageBackground = document.getElementById('homepageBackground');
            
            // Add slide down animation class
            setupContainer.classList.add('slide-down');
            
            // Remove blur from background
            homepageBackground.style.filter = 'blur(0px)';
            
            // Fade out overlay
            setTimeout(function() {
                overlayContainer.style.opacity = '0';
                
                // Remove overlay after animation completes
                setTimeout(function() {
                    overlayContainer.style.display = 'none';
                }, 800);
            }, 300);
        }
        
        // For testing purposes (comment out or remove in production)
        // setTimeout(completeSetup, 3000);
    </script>
</body>
</html>