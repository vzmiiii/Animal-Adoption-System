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
        .followup-wrapper {
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

        .followup-wrapper h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }

        .message-card {
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .message-header p {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .message-card .message-body {
            font-size: 15px;
            line-height: 1.6;
            color: var(--text-color-light);
            margin: 0 0 15px;
        }

        .message-card .message-footer {
            text-align: right;
            color: var(--text-color-light);
            font-size: 13px;
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

<div class="followup-wrapper">
    <h2>📩 Follow-Up Messages</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="message-card">
                <div class="message-header">
                    <p><strong>Pet:</strong> <?php echo htmlspecialchars($row['pet_name']); ?></p>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($row['shelter_name']); ?></p>
                </div>
                <p class="message-body"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                <div class="message-footer">
                    Sent: <?php echo date("F j, Y, g:i A", strtotime($row['sent_at'])); ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-msg">You haven't received any follow-up messages yet.</div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
