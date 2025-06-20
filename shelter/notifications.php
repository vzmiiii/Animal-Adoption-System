<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$user_id = $_SESSION['user_id'];
$role = 'shelter';

// Fetch notifications, newest first
$sql = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND role = ? ORDER BY created_at DESC");
if (!$sql) {
    die("Prepare failed: " . $conn->error);
}
$sql->bind_param("is", $user_id, $role);
$sql->execute();
$result = $sql->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

// Mark unread notifications as read after fetching them
$update = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND role = ? AND is_read = 0");
if ($update) {
    $update->bind_param("is", $user_id, $role);
    $update->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Notifications</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        body {
            background: linear-gradient(rgba(255,255,255,0.6), rgba(255,255,255,0.6)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .content-container {
            max-width: 850px;
            margin: 2.5rem auto;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.6);
        }
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .page-header h2 {
            font-size: 36px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 0.5rem 0;
        }
        .page-header p {
            font-size: 17px;
            color: #7f8c8d;
            margin: 0;
        }
        .notification-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .notification-item {
            background-color: #fff;
            padding: 1.25rem 1.75rem;
            margin-bottom: 1rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.2s ease-in-out;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .notification-item:hover {
            transform: translateX(5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }
        .notification-item .message {
            font-size: 16px;
            color: #34495e;
            flex-grow: 1;
        }
        .notification-item .timestamp {
            font-size: 14px;
            color: #95a5a6;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .empty-msg {
            text-align: center;
            font-size: 18px;
            color: #7f8c8d;
            margin-top: 2rem;
            padding: 3rem;
            background-color: rgba(245, 245, 245, 0.7);
            border-radius: 15px;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="content-container">
    <div class="page-header">
        <h2>🔔 My Notifications</h2>
        <p>Here are your latest updates. Notifications are automatically marked as read when you visit this page.</p>
    </div>
    
    <?php if (count($notifications) > 0): ?>
        <ul class="notification-list">
            <?php foreach ($notifications as $n): ?>
                <li class="notification-item">
                    <span class="message"><?= htmlspecialchars($n['message']) ?></span>
                    <span class="timestamp"><?= date("M j, Y, g:i A", strtotime($n['created_at'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="empty-msg">
            <p>You have no notifications at the moment.</p>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
