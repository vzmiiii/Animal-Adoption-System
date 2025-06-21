<?php
session_start();
// TEMPORARY BYPASS FOR TESTING ONLY
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

include('../db_connection.php');

// Fetch system stats
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_pets = $conn->query("SELECT COUNT(*) AS total FROM pets")->fetch_assoc()['total'];
$total_adoptions = $conn->query("SELECT COUNT(*) AS total FROM adoption_applications WHERE status = 'approved'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

        .page-wrapper {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .content-container {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            margin-top: 20px;
            min-width: 900px;
        }

        h1 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 40px;
            color: #2c3e50;
            font-size: 2.8em;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }

        .stat-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .box {
            background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7));
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        .box h2 {
            margin: 0;
            font-size: 3.5em;
            font-weight: 700;
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .box p {
            margin-top: 15px;
            font-size: 18px;
            color: #555;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .stat-boxes {
                grid-template-columns: 1fr;
            }
            .content-container {
                padding: 30px 20px;
            }
            h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <?php include('../includes/navbar_admin.php'); ?>

    <div class="page-wrapper">
        <div class="content-container">
            <h1>Admin Dashboard</h1>
            <div class="stat-boxes">
                <div class="box">
                    <h2><?= $total_users ?></h2>
                    <p>Total Users</p>
                </div>
                <div class="box">
                    <h2><?= $total_pets ?></h2>
                    <p>Pets Listed</p>
                </div>
                <div class="box">
                    <h2><?= $total_adoptions ?></h2>
                    <p>Approved Adoptions</p>
                </div>
            </div>
        </div>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>