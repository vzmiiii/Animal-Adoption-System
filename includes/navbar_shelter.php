<!-- Sidebar for Shelter -->
<div id="sidebar" class="sidebar">
    <a href="/animal_adoption_system/shelter/add_pet.php">Add New Pet</a>
    <a href="/animal_adoption_system/shelter/manage_pet_profiles.php">Manage Pet Profiles</a>
    <a href="/animal_adoption_system/shelter/adopted_pets.php">View Adopted Pets</a>
    <a href="/animal_adoption_system/shelter/view_applications.php">View Adoption Applications</a>
    <a href="/animal_adoption_system/shelter/follow_up_reminders.php">Send Follow-Up Reminders</a>
    <a href="/animal_adoption_system/shelter/follow_up_history.php">Follow-Up History</a>
    <a href="/animal_adoption_system/shelter/setting.php">My Settings</a>
    <a href="/animal_adoption_system/logout.php">Logout</a>
</div>

<!-- Navbar with toggle -->
<div class="navbar">
    <div class="left-nav">
        <button class="sidebar-toggle" onclick="toggleSidebar(event)">☰</button>
        <div class="logo">Animal Adoption System</div>
    </div>
    <nav>
        <a href="/animal_adoption_system/shelter/dashboard.php">Home</a>
        <a href="/animal_adoption_system/shelter/setting.php">Settings</a>
        <a href="/animal_adoption_system/logout.php">Log Out</a>
    </nav>
</div>

<!-- Sidebar CSS -->
<link rel="stylesheet" href="/animal_adoption_system/css/sidebar.css">

<!-- JS for toggle + outside click -->
<script>
function toggleSidebar(event) {
    event.stopPropagation();
    const sidebar = document.getElementById('sidebar');
    sidebar.style.left = sidebar.style.left === '0px' ? '-250px' : '0px';
}
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.sidebar-toggle');
    if (sidebar.style.left === '0px' &&
        !sidebar.contains(event.target) &&
        !toggle.contains(event.target)) {
        sidebar.style.left = '-250px';
    }
});
</script>

