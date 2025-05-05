<?php
session_start();


try {
    $conn = new mysqli("localhost", "root", "", "lugarlangdb");
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    echo "<script>alert('Database connection failed. Please try again later.');</script>";
        exit;
    }



$user_id = $_SESSION["user_id"] ?? null;
$userName = $_SESSION['user_name'] ?? 'User Name';
$userEmail = $_SESSION['user_email'] ?? 'user@example.com';
$userProfilePic = 'images/default-avatar.png';

if ($user_id) {
    $stmt = $conn->prepare("SELECT photo FROM account_info WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($photo);
    if ($stmt->fetch() && $photo) {
        $userProfilePic = '../../profile_uploads/' . $photo;
    }
    $stmt->close();
}
?>

<div class="hamburger-container">
    <div class="hamburger-icon" id="hamburgerIcon">
        <span></span>
        <span></span>
        <a href="../home/home.php" class="home-icon">
            <i class="icon-home"></i>
        </a>
        <span></span>
    </div>
    <div class="user-menu" id="userMenu">
        <div class="user-profile">
            <div class="profile-pic">
                <img src="<?php echo $userProfilePic; ?>" alt="Profile Picture">
            </div>
            <div class="user-info">
                <h3><?php echo $userName; ?></h3>
                <p><?php echo $userEmail; ?></p>
            </div>
        </div>
        <div class="menu-items">
            <a href="../user_profile/user_profile.php" class="menu-item">
                <i class="icon-user"></i> Update Profile
            </a>
            <a href="../bookmarks/bookmarks.php" class="menu-item">
                <i class="icon-bookmark"></i> Bookmarked Routes
            </a>
            <a href="../login/login_page.php" class="menu-item logout">
                <i class="icon-logout"></i> Log Out
            </a>
        </div>
    </div>
</div>

<style>
    .hamburger-container {
        position: relative;
    }

    .hamburger-icon {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 30px;
        height: 20px;
        cursor: pointer;
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .hamburger-icon span {
        display: block;
        height: 3px;
        width: 100%;
        background-color: #333;
        border-radius: 3px;
        transition: all 0.3s ease;
    }

    .hamburger-icon.active span:nth-child(1) {
        transform: translateY(8.5px) rotate(45deg);
    }

    .hamburger-icon.active span:nth-child(2) {
        opacity: 0;
    }

    .hamburger-icon.active span:nth-child(3) {
        transform: translateY(-8.5px) rotate(-45deg);
    }

    .user-menu {
        position: fixed;
        top: 70px;
        right: 20px;
        width: 280px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
        display: none;
        z-index: 999;
    }

    .user-menu.active {
        display: block;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user-profile {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .profile-pic {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 15px;
    }

    .profile-pic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-info h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .user-info p {
        margin: 5px 0 0;
        font-size: 14px;
        color: #666;
    }

    .menu-items {
        display: flex;
        flex-direction: column;
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        color: #333;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .menu-item:hover {
        color: #0066cc;
    }

    .menu-item i {
        margin-right: 10px;
        font-size: 18px;
    }

    .logout {
        color: #ff3b30;
    }

    .logout:hover {
        color: #cc2f26;
    }


    .icon-user:before {
        content: "👤";
    }

    .icon-bookmark:before {
        content: "🔖";
    }

    .icon-logout:before {
        content: "🚪";
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const userMenu = document.getElementById('userMenu');


        hamburgerIcon.addEventListener('click', function() {
            this.classList.toggle('active');
            userMenu.classList.toggle('active');
        });


        document.addEventListener('click', function(event) {
            if (!hamburgerIcon.contains(event.target) && !userMenu.contains(event.target)) {
                hamburgerIcon.classList.remove('active');
                userMenu.classList.remove('active');
            }
        });
    });
</script>