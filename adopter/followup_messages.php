<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$adopter_id = $_SESSION['user_id'];

// Fetch follow-up messages, pet name, and shelter username
$sql = "SELECT f.*, p.name AS pet_name, u.username AS shelter_name
        FROM follow_ups f
        JOIN pets p ON f.pet_id = p.id
        JOIN users u ON p.shelter_id = u.id
        WHERE f.adopter_id = ?
        ORDER BY f.sent_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adopter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Follow-Up Messages</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        .followup-wrapper {
            max-width: 900px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fef9ec;
            border-radius: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .followup-wrapper h2 {
            font-size: 26px;
            text-align: center;
            margin-bottom: 30px;
        }

        .followup-wrapper a {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        .message-card {
            background-color: #fff;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        }

        .message-card p {
            font-size: 14px;
            margin: 8px 0;
        }

        .message-card small {
            color: #555;
            font-size: 12px;
        }

        .empty-msg {
            background-color: #fff;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="followup-wrapper">
    <h2>📩 Follow-Up Messages from Shelters</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="message-card">
                <p><strong>Pet:</strong> <?php echo htmlspecialchars($row['pet_name']); ?></p>
                <p><strong>Shelter:</strong> <?php echo htmlspecialchars($row['shelter_name']); ?></p>
                <p><strong>Message:</strong><br><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                <p><small>Sent at: <?php echo date("F j, Y, g:i A", strtotime($row['sent_at'])); ?></small></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-msg">You haven't received any follow-up messages yet.</div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
