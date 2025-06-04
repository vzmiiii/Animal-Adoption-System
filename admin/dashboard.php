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
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fff; margin: 0; }
        .page-wrapper { padding: 30px; max-width: 1000px; margin: auto; }
        h1 { text-align: center; }
        .stat-boxes { display: flex; justify-content: space-around; margin-top: 40px; }
        .box {
            background: #f0f0f0;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            width: 30%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .box h2 { margin: 0; font-size: 2em; }
        .box p { margin-top: 10px; color: #666; }
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
</body>
</html>
