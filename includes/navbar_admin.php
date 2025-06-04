<!-- includes/navbar_admin.php -->
<div class="navbar">
    <div class="left-nav">
        <div class="logo">Animal Adoption System - Admin</div>
    </div>
    <nav class="right-nav">
        <a href="/animal_adoption_system/admin/dashboard.php">Dashboard</a>
        <a href="/animal_adoption_system/admin/manage_users.php">Manage Users</a>
        <a href="/animal_adoption_system/admin/manage_pets.php">Manage Pets</a>
        <a href="/animal_adoption_system/admin/view_all_applications.php">Applications</a>
        <a href="/animal_adoption_system/admin/manage_shelters.php">Shelters</a>
        <a href="/animal_adoption_system/admin/notifications_admin.php">Notifications</a>
        <a href="/animal_adoption_system/logout.php">Logout</a>
    </nav>
</div>

<style>
.navbar {
    background-color: #d5d0b0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', sans-serif;
}
.logo {
    font-weight: bold;
    font-size: 18px;
}
nav.right-nav a {
    margin-left: 20px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
}
nav.right-nav a:hover {
    text-decoration: underline;
}
</style>
