<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
  $conn = new mysqli("localhost", "s24103175_lugarlangdb", "lugarlangdb", "s24103175_lugarlangdb");
  $conn->set_charset("utf8mb4");
} catch (Exception $e) {
  echo "<script>alert('Database connection failed. Please try again later.');</script>";
  exit;
}

$username = $year = $photo = $default_campus = $name = $email = '';
$show_modal = false;
$password_message = '';
$password_status = '';

$user_id = isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile' && $user_id) {
  try {

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $year = isset($_POST['year']) ? trim($_POST['year']) : '';
    $default_campus = isset($_POST['default_campus']) ? trim($_POST['default_campus']) : '';


    if (empty($username)) {
      throw new Exception("Username cannot be empty");
    }


    $valid_years = ['1st', '2nd', '3rd', '4th'];
    if (!in_array($year, $valid_years)) {
      throw new Exception("Invalid year selected");
    }


    $valid_campuses = ['talamban', 'downtown'];
    if (!in_array($default_campus, $valid_campuses)) {
      throw new Exception("Invalid campus selected");
    }


    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
      $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
      $file_type = $_FILES['profile_photo']['type'];

      if (!in_array($file_type, $allowed_types)) {
        throw new Exception("Only JPG, PNG, and GIF files are allowed");
      }

      $upload_dir = 'uploads/profile_photos/';
      if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
      }

      $file_name = $user_id . '_' . time() . '_' . basename($_FILES['profile_photo']['name']);
      $target_file = $upload_dir . $file_name;

      if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
        $photo = $target_file;
      } else {
        throw new Exception("Failed to upload profile photo");
      }
    }


    $update_stmt = $conn->prepare("UPDATE account_info SET username = ?, year = ?, def_campus = ?" .
      ($photo ? ", photo = ?" : "") .
      " WHERE user_id = ?");

    if ($photo) {
      $update_stmt->bind_param("ssssi", $username, $year, $default_campus, $photo, $user_id);
    } else {
      $update_stmt->bind_param("sssi", $username, $year, $default_campus, $user_id);
    }

    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0 || $update_stmt->affected_rows === 0) {
      $_SESSION['update_success'] = true;
      header("Location: " . $_SERVER['PHP_SELF']);
      exit;
    } else {
      throw new Exception("Failed to update profile");
    }
  } catch (Exception $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
  }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password' && $user_id) {
  try {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';


    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
      throw new Exception("All password fields are required.");
    }


    $stmt = $conn->prepare("SELECT password FROM registration WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
      $stored_hash = $row['password'];

      if (!password_verify($current_password, $stored_hash)) {
        $password_status = 'error';
        $password_message = "Current password is incorrect.";
      } else if ($new_password !== $confirm_password) {
        $password_status = 'error';
        $password_message = "New passwords do not match.";
      } else if ($current_password === $new_password) {
        $password_status = 'error';
        $password_message = "New password must be different from current password.";
      } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) {
        $password_status = 'error';
        $password_message = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
      } else {

        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $update_stmt = $conn->prepare("UPDATE registration SET password = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $new_hash, $user_id);
        $update_stmt->execute();

        if ($update_stmt->affected_rows > 0) {
          $password_status = 'success';
          $password_message = "Password updated successfully!";
        } else {
          throw new Exception("Failed to update password. Please try again.");
        }
      }
    } else {
      throw new Exception("User account not found.");
    }
  } catch (Exception $e) {
    $password_status = 'error';
    $password_message = "Error: " . $e->getMessage();
  }
}


if ($user_id) {
  try {
    $stmt1 = $conn->prepare("SELECT username, year, photo, def_campus FROM account_info WHERE user_id = ?");
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    if ($row = $result1->fetch_assoc()) {
      $username = $row['username'];
      $year = $row['year'];
      $photo = $row['photo'] ? '../../profile_uploads/' . $row['photo'] : '../../public/images/default_profile.jpg';
      $default_campus = $row['def_campus'];
    }
    $stmt1->close();

    $stmt2 = $conn->prepare("SELECT name, email FROM registration WHERE user_id = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if ($row = $result2->fetch_assoc()) {
      $name = $row['name'];
      $email = $row['email'];
    }
    $stmt2->close();
  } catch (Exception $e) {
    echo "<script>alert('An error occurred while fetching user data: " . $e->getMessage() . "');</script>";
  }
}


if (isset($_SESSION['update_success']) && $_SESSION['update_success']) {
  $show_modal = true;
  unset($_SESSION['update_success']);
}
?>


<?php include '../nav/nav.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Profile | Lugar Lang</title>
  <link rel="stylesheet" href="user-profile.css" />
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

    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--neutral-light);
      color: var(--neutral-dark);
      font-size: 16px;
    }

    .header {
      background-color: var(--primary-blue);
      color: var(--white);
      padding: 0.8rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 10;
      box-shadow: var(--shadow);
    }

    .flex {
      display: flex;
    }

    .align-center {
      align-items: center;
    }

    .gap-1 {
      gap: 0.5rem;
    }

    .header-icon {
      width: 20px;
      height: 20px;
    }

    .header-title {
      font-size: 1.1rem;
      font-weight: bold;
    }

    .container {
      margin: 0.8rem;
      padding: 1rem;
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      margin-top: 3.5rem;

      transition: var(--transition);
    }

    .profile-section {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .profile-photo-container {
      position: relative;
      display: inline-block;
      margin-bottom: 0.5rem;
      width: 100px;

      height: 100px;

    }

    .profile-photo {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--primary-light);
      box-shadow: 0 2px 8px rgba(255, 127, 42, 0.2);
      transition: var(--transition);
    }

    .edit-photo {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(255, 127, 42, 0.8);
      color: var(--white);
      text-align: center;
      font-size: 0.7rem;
      padding: 0.2rem;
      cursor: pointer;
      border-radius: 0 0 50% 50%;
      text-decoration: none;
      transition: var(--transition);
    }

    .edit-photo:hover {
      background: rgba(255, 127, 42, 1);
    }

    .profile-name {
      margin: 0.5rem 0 0;
      font-size: 1.2rem;
      font-weight: bold;
      color: var(--primary-blue);
    }

    .profile-email {
      font-size: 0.8rem;
      color: var(--neutral-medium);
      margin: 0.3rem 0;
      word-break: break-word;
    }

    .info-card {
      padding: 1rem;
      border-top: 1px solid var(--neutral-light);
      margin-top: 0.5rem;
      background-color: var(--white);
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .card-title {
      font-size: 1rem;
      font-weight: bold;
      margin: 0;
      color: var(--primary-blue);
    }

    .info-row {
      margin-bottom: 1rem;
      display: flex;
      flex-direction: column;
    }

    .info-row label {
      font-size: 0.75rem;
      margin-bottom: 0.3rem;
      color: var(--neutral-medium);
      font-weight: 500;
    }

    .info-row input,
    .info-row select {
      padding: 0.8rem;
      border: 1px solid var(--neutral-medium);
      border-radius: var(--radius);
      font-size: 0.9rem;
      background-color: var(--white);
      width: 100%;
      box-sizing: border-box;
      transition: var(--transition);
    }

    .info-row input:focus,
    .info-row select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 2px var(--primary-light);
    }

    input[readonly],
    select:disabled {
      background-color: var(--neutral-light);
      color: var(--neutral-medium);
      cursor: not-allowed;
      border-color: var(--neutral-light);
    }

    .form-actions {
      display: flex;
      justify-content: center;
      gap: 0.8rem;
      margin-top: 1.5rem;
    }

    .btn {
      padding: 0.8rem 1.2rem;
      border: none;
      border-radius: var(--radius);
      font-size: 0.9rem;
      cursor: pointer;
      transition: var(--transition);
      font-weight: 500;
      min-width: 100px;
      box-shadow: var(--shadow);
    }

    .btn-primary {
      background-color: var(--primary);
      color: var(--white);
    }

    .btn-primary:hover,
    .btn-primary:active {
      background-color: var(--accent-orange);
      transform: translateY(-2px);
    }

    .btn-cancel {
      background-color: var(--neutral-light);
      color: var(--neutral-dark);
    }

    .btn-cancel:hover,
    .btn-cancel:active {
      background-color: var(--neutral-medium);
      color: var(--white);
    }

    .password-message {
      padding: 0.5rem;
      margin: 0.5rem 0 1rem;
      border-radius: var(--radius);
      font-size: 0.8rem;
      text-align: center;
    }

    .password-message.error {
      background-color: rgba(239, 83, 80, 0.1);
      color: var(--error);
      border: 1px solid var(--error);
    }

    .password-message.success {
      background-color: var(--light-green);
      color: var(--accent-green);
      border: 1px solid var(--accent-green);
    }

    .password-requirements {
      margin: 0.5rem 0 1rem;
      padding: 0.5rem;
      background-color: var(--neutral-light);
      border-radius: var(--radius);
      font-size: 0.75rem;
    }

    .password-requirements ul {
      margin: 0.5rem 0 0;
      padding-left: 1.5rem;
    }

    .password-requirements li {
      margin-bottom: 0.3rem;
    }

    .modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(30, 58, 138, 0.6);
      display: flex;
      align-content: space-around;
      z-index: 100;
      backdrop-filter: blur(3px);
      -webkit-backdrop-filter: blur(3px);
      height: 100vh;

    }

    .modal-content {
      background: var(--white);
      padding: 1.8rem 1.5rem;
      border-radius: var(--radius);
      text-align: center;
      width: 85%;
      box-shadow: var(--shadow);
      border-top: 3px solid var(--secondary);
      animation: fadeIn 0.3s ease;
      margin: auto;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .modal-content p {
      margin-top: 0;
      margin-bottom: 1.5rem;
      font-size: 1rem;
      color: var(--neutral-dark);
    }

    .modal-content .btn-primary {
      background-color: var(--secondary);
    }

    .modal-content .btn-primary:hover {
      background-color: var(--accent-green);
    }

    .password-strength {
      height: 5px;
      width: 100%;
      background: var(--neutral-light);
      margin-top: 0.5rem;
      border-radius: 10px;
      overflow: hidden;
    }

    .password-strength-meter {
      height: 100%;
      width: 0%;
      transition: width 0.3s ease;
    }

    .strength-weak {
      background-color: var(--error);
      width: 25%;
    }

    .strength-medium {
      background-color: #FFB74D;
      width: 50%;
    }

    .strength-strong {
      background-color: #81C784;
      width: 75%;
    }

    .strength-very-strong {
      background-color: var(--accent-green);
      width: 100%;
    }

    input,
    select,
    button,
    .edit-photo {
      touch-action: manipulation;
    }

    @media (min-width: 600px) {
      body {
        font-size: 18px;
      }

      .container {
        max-width: 500px;
        margin: 5rem auto 2rem;
        padding: 1.5rem;
      }

      .header {
        padding: 1rem;
      }

      .header-icon {
        width: 24px;
        height: 24px;
      }

      .profile-photo {
        width: 100px;
        height: 100px;
      }

      .profile-name {
        font-size: 1.4rem;
      }

      .profile-email {
        font-size: 0.9rem;
      }

      .info-row input,
      .info-row select {
        padding: 0.7rem;
      }

      .btn {
        padding: 0.7rem 1.5rem;
      }

      .modal-content {
        max-width: 400px;
        padding: 2rem;
      }
    }
  </style>
</head>

<body>
  <div class="header">
    <div class="flex align-center gap-1">
      <img class="header-icon" alt="User Profile Icon" src="../../public/icons/users-solid.svg" />
      <span class="header-title">My Profile</span>
    </div>
  </div>

  <div class="container">
    <div class="profile-section">
      <div class="profile-photo-container">
        <img class="profile-photo" id="profilePhoto" alt="Profile Photo" src="<?php echo htmlspecialchars($photo ?? 'default.jpg'); ?>" />
        <input type="file" id="photoInput" name="profile_photo" accept="image/*" style="display: none;" />
        <label for="photoInput" class="edit-photo">Change</label>
      </div>
      <h1 class="profile-name"><?php echo htmlspecialchars($username); ?></h1>
      <p class="profile-email"><?php echo htmlspecialchars($name) . " (" . htmlspecialchars($email) . ")"; ?></p>
    </div>

    <!-- Personal Information Form -->
    <form id="profileForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
      <input type="hidden" name="action" value="update_profile">
      <div class="info-card">
        <div class="card-header">
          <h2 class="card-title">Personal Information</h2>
          <button type="button" id="editBtn" class="btn btn-primary">Edit</button>
        </div>

        <div class="info-row">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
        </div>
        <div class="info-row">
          <label for="year">Year & Program:</label>
          <select id="year" name="year" disabled>
            <option value="1st" <?php if ($year == '1st') echo 'selected'; ?>>1st Year</option>
            <option value="2nd" <?php if ($year == '2nd') echo 'selected'; ?>>2nd Year</option>
            <option value="3rd" <?php if ($year == '3rd') echo 'selected'; ?>>3rd Year</option>
            <option value="4th" <?php if ($year == '4th') echo 'selected'; ?>>4th Year</option>
          </select>
        </div>

        <div class="info-row">
          <label for="default_campus">Default Campus:</label>
          <select id="default_campus" name="default_campus" disabled>
            <option value="talamban" <?php if ($default_campus == 'talamban') echo 'selected'; ?>>Talamban</option>
            <option value="downtown" <?php if ($default_campus == 'downtown') echo 'selected'; ?>>Downtown</option>
          </select>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" id="saveBtn" style="display: none;">Save Changes</button>
          <button type="button" class="btn btn-cancel" id="cancelBtn" style="display: none;">Cancel</button>
        </div>
      </div>
    </form>

    <!-- Password Change Form -->
    <form id="passwordForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
      <input type="hidden" name="action" value="change_password">
      <div class="info-card">
        <div class="card-header">
          <h2 class="card-title">Change Password</h2>
        </div>

        <?php if (!empty($password_message)): ?>
          <div class="password-message <?php echo $password_status; ?>">
            <?php echo htmlspecialchars($password_message); ?>
          </div>
        <?php endif; ?>

        <div class="info-row">
          <label for="current_password">Current Password:</label>
          <input type="password" id="current_password" name="current_password" required>
        </div>

        <div class="info-row">
          <label for="new_password">New Password:</label>
          <input type="password" id="new_password" name="new_password" required>
          <div class="password-strength">
            <div class="password-strength-meter" id="passwordStrength"></div>
          </div>
        </div>

        <div class="info-row">
          <label for="confirm_password">Confirm New Password:</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <div class="password-requirements">
          <p>Password must contain:</p>
          <ul>
            <li id="req-length">At least 8 characters</li>
            <li id="req-uppercase">At least one uppercase letter</li>
            <li id="req-lowercase">At least one lowercase letter</li>
            <li id="req-number">At least one number</li>
            <li id="req-special">At least one special character</li>
          </ul>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" id="changePasswordBtn">Change Password</button>
        </div>
      </div>
    </form>

    <!-- Success Modal -->
    <div id="confirmModal" class="modal" style="display:<?php echo $show_modal ? 'flex' : 'none'; ?>">
      <div class="modal-content">
        <p>Changes saved successfully!</p>
        <button id="closeModal" class="btn btn-primary">OK</button>
      </div>
    </div>
  </div>

  <script>
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const inputs = document.querySelectorAll('#profileForm input:not([type="file"]):not([type="hidden"]), #profileForm select');
    const modal = document.getElementById('confirmModal');
    const closeModal = document.getElementById('closeModal');
    const photoInput = document.getElementById('photoInput');
    const profileForm = document.getElementById('profileForm');

    editBtn.addEventListener('click', () => {
      inputs.forEach(input => {
        if (input.tagName.toLowerCase() === 'input') {
          input.removeAttribute('readonly');
        } else {
          input.disabled = false;
        }
      });
      saveBtn.style.display = 'inline-block';
      cancelBtn.style.display = 'inline-block';
      editBtn.style.display = 'none';
    });

    cancelBtn.addEventListener('click', () => {
      window.location.reload();
    });

    closeModal.addEventListener('click', () => {
      modal.style.display = 'none';
    });

    photoInput.addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          document.getElementById('profilePhoto').src = e.target.result;
        };
        reader.readAsDataURL(file);


        if (editBtn.style.display === 'none') {

        } else {
          editBtn.click();
        }
      }
    });


    document.getElementById('profileForm').addEventListener('submit', function() {

      if (photoInput.files.length > 0) {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.name = 'profile_photo';
        fileInput.files = photoInput.files;
        this.appendChild(fileInput);
      }
    });


    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordStrength = document.getElementById('passwordStrength');
    const changePasswordBtn = document.getElementById('changePasswordBtn');


    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');
    newPasswordInput.addEventListener('input', function() {
      const password = this.value;


      const hasLength = password.length >= 8;
      const hasUppercase = /[A-Z]/.test(password);
      const hasLowercase = /[a-z]/.test(password);
      const hasNumber = /[0-9]/.test(password);
      const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);


      let strength = 0;
      if (hasLength) strength++;
      if (hasUppercase) strength++;
      if (hasLowercase) strength++;
      if (hasNumber) strength++;
      if (hasSpecial) strength++;


      passwordStrength.className = 'password-strength-meter';
      if (password.length === 0) {
        passwordStrength.style.width = '0%';
      } else if (strength <= 2) {
        passwordStrength.classList.add('strength-weak');
        passwordStrength.style.width = '25%';
      } else if (strength === 3) {
        passwordStrength.classList.add('strength-medium');
        passwordStrength.style.width = '50%';
      } else if (strength === 4) {
        passwordStrength.classList.add('strength-strong');
        passwordStrength.style.width = '75%';
      } else {
        passwordStrength.classList.add('strength-very-strong');
        passwordStrength.style.width = '100%';
      }


      validatePasswordMatch();
    });

    confirmPasswordInput.addEventListener('input', validatePasswordMatch);

    function validatePasswordMatch() {
      const newPassword = newPasswordInput.value;
      const confirmPassword = confirmPasswordInput.value;

      if (newPassword !== confirmPassword) {
        confirmPasswordInput.setCustomValidity("Passwords do not match.");
      } else {
        confirmPasswordInput.setCustomValidity("");
      }
    }


    document.getElementById('passwordForm').addEventListener('submit', function(event) {
      if (!newPasswordInput.checkValidity() || !confirmPasswordInput.checkValidity()) {
        event.preventDefault();
        alert("Please ensure all password fields are filled out correctly.");
      }
    });
  </script>
</body>

</html>