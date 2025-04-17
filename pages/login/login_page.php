
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Lugar Lang</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="form-container">
        <h2>Log In to Your Account</h2>
        <p>Enter your credentials to access Lugar Lang!</p>
        
        <form id="loginForm">
            <div class="form-item">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="john@example.com">
                <div class="error" id="emailError"></div>
            </div>
            
            <div class="form-item">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Your password">
                <div class="error" id="passwordError"></div>
            </div>
            
            <button type="submit" class="submit-button" id="submitButton">Log In</button>
        </form>
        
        <div class="signup-redirect">
            <div class="signup-redirect">
                Don't have an account?
                <a href="../registration/registration_page.php" class="signup-link">Sign up</a>
            </div>  </div>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($email === 'test@example.com' && $password === 'password123') {
            header('Location: ../splash/splash.html');
            exit();
        } else {
            echo '<div class="error">Invalid email or password.</div>';
        }
    }
    ?>
    <script src="../../js/auth/login.js"></script>


</body>
</html>
