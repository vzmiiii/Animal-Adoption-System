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
    <style>
        /* Page styling for a clean, minimalist feel */
        body {
            background-color: #fff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .notif-box {
            max-width: 700px;
            margin: 100px auto;
            padding: 40px;
            background-color: #f7e6cf; /* Soft beige background */
            border-radius: 30px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
        }

        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background: #fff;
            margin-bottom: 12px;
            padding: 16px 20px;
            border-radius: 12px;
            border: 1px solid #ddd;
            font-size: 15px;
            position: relative;
        }

        em {
            font-size: 12px;
            color: #888;
            display: block;
            margin-top: 6px;
        }

        .empty-msg {
            text-align: center;
            color: #666;
            font-size: 16px;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="notif-box">
    <h2>🔔 My Notifications</h2>
    <ul>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($n = $result->fetch_assoc()): ?>
                <li>
                    <?= htmlspecialchars($n['message']) ?>
                    <em><?= date("d M Y, h:i A", strtotime($n['created_at'])) ?></em>
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
