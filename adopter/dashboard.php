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
        html {
            height: 100%;
        }

        body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

        .main-content {
            flex: 1 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        footer {
            flex-shrink: 0;
        }

        * {
            box-sizing: border-box;
        }

        .dashboard-container {
            width: 100%;
            max-width: 650px;
        }

        .dashboard-box {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px 50px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .dashboard-box h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 25px;
            color: #2c3e50;
        }

        .notif-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 35px;
            font-size: 16px;
            color: #34495e;
            padding: 12px 20px;
            border-radius: 12px;
            background-color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .notif-link:hover {
            background-color: #dbe4e8;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        ul.dashboard-links {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 15px;
        }

        ul.dashboard-links li {
            margin: 0;
        }

        ul.dashboard-links a {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 18px 25px;
            border-radius: 16px;
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
            color: #2c3e50;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #e8e8e8;
            gap: 15px;
        }

        ul.dashboard-links a:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            background: #fff;
            color: #1abc9c;
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-radius: 12px;
            margin: 20px auto;
            max-width: 650px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #f5c6cb;
            box-shadow: 0 4px 12px rgba(200, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            .dashboard-box {
                padding: 30px 25px;
                border-radius: 20px;
            }

            .dashboard-box h2 {
                font-size: 26px;
            }
            
            ul.dashboard-links a {
                padding: 15px 20px;
                font-size: 15px;
            }

            .notif-link {
                font-size: 14px;
                padding: 10px 16px;
            }
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="main-content">
    <div class="dashboard-container">
        <div class="dashboard-box">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>

            <a href="notifications.php" class="notif-link">🔔 View All Notifications</a>

            <ul class="dashboard-links">
                <li><a href="browse_available_pets.php"><span class="icon">🐾</span> Browse Available Pets</a></li>
                <li><a href="compatibility_quiz.php"><span class="icon">🧩</span> Take Compatibility Quiz</a></li>
                <li><a href="adoption_tracker.php"><span class="icon">📋</span> Adoption Tracker</a></li>
                <li><a href="interview_status.php"><span class="icon">📅</span> Interview Status</a></li>
                <li><a href="followup_messages.php"><span class="icon">📩</span> Follow-Up Messages</a></li>
                <li><a href="setting.php"><span class="icon">⚙️</span> My Settings</a></li>
                <li><a href="../logout.php"><span class="icon">🚪</span> Logout</a></li>
            </ul>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>

