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
<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <a href="/animal_adoption_system/adopter/browse_available_pets.php">🐾 Browse Available Pets</a>
    <a href="/animal_adoption_system/adopter/adoption_tracker.php">📝 Track My Applications</a>
    <a href="/animal_adoption_system/adopter/cancel_application.php">❌ Cancel Application</a>
    <a href="/animal_adoption_system/adopter/followup_messages.php">📩 Follow-Up Messages</a>
    <a href="/animal_adoption_system/adopter/compatibility_quiz.php">🧩 Take Compatibility Quiz</a>
    <a href="/animal_adoption_system/adopter/interview_status.php">📅 My Interview Status</a>
    <a href="/animal_adoption_system/adopter/setting.php">⚙️ My Settings</a>
    <a href="/animal_adoption_system/logout.php">📕 Logout</a>
</div>

<!-- Navbar with Sidebar Toggle -->
<div class="navbar">
    <div class="left-nav">
        <button class="sidebar-toggle" onclick="toggleSidebar(event)">☰</button>

        <div class="logo">
            <img src="../images/pawprint.png" alt="Logo" class="logo-img">
            <span class="logo-text">Animal Adoption System</span>
        </div>
    </div>
    <nav>
        <a href="/animal_adoption_system/adopter/dashboard.php">Home</a>
        <a href="/animal_adoption_system/adopter/faq.php">FAQ</a>
        <a href="/animal_adoption_system/adopter/notifications.php">🔔 Notifications (<?= $unread_count ?>)</a>
        <a href="/animal_adoption_system/adopter/setting.php">Settings</a>
        <a href="/animal_adoption_system/logout.php">Log Out</a>
    </nav>
</div>

<link rel="stylesheet" href="/animal_adoption_system/css/sidebar.css">

<script>
function toggleSidebar(event) {
    event.stopPropagation();
    const sidebar = document.getElementById('sidebar');
    const isOpen = sidebar.style.left === '0px';
    sidebar.style.left = isOpen ? '-250px' : '0px';
}
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.querySelector('.sidebar-toggle');
    if (sidebar.style.left === '0px' &&
        !sidebar.contains(event.target) &&
        !toggleButton.contains(event.target)) {
        sidebar.style.left = '-250px';
    }
});
</script>
