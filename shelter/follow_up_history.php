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

// Check if a specific pet_id is provided to filter the history
$pet_id_filter = isset($_GET['pet_id']) ? (int)$_GET['pet_id'] : 0;

// Base SQL for fetching messages
$sql = "
    SELECT 
        f.message,
        f.sent_at, 
        p.name AS pet_name, 
        u.username AS adopter_name
    FROM follow_ups f
    JOIN pets p ON f.pet_id = p.id
    JOIN users u ON f.adopter_id = u.id
    WHERE p.shelter_id = ?
";

if ($pet_id_filter > 0) {
    $sql .= " AND f.pet_id = ?";
}
$sql .= " ORDER BY f.sent_at DESC";

$stmt = $conn->prepare($sql);
if ($pet_id_filter > 0) {
    $stmt->bind_param("ii", $shelter_id, $pet_id_filter);
} else {
    $stmt->bind_param("i", $shelter_id);
}
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);

// Determine the page title
$page_title = "Follow-Up Message History";
$page_subtitle = "A log of all messages sent to adopters from your shelter.";
if ($pet_id_filter > 0) {
    // Fetch the pet's name for the title, regardless of whether there are messages
    $pet_name_sql = "SELECT name FROM pets WHERE id = ? AND shelter_id = ?";
    $pet_name_stmt = $conn->prepare($pet_name_sql);
    $pet_name_stmt->bind_param("ii", $pet_id_filter, $shelter_id);
    $pet_name_stmt->execute();
    $pet_name_result = $pet_name_stmt->get_result();
    if ($pet_row = $pet_name_result->fetch_assoc()) {
        $page_title = "Follow-Up History for " . htmlspecialchars($pet_row['name']);
        $page_subtitle = "A log of all messages sent for this specific pet.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
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
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .followup-card {
            background-color: #ffffff;
            padding: 2rem;
            margin-bottom: 1.75rem;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            transition: all 0.25s ease-in-out;
        }
        .followup-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.09);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .card-header .info p {
            margin: 0 0 0.3rem 0;
            font-size: 16px;
            color: #34495e;
        }
        .card-header .info strong {
            font-weight: 600;
            color: #5b6a78;
        }
        .card-header .timestamp {
            font-size: 14px;
            color: #95a5a6;
            font-weight: 500;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .message-text {
            white-space: pre-wrap;
            line-height: 1.7;
            color: #34495e;
            font-size: 15.5px;
            padding-top: 1.25rem;
            margin-top: 1rem;
            border-top: 1px solid #ecf0f1;
            text-align: left;
        }
        .no-messages {
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
            <h2><?php echo $page_title; ?></h2>
            <p><?php echo $page_subtitle; ?></p>
        </div>

        <?php if (count($messages) > 0): ?>
            <?php foreach ($messages as $row): ?>
                <div class="followup-card">
                    <div class="card-header">
                        <div class="info">
                            <?php if ($pet_id_filter === 0): ?>
                                <p><strong>Pet:</strong> <?php echo htmlspecialchars($row['pet_name']); ?></p>
                            <?php endif; ?>
                            <p><strong>To:</strong> <?php echo htmlspecialchars($row['adopter_name']); ?></p>
                        </div>
                        <span class="timestamp"><?php echo date("M j, Y, g:i A", strtotime($row['sent_at'])); ?></span>
                    </div>
                    <div class="message-text">
                        <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-messages">No follow-up messages found for this selection.</p>
        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
