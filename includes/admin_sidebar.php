<?php
/**
 * Admin Sidebar Component
 * Include this file in all admin pages
 */

// Ensure user is logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Web_Project/admin/login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$admin_name = $_SESSION['full_name'] ?? 'Admin';
?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="user-profile">
        <div class="avatar">
            <img src="/Web_Project/images/default_avatar.jpg" 
                 alt="<?php echo htmlspecialchars($admin_name); ?>" 
                 style="background-color: #ccc" />
        </div>
        <div class="user-name"><?php echo htmlspecialchars($admin_name); ?></div>
    </div>

    <div class="menu">
        <a href="dashboard.php" class="menu-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <span class="icon">🏠</span> Dashboard
        </a>
        <a href="add_member.php" class="menu-item <?php echo ($current_page == 'add_member.php') ? 'active' : ''; ?>">
            <span class="icon">➕</span> Add Member
        </a>
        <a href="add_trainer.php" class="menu-item <?php echo ($current_page == 'add_trainer.php') ? 'active' : ''; ?>">
            <span class="icon">➕</span> Add Trainer
        </a>
        <a href="assign_trainer.php" class="menu-item <?php echo ($current_page == 'assign_trainer.php') ? 'active' : ''; ?>">
            <span class="icon">🔗</span> Assign Trainer
        </a>
        <a href="profile.php" class="menu-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <span class="icon">👤</span> Profile
        </a>
        <a href="/Web_Project/handlers/logout_handler.php" class="menu-item" style="margin-top: 50px">
            <span class="icon">🚪</span> Logout
        </a>
    </div>
</div>
