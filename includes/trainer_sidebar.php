<?php
/**
 * Trainer Sidebar Component
 * Include this file in all trainer pages
 */

// Ensure user is logged in as trainer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'trainer') {
    header("Location: /Web_Project/html/login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$trainer_name = $_SESSION['full_name'] ?? 'Trainer';
$profile_picture = $_SESSION['profile_picture'] ?? 'default_avatar.jpg';
?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="user-profile">
        <div class="avatar">
            <img src="/Web_Project/images/<?php echo htmlspecialchars($profile_picture); ?>" 
                 alt="<?php echo htmlspecialchars($trainer_name); ?>" 
                 style="background-color: #ccc" />
        </div>
        <div class="user-name"><?php echo htmlspecialchars($trainer_name); ?></div>
    </div>

    <div class="menu">
        <a href="dashboard.php" class="menu-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <span class="icon">🏠</span> Dashboard
        </a>
        <a href="members.php" class="menu-item <?php echo ($current_page == 'members.php') ? 'active' : ''; ?>">
            <span class="icon">👥</span> Members
        </a>
        <a href="routine.php" class="menu-item <?php echo ($current_page == 'routine.php') ? 'active' : ''; ?>">
            <span class="icon">🧘</span> Routines
        </a>
        <a href="diet_plan.php" class="menu-item <?php echo ($current_page == 'diet_plan.php') ? 'active' : ''; ?>">
            <span class="icon">🥗</span> Diet Plans
        </a>
        <a href="progress_logs.php" class="menu-item <?php echo ($current_page == 'progress_logs.php') ? 'active' : ''; ?>">
            <span class="icon">📈</span> Progress Logs
        </a>
        <a href="chat.php" class="menu-item <?php echo ($current_page == 'chat.php') ? 'active' : ''; ?>">
            <span class="icon">💬</span> Chat
        </a>
        <a href="content.php" class="menu-item <?php echo ($current_page == 'content.php') ? 'active' : ''; ?>">
            <span class="icon">📚</span> Content
        </a>
        <a href="profile.php" class="menu-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <span class="icon">👤</span> Profile
        </a>
        <a href="/Web_Project/handlers/logout_handler.php" class="menu-item" style="margin-top: 50px">
            <span class="icon">🚪</span> Logout
        </a>
    </div>
</div>
