<?php
// ---------------------------------------------
// followup_history.php
// Shelter-side page to view the history of follow-up messages
// ---------------------------------------------

session_start();

// Restrict access: only logged-in users with the 'shelter' role can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];

// Fetch all follow-up messages for pets belonging to this shelter,
// along with the pet name and adopter's username, ordered by most recent
$sql = "
    SELECT 
        f.*, 
        p.name AS pet_name, 
        u.username AS adopter_name
    FROM follow_ups f
    JOIN pets p ON f.pet_id = p.id
    JOIN users u ON f.adopter_id = u.id
    WHERE p.shelter_id = ?
    ORDER BY f.sent_at DESC
";

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

    <!-- Common styles (e.g., navbar, footer, resets) -->
    <link rel="stylesheet" href="../css/common.css">
    <!-- Shelter-specific styles (you may already have layout and color rules here) -->
    <link rel="stylesheet" href="../css/shelter.css">

    <style>
        /* Page background and font settings */
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Main wrapper to center content and limit width */
        .page-wrapper {
            max-width: 700px;
            margin: auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            border: 1px solid #e0e0e0;
        }

        /* Page title */
        h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 30px;
            color: #222;
        }

        /* Individual follow-up card */
        .followup-card {
            background-color: #ffffff; /* Light beige */
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08); /* Subtle shadow */
        }

        .followup-card p {
            margin: 8px 0;
            line-height: 1.5;
            color: #333;
        }

        .followup-card strong {
            color: #000; /* Pure black for labels */
        }

        .followup-card em {
            color: #666; /* Lighter grey for timestamps */
            font-size: 0.9rem;
        }

        /* Message text formatting */
        .message-text {
            white-space: pre-wrap; /* Preserve line breaks */
            margin-top: 8px;
        }

        /* Fallback text when there are no messages */
        p.no-messages {
            text-align: center;
            font-style: italic;
            color: #555;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <!-- Include shelter navbar (assumes ../includes/navbar_shelter.php exists) -->
    <?php include('../includes/navbar_shelter.php'); ?>

    <div class="page-wrapper">
        <!-- Page heading -->
        <h2>Follow-Up Message History</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <!-- Single follow-up card -->
                <div class="followup-card">
                    <!-- Pet name -->
                    <p>
                        <strong>Pet:</strong>
                        <?php echo htmlspecialchars($row['pet_name']); ?>
                    </p>

                    <!-- Adopter name -->
                    <p>
                        <strong>Adopter:</strong>
                        <?php echo htmlspecialchars($row['adopter_name']); ?>
                    </p>

                    <!-- Message content; `nl2br` to preserve new lines -->
                    <p>
    <strong>Message:</strong>
    <?php echo nl2br(htmlspecialchars($row['message'])); ?>
</p>
                    <!-- Timestamp -->
                    <p>
                        <em>Sent at: <?php echo htmlspecialchars($row['sent_at']); ?></em>
                    </p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <!-- Display when no follow-up messages exist -->
            <p class="no-messages">No follow-up messages sent yet.</p>
        <?php endif; ?>
    </div>

    <!-- Include footer (assumes ../includes/footer.php exists) -->
    <?php include('../includes/footer.php'); ?>
</body>
</html>
