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

// Initialize variables to prevent undefined warnings
$username = $year = $photo = $default_campus = $name = $email = '';

$user_id = isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

if ($user_id) {
  $stmt1 = $conn->prepare("SELECT username, year, photo, def_campus FROM account_info WHERE user_id = ?");
  $stmt1->bind_param("i", $user_id);
  $stmt1->execute();
  $result1 = $stmt1->get_result();
  if ($row = $result1->fetch_assoc()) {
    $username = $row['username'];
    $year = $row['year'];
    $photo = $row['photo'];
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
}

$show_modal = false;
if (isset($_SESSION['update_success']) && $_SESSION['update_success']) {
  $show_modal = true;
  unset($_SESSION['update_success']);
}
?>

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
      /* Account for fixed header */
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
      /* Ensure consistent width */
      height: 100px;
      /* Ensure consistent height */
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
  height: 100vh; /* Ensure full viewport height */
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
    <img class="header-icon" alt="Menu Icon" src="../../public/icons/bars-solid.svg" />
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

    <form id="profileForm" method="POST" action="update_profile.php" enctype="multipart/form-data">
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

    <!-- Modal -->
    <div id="confirmModal" class="modal" style="display:none;">
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
    const inputs = document.querySelectorAll('#profileForm input, #profileForm select');
    const modal = document.getElementById('confirmModal');
    const closeModal = document.getElementById('closeModal');

    editBtn.addEventListener('click', () => {
      inputs.forEach(input => input.removeAttribute('readonly'));
      document.getElementById('default_campus').disabled = false;
      document.getElementById('year').disabled = false;
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

    document.getElementById('photoInput').addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          document.getElementById('profilePhoto').src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });

    <?php if ($show_modal): ?>
      window.addEventListener('DOMContentLoaded', () => {
        modal.style.display = 'block';
      });
    <?php endif; ?>
  </script>

</body>

</html>