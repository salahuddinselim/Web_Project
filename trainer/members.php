<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];
$members = getTrainerMembers($trainer_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Member Details Trainer</title>
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background-color: #0e0e0e;
        color: #ffffff;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .mainContainer {
        display: flex;
        width: 95%;
        height: 90vh;
        background-color: #121212;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        background-color: #0d110d;
        padding: 30px 20px;
        border-right: 1px solid #222;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 50px;
    }

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #333;
        overflow: hidden;
    }

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-name {
        font-weight: bold;
        font-size: 16px;
    }

    .menu {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 15px;
        color: #aaa;
        text-decoration: none;
        font-size: 14px;
        padding: 10px;
        border-radius: 5px;
        transition: 0.3s;
    }

    .menu-item:hover,
    .menu-item.active {
        background-color: #1f261f;
        color: white;
    }

    .icon {
        width: 20px;
        text-align: center;
    }

    /* Main Content Styling */
    .mainContent {
        flex: 1;
        padding: 40px;
        overflow-y: auto;
        background-color: #0e0e0e;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h2 {
        margin: 0;
        font-size: 28px;
        font-weight: bold;
    }

    .header-stats {
        display: flex;
        gap: 20px;
    }

    .stat-box {
        background-color: #1a201a;
        padding: 15px 25px;
        border-radius: 8px;
        border: 1px solid #2a3830;
        text-align: center;
    }

    .stat-value {
        display: block;
        font-size: 24px;
        font-weight: bold;
        color: #00d26a;
        margin-bottom: 5px;
    }

    .stat-label {
        display: block;
        font-size: 12px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Toolbar */
    .toolbar {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        align-items: center;
    }

    .search-box {
        flex: 1;
        max-width: 400px;
        position: relative;
        display: flex;
        align-items: center;
        background-color: #1a201a;
        border: 1px solid #2a3830;
        border-radius: 8px;
        padding: 10px 15px;
    }

    .search-icon {
        margin-right: 10px;
        font-size: 16px;
    }

    .search-box input {
        flex: 1;
        background: none;
        border: none;
        color: white;
        outline: none;
        font-size: 14px;
    }

    .search-box input::placeholder {
        color: #666;
    }

    .filter-select {
        background-color: #1a201a;
        border: 1px solid #2a3830;
        border-radius: 8px;
        padding: 10px 15px;
        color: white;
        font-size: 14px;
        outline: none;
        cursor: pointer;
    }

    .filter-select option {
        background-color: #1a201a;
        color: white;
    }

    /* Members Grid */
    .members-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }

    /* Member Card */
    .member-card {
        background-color: #1a201a;
        border: 1px solid #2a3830;
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
    }

    .member-card:hover {
        transform: translateY(-5px);
        border-color: #00d26a;
        box-shadow: 0 10px 30px rgba(0, 210, 106, 0.1);
    }

    /* Card Header */
    .card-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #2a3830;
    }

    .member-avatar {
        position: relative;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #00d26a;
    }

    .member-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .status-dot {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #1a201a;
    }

    .status-dot.active {
        background-color: #00d26a;
    }

    .member-info h3 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
    }

    .membership-badge {
        display: inline-block;
        padding: 3px 10px;
        background-color: #2a352a;
        color: #00d26a;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Card Stats */
    .card-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 10px;
    }

    .stat-item {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
        background-color: #15201a;
        padding: 10px;
        border-radius: 8px;
    }

    .stat-icon {
        font-size: 20px;
    }

    .stat-content {
        display: flex;
        flex-direction: column;
    }

    .stat-number {
        font-size: 16px;
        font-weight: bold;
        color: white;
        line-height: 1;
    }

    .stat-text {
        font-size: 10px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Card Actions */
    .card-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 12px;
        border-radius: 6px;
        border: 1px solid #2a3830;
        background-color: transparent;
        color: #aaa;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .action-btn:hover {
        background-color: #2a3830;
        color: white;
        border-color: #00d26a;
    }

    .btn-primary {
        grid-column: 1 / -1;
        background-color: #2a352a;
        border-color: #00d26a;
        color: #00d26a;
    }

    .btn-primary:hover {
        background-color: #00d26a;
        color: black;
    }

    .btn-chat {
        grid-column: 1 / -1;
    }

    .btn-icon {
        font-size: 14px;
    }

    .btn-text {
        font-size: 12px;
    }

    .count-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #00d26a;
        color: black;
        font-size: 10px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background-color: #1a201a;
        border: 1px dashed #2a3830;
        border-radius: 12px;
    }

    .empty-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #ccc;
    }

    .empty-state p {
        color: #888;
        font-size: 14px;
    }

    /* Member Detail Modal */
    .member-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .member-modal.active {
        display: flex;
    }
    .modal-content {
        background: #1a201a;
        border-radius: 12px;
        width: 100%;
        max-width: 700px;
        max-height: 85vh;
        overflow-y: auto;
        border: 1px solid #2a3830;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        border-bottom: 1px solid #2a3830;
        background: #15201a;
    }
    .modal-header h3 {
        margin: 0;
        color: #00d26a;
        font-size: 18px;
    }
    .modal-close {
        background: none;
        border: none;
        color: #888;
        font-size: 24px;
        cursor: pointer;
    }
    .modal-close:hover {
        color: #fff;
    }
    .modal-body {
        padding: 25px;
    }
    .modal-body .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .modal-body .data-table th,
    .modal-body .data-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #2a3830;
    }
    .modal-body .data-table th {
        background: #15201a;
        color: #00d26a;
        font-size: 12px;
        text-transform: uppercase;
    }
    .modal-body .data-table td {
        color: #ccc;
        font-size: 14px;
    }
    .modal-body .empty-msg {
        text-align: center;
        color: #888;
        padding: 40px;
    }
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .profile-item {
        background: #15201a;
        padding: 15px;
        border-radius: 8px;
    }
    .profile-item label {
        display: block;
        font-size: 11px;
        color: #888;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    .profile-item span {
        font-size: 16px;
        color: #fff;
    }
    </style>
</head>

<body>
    <div class="mainContainer">
        <?php include __DIR__ . '/../includes/trainer_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="mainContent">
            <!-- Page Header with Stats -->
            <div class="page-header">
                <h2>My Members</h2>
                <div class="header-stats">
                    <div class="stat-box">
                        <span class="stat-value"><?php echo count($members); ?></span>
                        <span class="stat-label">Total Members</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value"><?php echo count(array_filter($members, function ($m) {
                                    return true;
                                  })); ?></span>
                        <span class="stat-label">Active</span>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="toolbar">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="Search members by name..."
                        onkeyup="filterMembers()">
                </div>
                <div class="filter-group">
                    <select id="filterStatus" onchange="filterMembers()" class="filter-select">
                        <option value="all">All Members</option>
                        <option value="active">Active Only</option>
                        <option value="with-routine">Has Routines</option>
                        <option value="with-diet">Has Diet Plan</option>
                    </select>
                </div>
            </div>

            <!-- Members Grid -->
            <div class="members-grid" id="membersGrid">
                <?php if (empty($members)): ?>
                <div class="empty-state">
                    <div class="empty-icon">👥</div>
                    <h3>No Members Assigned</h3>
                    <p>You don't have any members assigned to you yet.</p>
                </div>
                <?php else: ?>
                <?php foreach ($members as $m):
            // Get member's data
            $dietPlans = getMemberDietPlans($m['member_id'], null);
            $routines = getMemberRoutines($m['member_id'], true);
            $routineCount = count($routines);
            $dietCount = count($dietPlans);
            $profilePic = !empty($m['profile_picture']) ? '../uploads/profile_pics/' . $m['profile_picture'] : '../images/default_avatar.jpg';
          ?>
                <div class="member-card" data-name="<?php echo strtolower($m['full_name']); ?>"
                    data-has-routine="<?php echo $routineCount > 0 ? 'yes' : 'no'; ?>"
                    data-has-diet="<?php echo $dietCount > 0 ? 'yes' : 'no'; ?>">
                    <!-- Card Header -->
                    <div class="card-header">
                        <div class="member-avatar">
                            <img src="<?php echo htmlspecialchars($profilePic); ?>"
                                alt="<?php echo htmlspecialchars($m['full_name']); ?>">
                            <div class="status-dot active"></div>
                        </div>
                        <div class="member-info">
                            <h3><?php echo htmlspecialchars($m['full_name']); ?></h3>
                            <span
                                class="membership-badge"><?php echo ucfirst($m['membership_type'] ?? 'Basic'); ?></span>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card-stats">
                        <div class="stat-item">
                            <span class="stat-icon">🧘</span>
                            <div class="stat-content">
                                <span class="stat-number"><?php echo $routineCount; ?></span>
                                <span class="stat-text">Routines</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">🥗</span>
                            <div class="stat-content">
                                <span class="stat-number"><?php echo $dietCount; ?></span>
                                <span class="stat-text">Diet Plans</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">📊</span>
                            <div class="stat-content">
                                <span class="stat-number">--</span>
                                <span class="stat-text">Progress</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card-actions">
                        <button class="action-btn btn-primary"
                            onclick="viewMemberDetails(<?php echo $m['member_id']; ?>, '<?php echo addslashes($m['full_name']); ?>')">
                            <span class="btn-icon">👤</span>
                            <span class="btn-text">Profile</span>
                        </button>
                        <button class="action-btn btn-secondary"
                            onclick="manageRoutines(<?php echo $m['member_id']; ?>, '<?php echo addslashes($m['full_name']); ?>', <?php echo $routineCount; ?>)">
                            <span class="btn-icon">🧘</span>
                            <span class="btn-text">Routines</span>
                            <?php if ($routineCount > 0): ?>
                            <span class="count-badge"><?php echo $routineCount; ?></span>
                            <?php endif; ?>
                        </button>
                        <button class="action-btn btn-secondary"
                            onclick="manageDiet(<?php echo $m['member_id']; ?>, '<?php echo addslashes($m['full_name']); ?>', <?php echo $dietCount; ?>)">
                            <span class="btn-icon">🥗</span>
                            <span class="btn-text">Diet</span>
                            <?php if ($dietCount > 0): ?>
                            <span class="count-badge"><?php echo $dietCount; ?></span>
                            <?php endif; ?>
                        </button>
                        <button class="action-btn btn-secondary"
                            onclick="viewProgress(<?php echo $m['member_id']; ?>, '<?php echo addslashes($m['full_name']); ?>')">
                            <span class="btn-icon">📈</span>
                            <span class="btn-text">Progress</span>
                        </button>
                        <button class="action-btn btn-chat"
                            onclick="window.location.href='chat.php?member_id=<?php echo $m['user_id']; ?>'">
                            <span class="btn-icon">💬</span>
                            <span class="btn-text">Chat</span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
    </div>

    <!-- Member Detail Modal -->
    <div id="memberModal" class="member-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Member Details</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="empty-msg">Loading...</div>
            </div>
        </div>
    </div>

    <script>
    // Filter members by search and status
    function filterMembers() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const filterStatus = document.getElementById('filterStatus').value;
        const cards = document.querySelectorAll('.member-card');

        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const hasRoutine = card.getAttribute('data-has-routine');
            const hasDiet = card.getAttribute('data-has-diet');

            let matchesSearch = name.includes(searchTerm);
            let matchesFilter = true;

            if (filterStatus === 'with-routine') {
                matchesFilter = hasRoutine === 'yes';
            } else if (filterStatus === 'with-diet') {
                matchesFilter = hasDiet === 'yes';
            }

            if (matchesSearch && matchesFilter) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
    }

    function openModal(title) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalBody').innerHTML = '<div class="empty-msg">Loading...</div>';
        document.getElementById('memberModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('memberModal').classList.remove('active');
    }

    // Close modal on outside click
    document.getElementById('memberModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // View member profile details
    function viewMemberDetails(memberId, memberName) {
        openModal('Profile: ' + memberName);
        fetch('../handlers/trainer/get_member_data.php?member_id=' + memberId + '&type=profile')
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('modalBody').innerHTML = '<div class="empty-msg">' + data.error + '</div>';
                    return;
                }
                const m = data.member;
                document.getElementById('modalBody').innerHTML = `
                    <div class="profile-grid">
                        <div class="profile-item"><label>Full Name</label><span>${m.full_name || '-'}</span></div>
                        <div class="profile-item"><label>Email</label><span>${m.email || '-'}</span></div>
                        <div class="profile-item"><label>Phone</label><span>${m.phone || '-'}</span></div>
                        <div class="profile-item"><label>Membership</label><span>${m.membership_type || 'Basic'}</span></div>
                        <div class="profile-item"><label>Join Date</label><span>${m.join_date || '-'}</span></div>
                        <div class="profile-item"><label>Gender</label><span>${m.gender || '-'}</span></div>
                    </div>
                `;
            });
    }

    // Manage member routines
    function manageRoutines(memberId, memberName, routineCount) {
        openModal('Routines: ' + memberName);
        fetch('../handlers/trainer/get_member_data.php?member_id=' + memberId + '&type=routines')
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('modalBody').innerHTML = '<div class="empty-msg">' + data.error + '</div>';
                    return;
                }
                const routines = data.routines || [];
                if (routines.length === 0) {
                    document.getElementById('modalBody').innerHTML = '<div class="empty-msg">No routines assigned yet.</div>';
                    return;
                }
                let html = '<table class="data-table"><thead><tr><th>Title</th><th>Type</th><th>Difficulty</th><th>Duration</th></tr></thead><tbody>';
                routines.forEach(r => {
                    html += `<tr><td>${r.title || '-'}</td><td>${r.routine_type || '-'}</td><td>${r.difficulty_level || '-'}</td><td>${r.duration_minutes || '-'} min</td></tr>`;
                });
                html += '</tbody></table>';
                document.getElementById('modalBody').innerHTML = html;
            });
    }

    // Manage member diet plans
    function manageDiet(memberId, memberName, dietCount) {
        openModal('Diet Plans: ' + memberName);
        fetch('../handlers/trainer/get_member_data.php?member_id=' + memberId + '&type=diet')
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('modalBody').innerHTML = '<div class="empty-msg">' + data.error + '</div>';
                    return;
                }
                const plans = data.diet_plans || [];
                if (plans.length === 0) {
                    document.getElementById('modalBody').innerHTML = '<div class="empty-msg">No diet plans assigned yet.</div>';
                    return;
                }
                let html = '<table class="data-table"><thead><tr><th>Date</th><th>Meal Time</th><th>Meal Name</th><th>Calories</th></tr></thead><tbody>';
                plans.forEach(p => {
                    html += `<tr><td>${p.plan_date || '-'}</td><td>${p.meal_time || '-'}</td><td>${p.meal_name || '-'}</td><td>${p.calories || '-'}</td></tr>`;
                });
                html += '</tbody></table>';
                document.getElementById('modalBody').innerHTML = html;
            });
    }

    // View member progress
    function viewProgress(memberId, memberName) {
        openModal('Progress: ' + memberName);
        fetch('../handlers/trainer/get_member_data.php?member_id=' + memberId + '&type=progress')
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('modalBody').innerHTML = '<div class="empty-msg">' + data.error + '</div>';
                    return;
                }
                const progress = data.progress || [];
                if (progress.length === 0) {
                    document.getElementById('modalBody').innerHTML = '<div class="empty-msg">No progress data logged yet.</div>';
                    return;
                }
                let html = '<table class="data-table"><thead><tr><th>Date</th><th>Weight (kg)</th><th>Heart Rate</th><th>Sleep (hrs)</th><th>Mood</th></tr></thead><tbody>';
                progress.forEach(p => {
                    html += `<tr><td>${p.tracking_date || '-'}</td><td>${p.weight_kg || '-'}</td><td>${p.heart_rate || '-'}</td><td>${p.sleep_hours || '-'}</td><td>${p.mood || '-'}</td></tr>`;
                });
                html += '</tbody></table>';
                document.getElementById('modalBody').innerHTML = html;
            });
    }
    </script>
</body>

</html>
