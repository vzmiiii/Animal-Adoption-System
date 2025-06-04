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
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            color: #333;
        }

        .page-wrapper {
            padding: 50px 20px;
            max-width: 800px;
            margin: auto;
            text-align: center;
            background-color: #ffffff;
        }

        h1 {
            font-weight: 500;
            margin-bottom: 40px;
        }

        .stat-boxes {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .box {
            background-color: #fafafa;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            flex: 1;
        }

        .box h2 {
            margin: 0;
            font-size: 36px;
            font-weight: 400;
            color: #000;
        }

        .box p {
            margin-top: 8px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <?php include('../includes/navbar_admin.php'); ?>

    <div class="page-wrapper">
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
    <?php include('../includes/footer.php'); ?>
</body>
</html>