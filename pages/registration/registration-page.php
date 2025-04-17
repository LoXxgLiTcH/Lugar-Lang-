<?php
   session_start();
   $db_server = "localhost";
   $db_user = "root";
   $db_pass = "";
   $db_name = "lugarlangdb";
   $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

   if(!$conn){
       echo "<script>alert('Database connection failed. Please try again later.');</script>";
       exit;
   }

   if(isset($_POST["submitBtn"])){
       $name = $_POST["fullName"];
       $email = $_POST["email"];
       $pass = $_POST["password"];
       $confPass = $_POST["confirmPassword"];
       
       $passHash = password_hash($pass, PASSWORD_DEFAULT);

       $stmt = mysqli_prepare($conn, "INSERT INTO registration (name, email, password) VALUES ( ?, ?, ? )") ;
       mysqli_stmt_bind_param($stmt, "sss", $name, $email, $passHash);

       if (empty($name) || empty($email) || empty($pass) || empty($confPass)) {
           echo "<script>alert('All fields are required.')</script>";
           exit;
       }

       if(mysqli_stmt_execute($stmt)){
           $user_id = mysqli_insert_id($conn);
           $_SESSION["user_id"] = $user_id;
       
           $stmt2 = mysqli_prepare($conn, "INSERT INTO account_info (user_id) VALUES (?)") ;
           mysqli_stmt_bind_param($stmt2, "i", $user_id);

           if(mysqli_stmt_execute($stmt2)){
               echo "<script>alert('Your Files are Uploaded')</script>";
               mysqli_stmt_close($stmt2);
           }
           else{
               echo "<script>alert('Failed to insert in Account Setup')</script>";
           }
       }
       else{
           echo "<script>alert('Something is Wrong')</script>";
       }
       mysqli_stmt_close($stmt);
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
        
        <form method="POST" id="signupForm" action="registration-page.php" enctype="multipart/form-data">
            <div class="form-item">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" placeholder="John Doe">
                <div class="error" id="fullNameError"></div>
            </div>
            
            <div class="form-item">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="john@example.com">
                <div class="error" id="emailError"></div>
            </div>
            
            <div class="form-item">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="At least 8 characters">
                <div class="error" id="passwordError"></div>
            </div>
            
            <div class="form-item">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password">
                <div class="error" id="confirmPasswordError"></div>
            </div>
            
            <button type="submit" class="submit-button" id="submitButton" name="submitBtn">Sign Up</button>
        </form>
        
        <div class="login-redirect">
            Already have an account?
            <a href="../login/login-page.php" class="login-link">Log in</a>
        </div>
    </div>
    <script src="../js/registration.js"></script>
</body>
</html>