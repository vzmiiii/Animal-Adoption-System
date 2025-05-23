<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];

$sql = "SELECT f.*, p.name AS pet_name, u.username AS adopter_name
        FROM follow_ups f
        JOIN pets p ON f.pet_id = p.id
        JOIN users u ON f.adopter_id = u.id
        WHERE p.shelter_id = ?
        ORDER BY f.sent_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Follow-Up Message History</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: white;
        }

        .page-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .pet-card {
            background-color: #fce7cd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .pet-card p {
            margin: 8px 0;
            line-height: 1.6;
            color: #333;
        }

        .pet-card strong {
            color: #222;
        }

        .pet-card em {
            color: #666;
            font-size: 0.95em;
        }

        p.no-message {
            text-align: center;
            color: #555;
            font-style: italic;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_shelter.php'); ?>

<div class="page-wrapper">
    <h2>Follow-Up Message History</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="pet-card">
                <p><strong>Pet:</strong> <?php echo htmlspecialchars($row['pet_name']); ?></p>
                <p><strong>Adopter:</strong> <?php echo htmlspecialchars($row['adopter_name']); ?></p>
                <p><strong>Message:</strong><br><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                <p><em>Sent at: <?php echo $row['sent_at']; ?></em></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-message">No follow-up messages sent yet.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
