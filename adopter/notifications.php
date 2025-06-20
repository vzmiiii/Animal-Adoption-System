<?php
session_start();

// Check if user is logged in and is an adopter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$user_id = $_SESSION['user_id'];
$role = 'adopter';

// Prepare SQL to fetch notifications for this adopter
$sql = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND role = ? ORDER BY created_at ASC");
if (!$sql) {
    die("Prepare failed: " . $conn->error); // Debug message in case of SQL error
}
$sql->bind_param("is", $user_id, $role);
$sql->execute();
$result = $sql->get_result();

// Mark all fetched notifications as read
$update = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND role = ?");
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
    <link rel="stylesheet" href="../css/adopter.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        :root {
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

        .notifications-wrapper {
            max-width: 800px;
            margin: 80px auto 40px;
            padding: 40px;
            background: var(--container-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }

        h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li.notification-item {
            background-color: #fff;
            border: 1px solid var(--border-color);
            margin-bottom: 15px;
            padding: 20px;
            border-radius: var(--border-radius);
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }

        .notification-item span {
            flex-grow: 1;
        }

        .notification-item em {
            font-size: 13px;
            color: var(--text-color-light);
            font-style: normal;
            flex-shrink: 0;
            margin-left: 20px;
        }

        .empty-msg {
            text-align: center;
            color: var(--text-color-light);
            margin-top: 40px;
            font-size: 16px;
            padding: 30px;
            background: rgba(255,255,255,0.5);
            border-radius: var(--border-radius);
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="notifications-wrapper">
    <h2>🔔 My Notifications</h2>
    <ul>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($n = $result->fetch_assoc()): ?>
                <li class="notification-item">
                    <span><?= htmlspecialchars($n['message']) ?></span>
                    <em><?= date("F j, Y, g:i A", strtotime($n['created_at'])) ?></em>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="empty-msg">No notifications at the moment.</li>
        <?php endif; ?>
    </ul>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
