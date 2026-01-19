<?php
/**
 * Member Sidebar Component
 * Include this file in all member pages
 */

// Ensure user is logged in as member
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: /Web_Project/html/login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$member_name = $_SESSION['full_name'] ?? 'Member';
$profile_picture = $_SESSION['profile_picture'] ?? 'default_avatar.jpg';
?>

<!-- Sidebar -->
<div class="sidebar">
    <a href="member_profile.php" class="user-profile" style="text-decoration: none; color: inherit;">
        <div class="avatar">
            <img src="/Web_Project/images/<?php echo htmlspecialchars($profile_picture); ?>" 
                 alt="<?php echo htmlspecialchars($member_name); ?>" 
                 style="background-color: #ccc; width: 100%; height: 100%; object-fit: cover;" />
        </div>
        <div class="user-name"><?php echo htmlspecialchars($member_name); ?></div>
    </a>

    <div class="menu">
        <a href="member_dashboard.php" class="menu-item <?php echo ($current_page == 'member_dashboard.php') ? 'active' : ''; ?>">
            <span class="icon">🏠</span> Dashboard
        </a>
        <a href="member_classes.php" class="menu-item <?php echo ($current_page == 'member_classes.php') ? 'active' : ''; ?>">
            <span class="icon">📅</span> Classes
        </a>
        <a href="member_routines.php" class="menu-item <?php echo ($current_page == 'member_routines.php') ? 'active' : ''; ?>">
            <span class="icon">🧘</span> Routines
        </a>
        <a href="member_diet.php" class="menu-item <?php echo ($current_page == 'member_diet.php') ? 'active' : ''; ?>">
            <span class="icon">🥗</span> Diet
        </a>
        <a href="member_progress.php" class="menu-item <?php echo ($current_page == 'member_progress.php') ? 'active' : ''; ?>">
            <span class="icon">📈</span> Progress
        </a>
        <a href="member_chat.php" class="menu-item <?php echo ($current_page == 'member_chat.php') ? 'active' : ''; ?>">
            <span class="icon">💬</span> Chat
        </a>
        <a href="member_profile.php" class="menu-item <?php echo ($current_page == 'member_profile.php') ? 'active' : ''; ?>">
            <span class="icon">👤</span> Profile
        </a>
        <a href="/Web_Project/handlers/logout_handler.php" class="menu-item" style="margin-top: 50px">
            <span class="icon">🚪</span> Logout
        </a>
    </div>
</div>
