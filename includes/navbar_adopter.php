<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <a href="/animal_adoption_system/adopter/browse_available_pets.php">🐾 Browse Available Pets</a>
    <a href="/animal_adoption_system/adopter/adoption_tracker.php">📝 Track My Applications</a>
    <a href="/animal_adoption_system/adopter/cancel_application.php">❌ Cancel Application</a>
    <a href="/animal_adoption_system/adopter/followup_messages.php">📩 Follow-Up Messages</a>
    <a href="/animal_adoption_system/adopter/compatibility_quiz.php">🧩 Take Compatibility Quiz</a>
    <a href="/animal_adoption_system/adopter/setting.php">⚙️ My Settings</a>
    <a href="/animal_adoption_system/logout.php">📕 Logout</a>
</div>

<!-- Navbar with Sidebar Toggle -->
<div class="navbar">
    <div class="left-nav">
        <button class="sidebar-toggle" onclick="toggleSidebar(event)">☰</button>
        <div class="logo">Animal Adoption System</div>
    </div>
    <nav>
        <a href="/animal_adoption_system/adopter/dashboard.php">Home</a>
        <a href="/animal_adoption_system/adopter/setting.php">Settings</a>
        <a href="/animal_adoption_system/logout.php">Log Out</a>
    </nav>
</div>

<!-- Link to sidebar.css -->
<link rel="stylesheet" href="/animal_adoption_system/css/sidebar.css">

<!-- Enhanced JavaScript -->
<script>
function toggleSidebar(event) {
    event.stopPropagation(); // Prevent the click from bubbling to document
    const sidebar = document.getElementById('sidebar');
    const isOpen = sidebar.style.left === '0px';
    sidebar.style.left = isOpen ? '-250px' : '0px';
}

// Close sidebar when clicking outside
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.querySelector('.sidebar-toggle');
    if (
        sidebar.style.left === '0px' &&
        !sidebar.contains(event.target) &&
        !toggleButton.contains(event.target)
    ) {
        sidebar.style.left = '-250px';
    }
});
</script>
