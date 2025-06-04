<?php
session_start();

// Show flash message if exists
if (isset($_SESSION['message'])) {
    echo "<div class='alert'>" . htmlspecialchars($_SESSION['message']) . "</div>";
    unset($_SESSION['message']);
}

// Block access if not an adopter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adopter Dashboard - Animal Adoption System</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px;
            min-height: 100vh;
        }

        .dashboard-box {
            background-color: #ffffff;
            width: 100%;
            max-width: 600px;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .dashboard-box h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #222;
        }

        .notif-link {
            display: inline-block;
            margin-bottom: 30px;
            font-size: 14px;
            color: #333;
            padding: 10px 18px;
            border-radius: 8px;
            background-color: #f0f0f0;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .notif-link:hover {
            background-color: #e6e6e6;
            transform: scale(1.03);
        }

        ul.dashboard-links {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        ul.dashboard-links li {
            margin-bottom: 15px;
        }

        ul.dashboard-links a {
            display: block;
            padding: 14px 22px;
            border-radius: 10px;
            background-color: #f7f7f7;
            color: #111;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: inset 0 0 0 2px #e0e0e0;
        }

        ul.dashboard-links a:hover {
            background-color: #f0f0f0;
            box-shadow: inset 0 0 0 2px #c7c7c7;
        }

        .alert {
            background-color: #ffe6e6;
            color: #a33;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 600px;
            text-align: center;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            .dashboard-box {
                padding: 40px 20px;
            }

            .dashboard-box h2 {
                font-size: 24px;
            }

            ul.dashboard-links a {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="dashboard-container">
    <div class="dashboard-box">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>

        <a href="notifications.php" class="notif-link">🔔 View All Notifications</a>

        <ul class="dashboard-links">
            <li><a href="browse_available_pets.php">🐾 Browse Available Pets</a></li>
            <li><a href="compatibility_quiz.php">🧩 Take Compatibility Quiz</a></li>
            <li><a href="adoption_tracker.php">📋 Adoption Tracker</a></li>
            <li><a href="interview_status.php">📅 Interview Status</a></li>
            <li><a href="followup_messages.php">📩 Follow-Up Messages</a></li>
            <li><a href="setting.php">⚙️ My Settings</a></li>
            <li><a href="../logout.php">🚪 Logout</a></li>
        </ul>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
