<?php
include('../db_connection.php');
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$notif_sql = $conn->prepare("SELECT COUNT(*) AS unread FROM notifications WHERE user_id = ? AND role = ? AND is_read = 0");
$notif_sql->bind_param("is", $user_id, $role);
$notif_sql->execute();
$notif_result = $notif_sql->get_result()->fetch_assoc();
$unread_count = $notif_result['unread'];
?>
<!-- Sidebar for Shelter -->
<div id="sidebar" class="sidebar">
    <a href="/animal_adoption_system/shelter/add_pet.php">➕ Add New Pet</a>
    <a href="/animal_adoption_system/shelter/manage_pet_profiles.php">📂 Manage Pet Profiles</a>
    <a href="/animal_adoption_system/shelter/adopted_pets.php">✅ View Adopted Pets</a>
    <a href="/animal_adoption_system/shelter/view_applications.php">📋 View Adoption Applications</a>
    <a href="/animal_adoption_system/shelter/interview_requests.php">📅 Interview Requests</a>
    <a href="/animal_adoption_system/shelter/scheduled_interviews.php">🗓️ Scheduled Interviews</a>
    <a href="/animal_adoption_system/shelter/follow_up_reminders.php">📨 Send Follow-Up Reminders</a>
    <a href="/animal_adoption_system/shelter/follow_up_history.php">🕓 Follow-Up History</a>
    <a href="/animal_adoption_system/shelter/setting.php">⚙️ My Settings</a>
    <a href="/animal_adoption_system/logout.php">🚪 Logout</a>
</div>

<div class="navbar">
    <div class="left-nav">
        <button class="sidebar-toggle" onclick="toggleSidebar(event)">☰</button>
        <div class="logo">
            <img src="../images/pawprint.png" alt="Logo" class="logo-img">
            <span class="logo-text">Animal Adoption System</span>
        </div>
    </div>
    <nav>
        <a href="/animal_adoption_system/shelter/dashboard.php">Home</a>
        <a href="/animal_adoption_system/shelter/notifications.php">🔔 Notifications (<?= $unread_count ?>)</a>
        <a href="/animal_adoption_system/shelter/setting.php">Settings</a>
        <a href="/animal_adoption_system/logout.php">Log Out</a>
    </nav>
</div>

<link rel="stylesheet" href="/animal_adoption_system/css/sidebar.css">

<script>
function toggleSidebar(event) {
    event.stopPropagation();
    const sidebar = document.getElementById('sidebar');
    sidebar.style.left = sidebar.style.left === '0px' ? '-280px' : '0px';
}
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.sidebar-toggle');
    if (sidebar.style.left === '0px' &&
        !sidebar.contains(event.target) &&
        !toggle.contains(event.target)) {
        sidebar.style.left = '-280px';
    }
});
</script>
