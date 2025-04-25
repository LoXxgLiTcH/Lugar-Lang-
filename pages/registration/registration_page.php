<?php
   session_start();
   $db_server = "localhost";
   $db_user = "root";
   $db_pass = "";
   $db_name = "lugarlangdb";
   
   mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 

   $error_message = "";
   $name = "";
   $email = "";
   
   try {
       $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
       $conn->set_charset("utf8mb4"); 
   } catch (Exception $e) {
       $error_message = "Database connection failed. Please try again later.";
   }

   if(isset($_POST["submitBtn"])){
       $name = trim($_POST["fullName"]);
       $email = trim($_POST["email"]);
       $pass = $_POST["password"];
       $confPass = $_POST["confirmPassword"];
       
       // Validation
       if (empty($name) || empty($email) || empty($pass) || empty($confPass)) {
           $error_message = "All fields are required.";
       }
       // Check if email ends with @usc.edu.ph
       else if (!preg_match('/^[\w.%+-]+@usc\.edu\.ph$/i', $email)) {
           $error_message = "Please use a valid USC email address (@usc.edu.ph).";
       }
       // Check if passwords match
       else if($pass !== $confPass) {
           $error_message = "Passwords do not match.";
       }
       else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $pass)) {
           $error_message = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
       }
       else {
           // Check if email already exists
           $check_email = mysqli_prepare($conn, "SELECT email FROM registration WHERE email = ?");
           mysqli_stmt_bind_param($check_email, "s", $email);
           mysqli_stmt_execute($check_email);
           mysqli_stmt_store_result($check_email);
           
           if(mysqli_stmt_num_rows($check_email) > 0) {
               $error_message = "Email already exists. Please use a different email.";
               mysqli_stmt_close($check_email);
           }
           else {
               mysqli_stmt_close($check_email);
               
        
               $passHash = password_hash($pass, PASSWORD_DEFAULT);

               mysqli_begin_transaction($conn);
               
               try {
                   $stmt = mysqli_prepare($conn, "INSERT INTO registration (name, email, password) VALUES (?, ?, ?)");
                   mysqli_stmt_bind_param($stmt, "sss", $name, $email, $passHash);
                   
                   if(mysqli_stmt_execute($stmt)){
                       $user_id = mysqli_insert_id($conn);
                       $_SESSION["user_id"] = $user_id;
                       mysqli_stmt_close($stmt);
                       
            
                       $stmt2 = mysqli_prepare($conn, "INSERT INTO account_info (user_id, has_default_destination, has_completed_setup) VALUES (?, 0, 0)");
                       mysqli_stmt_bind_param($stmt2, "i", $user_id);
                       
                       if(mysqli_stmt_execute($stmt2)){
                           mysqli_stmt_close($stmt2);
                           
                          
                           mysqli_commit($conn);
                           
                           echo "<script>
                               alert('Registered Successfully!');
                               window.location.href = '../account_setup/account_setup.php';
                           </script>";
                           exit;
                       } else {
                           throw new Exception("Failed to insert account information");
                       }
                   } else {
                       throw new Exception("Failed to register user");
                   }
               } catch (Exception $e) {
                 
                   mysqli_rollback($conn);
                   $error_message = "Registration failed: " . $e->getMessage();
               }
           }
       }
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Lugar Lang</title>
    <link rel="stylesheet" href="register.css">
    <style>
        .error-alert {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create an account</h2>
        <p>Enter your details to sign up for Lugar Lang!</p>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-alert"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" id="signupForm" action="registration_page.php" enctype="multipart/form-data">
            <div class="form-item">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullName" placeholder="John Doe" value="<?php echo htmlspecialchars($name); ?>" required>
            <div class="error" id="fullNameError"></div>
            </div>
            
            <div class="form-item">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="youremail@usc.edu.ph" value="<?php echo htmlspecialchars($email); ?>" required>
            <div class="error" id="emailError"></div>
            </div>
            
            <div class="form-item">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="At least 8 characters" required>
            <div class="error" id="passwordError"></div>
            </div>
            
            <div class="form-item">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password" required>
            <div class="error" id="confirmPasswordError"></div>
            </div>
            
            <button type="submit" class="submit-button" id="submitButton" name="submitBtn">Sign Up</button>
        </form>
        
        <div class="login-redirect">
            Already have an account?
            <a href="../login/login_page.php" class="login-link">Log in</a>
        </div>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(event) {
            const email = document.getElementById('email').value.trim();
            const emailError = document.getElementById('emailError');
            
            if (!email.toLowerCase().endsWith('@usc.edu.ph')) {
                emailError.textContent = 'Please use a valid USC email address (@usc.edu.ph)';
                event.preventDefault();
            } else {
                emailError.textContent = '';
            }
        });
    </script>
</body>
</html>