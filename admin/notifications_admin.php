<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $audience = $_POST['audience'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $role_filter = "";
        if ($audience === 'adopter') {
            $role_filter = "WHERE role = 'adopter'";
        } elseif ($audience === 'shelter') {
            $role_filter = "WHERE role = 'shelter'";
        }

        $user_sql = "SELECT id FROM users $role_filter";
        $users = $conn->query($user_sql);

        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        while ($row = $users->fetch_assoc()) {
            $stmt->bind_param("is", $row['id'], $message);
            $stmt->execute();
        }

        $msg = "Notification sent successfully to " . $audience . "s.";
    } else {
        $msg = "Message cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Notifications</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fff; margin: 0; }
        .page-wrapper { max-width: 700px; margin: auto; padding: 30px; }
        h1 { text-align: center; }
        form { margin-top: 30px; }
        textarea, select, button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 1em;
        }
        .message {
            background: #e1f8e1;
            border: 1px solid #b3e6b3;
            padding: 10px;
            margin-bottom: 20px;
            color: green;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <h1>Send Notification</h1>

    <?php if ($msg): ?>
        <div class="message"><?= $msg ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="audience">Select Audience:</label>
        <select name="audience" id="audience" required>
            <option value="all">All Users</option>
            <option value="adopter">Adopters Only</option>
            <option value="shelter">Shelter Personnel Only</option>
        </select>

        <label for="message">Notification Message:</label>
        <textarea name="message" id="message" rows="5" required></textarea>

        <button type="submit">Send Notification</button>
    </form>
</div>
</body>
</html>
