<?php
   session_start();
   $db_server = "localhost";
   $db_user = "root";
   $db_pass = "";
   $db_name = "lugarlangdb";
   
   mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable better error reporting
   
   try {
       $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
       $conn->set_charset("utf8mb4"); // Set charset for better security
   } catch (Exception $e) {
       echo "<script>alert('Database connection failed. Please try again later.');</script>";
       exit;
   }

   if(isset($_POST["submitBtn"])){
       $name = trim($_POST["fullName"]);
       $email = trim($_POST["email"]);
       $pass = $_POST["password"];
       $confPass = $_POST["confirmPassword"];
       
       // Validation
       if (empty($name) || empty($email) || empty($pass) || empty($confPass)) {
           echo "<script>alert('All fields are required.')</script>";
           exit;
       }
       
       // Check if passwords match
       if($pass !== $confPass) {
           echo "<script>alert('Passwords do not match.')</script>";
           exit;
       }
       
       // Check if email already exists
       $check_email = mysqli_prepare($conn, "SELECT email FROM registration WHERE email = ?");
       mysqli_stmt_bind_param($check_email, "s", $email);
       mysqli_stmt_execute($check_email);
       mysqli_stmt_store_result($check_email);
       
       if(mysqli_stmt_num_rows($check_email) > 0) {
           echo "<script>alert('Email already exists. Please use a different email.')</script>";
           mysqli_stmt_close($check_email);
           exit;
       }
       mysqli_stmt_close($check_email);
       
       // Hash password
       $passHash = password_hash($pass, PASSWORD_DEFAULT);

       // Begin transaction to ensure data consistency
       mysqli_begin_transaction($conn);
       
       try {
           // Insert into registration table
           $stmt = mysqli_prepare($conn, "INSERT INTO registration (name, email, password) VALUES (?, ?, ?)");
           mysqli_stmt_bind_param($stmt, "sss", $name, $email, $passHash);
           
           if(mysqli_stmt_execute($stmt)){
               $user_id = mysqli_insert_id($conn);
               $_SESSION["user_id"] = $user_id;
               mysqli_stmt_close($stmt);
               
               // Insert into account_info table with default values explicitly set to 0/false
               $stmt2 = mysqli_prepare($conn, "INSERT INTO account_info (user_id, has_default_destination, has_completed_setup) VALUES (?, 0, 0)");
               mysqli_stmt_bind_param($stmt2, "i", $user_id);
               
               if(mysqli_stmt_execute($stmt2)){
                   mysqli_stmt_close($stmt2);
                   
                   // Commit the transaction
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
           // Rollback transaction on error
           mysqli_rollback($conn);
           echo "<script>alert('Registration failed: " . $e->getMessage() . "')</script>";
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

</head>
<body>
    <div class="form-container">
        <h2>Create an account</h2>
        <p>Enter your details to sign up for Lugar Lang!</p>
        
        <form method="POST" id="signupForm" action="registration_page.php" enctype="multipart/form-data">
            <div class="form-item">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullName" placeholder="John Doe" required>
            <div class="error" id="fullNameError"></div>
            </div>
            
            <div class="form-item">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="john@example.com" required>
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
    <script src="../../js/auth/register.js" ></script> 
</body>
</html>