<?php
session_start();
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "lugarlangdb";

$error_message = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    $error_message = "Database connection failed. Please try again later.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];


    if (!preg_match('/^[\w.%+-]+@usc\.edu\.ph$/i', $email)) {
        $error_message = "Please use a valid USC email address (@usc.edu.ph).";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, password FROM registration WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $user_id, $hashed_password);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($user_id && password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;


            $setup_check = mysqli_prepare($conn, "SELECT has_completed_setup FROM account_info WHERE user_id = ?");
            mysqli_stmt_bind_param($setup_check, "i", $user_id);
            mysqli_stmt_execute($setup_check);
            mysqli_stmt_bind_result($setup_check, $has_completed_setup);
            mysqli_stmt_fetch($setup_check);
            mysqli_stmt_close($setup_check);


            if ($has_completed_setup) {
                header('Location: ../splash/splash.html');
            } else {
                header('Location: ../account_setup/account_setup.php');
            }
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Lugar Lang</title>
    <link rel="stylesheet" href="login.css">
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
        <h2>Log In to Your Account</h2>
        <p>Enter your credentials to access Lugar Lang!</p>

        <?php if (!empty($error_message)): ?>
            <div class="error-alert"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST">
            <div class="form-item">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="youremail@usc.edu.ph" required>
                <div class="error" id="emailError"></div>
            </div>

            <div class="form-item">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Your password" required>
                <div class="error" id="passwordError"></div>
            </div>

            <button type="submit" class="submit-button" id="submitButton">Log In</button>
        </form>

        <div class="signup-redirect">
            Don't have an account?
            <a href="../registration/registration_page.php" class="signup-link">Sign up</a>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
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