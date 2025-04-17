<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Campus | Lugar Lang</title>

    <style>
        :root {
            --primary-blue: #1e3a8a;
            --accent-orange: #ff6b35;
            --accent-green: #4caf50;
            --light-green: rgba(76, 175, 80, 0.1);
            --light-orange: rgba(255, 107, 53, 0.1);
        }

        body {
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #121212 0%, var(--primary-blue) 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            position: relative;
            overflow: hidden;
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

        .page-container {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 20px;
            backdrop-filter: blur(10px);
            border-top: 4px solid var(--accent-orange);
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

        /* Pin as default destination button styles */
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

        /* Success notification styles */
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

        /* Media queries for larger screens */
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

        @media (min-width: 1200px) {
            .page-container {
                max-width: 1140px;
            }
            
            .campus-card {
                height: 240px;
            }
        }
    </style>
</head>
<body>
    <?php
    session_start();
    

    if(!isset($_SESSION['user_id'])) {
        header("Location: ../login/login-page.php");    
        exit();
    }
    
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "lugarlangdb";
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    if (!$conn) {
        echo "<script>alert('Database connection failed. Please try again later.');</script>";
        exit;
    }
    
    if(!$conn){
        echo "<script>alert('Database connection failed. Please try again later.');</script>";
        exit;
    }
    
   
    $check_query = "SELECT has_default_destination, def_campus FROM user_info WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if(mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $has_default, $default_campus);
        mysqli_stmt_fetch($stmt);
        
   
        if($has_default == 1 && !isset($_GET['change'])) {
            header("Location: ../home/home.php");
            exit();
        }
    }
    
    // Handle AJAX request to set default destination
    if(isset($_POST['set_default_campus'])) {
        $campus = $_POST['campus'];
        
        $update_query = "UPDATE user_info SET def_campus = ?, has_default_destination = 1 WHERE user_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "si", $campus, $user_id);
        
        if(mysqli_stmt_execute($update_stmt)) {
            echo json_encode(['success' => true, 'message' => 'Default destination updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update default destination']);
        }
        
        mysqli_stmt_close($update_stmt);
        exit();
    }
    ?>

    <div class="page-container">
        <div class="header">
            <h2>Choose Your Campus</h2>
            <p>Select a destination campus to continue your journey with Lugar Lang!</p>
        </div>
        
        <div class="campus-grid">
            <!-- Campus 1 -->
            <div class="campus-card">
                <div class="campus-image-container">
                    <img src="../public/images/USC_Talamban_Campus.jpg" alt="Talamban Campus" class="campus-image">
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
                
                <div class="pin-destination" data-campus="talamban" <?php if(isset($default_campus) && $default_campus == 'talamban') echo 'class="active"'; ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path d="M32 32C32 14.3 46.3 0 64 0H320c17.7 0 32 14.3 32 32s-14.3 32-32 32H290.5l11.4 148.2c36.7 19.9 65.7 53.2 79.5 94.7l1 3c3.3 9.8 1.6 20.5-4.4 28.8s-15.7 13.3-26 13.3H32c-10.3 0-19.9-4.9-26-13.3s-7.7-19.1-4.4-28.8l1-3c13.8-41.5 42.8-74.8 79.5-94.7L93.5 64H64C46.3 64 32 49.7 32 32zM160 384h64v96c0 17.7-14.3 32-32 32s-32-14.3-32-32V384z"/>
                    </svg>
                    <span class="pin-tooltip">Pin as default destination</span>
                </div>
            </div>
            
            <!-- Campus 2 -->
            <div class="campus-card">
                <div class="campus-image-container">
                    <img src="../public/images/USC_Downtown_Campus.jpg" alt="Downtown Campus" class="campus-image">
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

                <div class="pin-destination" data-campus="downtown" <?php if(isset($default_campus) && $default_campus == 'downtown') echo 'class="active"'; ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                        <path d="M32 32C32 14.3 46.3 0 64 0H320c17.7 0 32 14.3 32 32s-14.3 32-32 32H290.5l11.4 148.2c36.7 19.9 65.7 53.2 79.5 94.7l1 3c3.3 9.8 1.6 20.5-4.4 28.8s-15.7 13.3-26 13.3H32c-10.3 0-19.9-4.9-26-13.3s-7.7-19.1-4.4-28.8l1-3c13.8-41.5 42.8-74.8 79.5-94.7L93.5 64H64C46.3 64 32 49.7 32 32zM160 384h64v96c0 17.7-14.3 32-32 32s-32-14.3-32-32V384z"/>
                    </svg>
                    <span class="pin-tooltip">Pin as default destination</span>
                </div>
            </div>
        </div>
    </div>


    <div class="notification" id="notification">
        <svg class="notification-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <path fill="white" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
        </svg>
        <span id="notification-message">Default Campus set successfully!</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle regular destination selection
            const destinationButtons = document.querySelectorAll('.set-destination-btn');
            destinationButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const campus = this.getAttribute('data-campus');
                    window.location.href = `home.php?campus=${campus}`;
                });
            });
            
            // Handle pin as default destination
            const pinButtons = document.querySelectorAll('.pin-destination');
            pinButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const campus = this.getAttribute('data-campus');
                    setDefaultDestination(campus);
                    
                    // Update UI to show active pin
                    pinButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            function setDefaultDestination(campus) {
                // Create form data
                const formData = new FormData();
                formData.append('set_default_campus', true);
                formData.append('campus', campus);
                
                // Send AJAX request
                fetch('choose-campus.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showNotification(data.message);
                        
                        // Redirect to homepage after 2 seconds
                        setTimeout(() => {
                            window.location.href = 'home.php';
                        }, 2000);
                    } else {
                        showNotification(data.message, false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', false);
                });
            }
            
            function showNotification(message, success = true) {
                const notification = document.getElementById('notification');
                const notificationMessage = document.getElementById('notification-message');
                
                // Set message and styling
                notificationMessage.textContent = message;
                notification.style.backgroundColor = success ? 'var(--accent-green)' : 'var(--error)';
                
                // Show notification
                notification.classList.add('show');
                
                // Hide after 5 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 5000);
            }
        });
    </script>
</body>
</html>


