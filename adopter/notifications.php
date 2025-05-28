<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$user_id = $_SESSION['user_id'];
$role = 'adopter';

// SELECT Notifications
$sql = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND role = ? ORDER BY created_at DESC");
if (!$sql) {
    die("Prepare failed: " . $conn->error);
}
$sql->bind_param("is", $user_id, $role);
$sql->execute();
$result = $sql->get_result();

// UPDATE Notifications to mark as read
$update = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND role = ?");
if ($update) {
    $update->bind_param("is", $user_id, $role);
    $update->execute();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Notifications</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body {
            background-color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }
        .notif-box {
            max-width: 700px;
            margin: 80px auto;
            padding: 40px;
            background-color: #f7e6cf;
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
            padding-left: 0;
        }
        li {
            background: #fff;
            margin-bottom: 10px;
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        em {
            font-size: 12px;
            color: #888;
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
                    <br><em><?= date("d M Y, h:i A", strtotime($n['created_at'])) ?></em>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No notifications at the moment.</li>
        <?php endif; ?>
    </ul>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
