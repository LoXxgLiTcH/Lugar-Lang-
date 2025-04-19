
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
        
        <form id="loginForm" method="POST">
            <div class="form-item">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="john@example.com" required>
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
    <?php
    session_start();
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "lugarlangdb";
    
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    try {
        $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
        $conn->set_charset("utf8mb4");
    } catch (Exception $e) {
        echo "<script>alert('Database connection failed. Please try again later.');</script>";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = mysqli_prepare($conn, "SELECT id, password FROM registration WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $user_id, $hashed_password);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($user_id && password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;
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
    