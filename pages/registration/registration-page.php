<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Lugar Lang</title>
    <link rel="stylesheet" href="registration.css">
</head>
<body>
    <div class="form-container">
        <h2>Create an account</h2>
        <p>Enter your details to sign up for Lugar Lang!</p>
        
        <form id="signupForm">
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
            
            <button type="submit" class="submit-button" id="submitButton">Sign Up</button>
        </form>
        
        <div class="login-redirect">
            Already have an account?
            <a href="/login" class="login-link">Log in</a>
        </div>
    </div>

    <script src="../js/registration.js"></script>
</body>
</html>