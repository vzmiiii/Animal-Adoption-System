<?php
session_start();
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
    <style>
        body {
            background-color: #ffffff;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .dashboard-box {
            background-color: #f7e6cf;
            max-width: 700px;
            margin: 80px auto;
            padding: 60px 50px;
            border-radius: 35px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
        }

        .dashboard-box h2 {
            font-size: 30px;
            margin-bottom: 30px;
            font-weight: bold;
            color: #111;
        }

        .notif-link {
            font-size: 14px;
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #000;
            font-weight: bold;
            border: 1px solid #000;
            padding: 10px 18px;
            border-radius: 20px;
            transition: background-color 0.3s ease;
        }

        .notif-link:hover {
            background-color: #fff;
        }

        .dashboard-box ul {
            list-style-type: none;
            padding: 0;
            margin: 0 auto;
            max-width: 500px;
        }

        .dashboard-box li {
            margin-bottom: 20px;
        }

        .dashboard-box a {
            display: inline-block;
            background-color: #ffffff;
            color: #000;
            padding: 16px 28px;
            border-radius: 30px;
            border: 2px solid #000;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .dashboard-box a:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="dashboard-box">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (Shelter)</h2>

    <a href="notifications.php" class="notif-link">🔔 View All Notifications</a>

    <ul>
        <li><a href="add_pet.php">➕ Add New Pet</a></li>
        <li><a href="manage_pet_profiles.php">📂 Manage Pet Profiles</a></li>
        <li><a href="adopted_pets.php">✅ View Adopted Pets</a></li>
        <li><a href="view_applications.php">📋 View Adoption Applications</a></li>
        <li><a href="interview_requests.php">📅 Interview Requests</a></li>
        <li><a href="scheduled_interviews.php">📅 Interview Schedule</a></li>
        <li><a href="follow_up_reminders.php">📨 Send Follow-Up Reminders</a></li>
        <li><a href="follow_up_history.php">🕓 Follow-Up History</a></li>
        <li><a href="setting.php">⚙️ My Settings</a></li>
        <li><a href="/animal_adoption_system/logout.php">🚪 Logout</a></li>
    </ul>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
