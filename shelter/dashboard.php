<?php
session_start();

// Redirect if not shelter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shelter Dashboard - Animal Adoption System</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        :root {
            --accent-gradient: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            --text-color: #333;
            --text-color-light: #555;
            --container-bg: rgba(255, 255, 255, 0.92);
            --border-color: #e0e0e0;
            --shadow: 0 8px 25px rgba(0,0,0,0.1);
            --border-radius: 16px;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-color);
        }

        .dashboard-wrapper {
            max-width: 900px;
            margin: 80px auto 40px;
            padding: 40px;
            background: var(--container-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            
            text-align: center;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .dashboard-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 10px 0;
        }
        
        .dashboard-header p {
            font-size: 16px;
            color: var(--text-color-light);
            margin: 0;
        }

        .notif-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 40px;
            font-size: 16px;
            font-weight: 500;
            color: #34495e;
            padding: 12px 20px;
            border-radius: 12px;
            background-color: #ecf0f1;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .notif-link:hover {
            background-color: #dbe4e8;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .dashboard-links {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .dashboard-links a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            border-radius: var(--border-radius);
            background: #fff;
            color: var(--text-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
            text-align: left;
        }

        .dashboard-links a:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
            color: #4e8cff;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
        <p>Manage your shelter's pets, applications, and interviews from here.</p>
    </div>

    <a href="notifications.php" class="notif-link">🔔 View All Notifications</a>

    <ul class="dashboard-links">
        <li><a href="manage_pet_profiles.php">📂 Manage Pet Profiles</a></li>
        <li><a href="add_pet.php">➕ Add New Pet</a></li>
        <li><a href="view_applications.php">📋 View Adoption Applications</a></li>
        <li><a href="interview_requests.php">📅 Interview Requests</a></li>
        <li><a href="scheduled_interviews.php">🗓️ Interview Schedule</a></li>
        <li><a href="follow_up_reminders.php">📨 Send Follow-Ups</a></li>
        <li><a href="follow_up_history.php">🕓 Follow-Up History</a></li>
        <li><a href="adopted_pets.php">✅ View Adopted Pets</a></li>
        <li><a href="setting.php">⚙️ My Settings</a></li>
        <li><a href="/animal_adoption_system/logout.php">🚪 Logout</a></li>
    </ul>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
