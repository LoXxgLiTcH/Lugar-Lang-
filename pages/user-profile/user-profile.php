<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("localhost", "root", "", "lugarlangdb");
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    echo "<script>alert('Database connection failed. Please try again later.');</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Profile | Lugar Lang</title>
  <link rel="stylesheet" href="user-profile.css" />
</head>
<body>
  <div class="header">
    <div class="flex align-center gap-1">
      <img class="header-icon" alt="User Icon" src="../../public/assets/user-solid.svg" />
      <span class="header-title">My Profile</span>
    </div>
    <img class="header-icon" alt="Menu Icon" src="../../public/assets/bars-solid.svg" />
  </div>

  <div class="container">
    <div class="profile-section">
      <div class="profile-photo-container">
        <img class="profile-photo" id="profilePhoto" alt="Profile Photo" src="<?php echo htmlspecialchars($photo ?? '../../public/images/default_profile.jpg'); ?>" />
        <label for="photoInput" class="edit-photo">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
          </svg>
        </label>
        <input type="file" id="photoInput" style="display: none;" accept="image/*" />
      </div>
      <h1 class="profile-name"><?php echo htmlspecialchars($name ?? 'Username'); ?></h1>
      <p class="profile-email"><?php 
        $email_query = "SELECT email FROM account_info WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $email_query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $email);
        mysqli_stmt_fetch($stmt);
        echo htmlspecialchars($email ?? 'user@example.com'); 
        mysqli_stmt_close($stmt);
      ?></p>
    </div>

    <div class="info-card">
      <div class="card-header">
        <h2 class="card-title">Personal Information</h2>
        <button class="edit-button" id="editPersonalBtn">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
          </svg>
          Edit
        </button>
      </div>

      <div id="personalInfo">
        <div class="info-row">
          <div class="info-label">Username</div>
          <div class="info-value"><?php echo htmlspecialchars($name ?? 'Username'); ?></div>
        </div>
        <div class="info-row">
          <div class="info-label">Year & Program</div>
          <div class="info-value"><?php echo htmlspecialchars($year ?? 'Not specified'); ?></div>
        </div>
      </div>

      <form id="personalForm" class="edit-form" method="POST" action="update_profile.php">
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <input type="text" id="username" name="username" class="form-input" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="year">Year & Program</label>
          <input type="text" id="year" name="year" class="form-input" value="<?php echo htmlspecialchars($year ?? ''); ?>" required>
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-cancel" id="cancelPersonalBtn">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>

    <div class="info-card">
      <div class="card-header">
        <h2 class="card-title">Campus Settings</h2>
        <button class="edit-button" id="editCampusBtn">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
          </svg>
          Edit
        </button>
      </div>

      <div id="campusInfo">
        <div class="info-row">
          <div class="info-label">Default Campus</div>
          <div class="info-value"><?php echo htmlspecialchars($defaultCampus ?? 'Not set'); ?></div>
        </div>
      </div>
    <?php
    $campus_query = "SELECT default_campus FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $campus_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $defaultCampus);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    ?>

      <form id="campusForm" class="edit-form" method="POST" action="update_campus.php">
        <div class="form-group">
          <label class="form-label" for="defaultCampus">Default Campus</label>
          <input type="text" id="defaultCampus" name="defaultCampus" class="form-input" value="<?php echo htmlspecialchars($defaultCampus ?? ''); ?>" required>
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-cancel" id="cancelCampusBtn">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>

    <a href="change_password.php" class="action-button primary-button">
      Change Password
    </a>

    <!-- <a href="logout.php" class="action-button logout-button">
      Logout
    </a> -->
  </div>

  <script>

    const editPersonalBtn = document.getElementById('editPersonalBtn');
    const cancelPersonalBtn = document.getElementById('cancelPersonalBtn');
    const personalInfo = document.getElementById('personalInfo');
    const personalForm = document.getElementById('personalForm');

    editPersonalBtn.addEventListener('click', () => {
      personalInfo.style.display = 'none';
      personalForm.style.display = 'block';
    });

    cancelPersonalBtn.addEventListener('click', () => {
      personalForm.style.display = 'none';
      personalInfo.style.display = 'block';
    });

    const editCampusBtn = document.getElementById('editCampusBtn');
    const cancelCampusBtn = document.getElementById('cancelCampusBtn');
    const campusInfo = document.getElementById('campusInfo');
    const campusForm = document.getElementById('campusForm');

    editCampusBtn.addEventListener('click', () => {
      campusInfo.style.display = 'none';
      campusForm.style.display = 'block';
    });

    cancelCampusBtn.addEventListener('click', () => {
      campusForm.style.display = 'none';
      campusInfo.style.display = 'block';
    });

    // Hide all edit forms initially
    personalForm.style.display = 'none';
    campusForm.style.display = 'none';

    // Profile photo preview
    document.getElementById('photoInput').addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('profilePhoto').src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>
