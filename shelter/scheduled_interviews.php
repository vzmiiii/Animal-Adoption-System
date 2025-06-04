<?php
session_start();

// Only allow access if the user is logged in as a shelter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

// Fetch the current shelter’s ID from session
$shelter_id = $_SESSION['user_id'];

// Prepare and execute query to get all interviews for this shelter, including pet & adopter names
$sql = "
    SELECT 
        i.id,
        i.interview_datetime,
        i.status,
        p.name AS pet_name,
        u.username AS adopter_name
    FROM interviews AS i
    JOIN pets AS p 
        ON i.pet_id = p.id
    JOIN users AS u 
        ON i.adopter_id = u.id
    WHERE i.shelter_id = ?
    ORDER BY i.interview_datetime ASC
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
    <title>Scheduled Interviews</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body {
            background-color: #f5f5f5;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        /* Centered container for the card */
        .page-wrapper {
            max-width: 900px;
            margin: 80px auto;
            padding: 32px;
            background-color: #ffffff;
            border-radius: 16px;
            border: 1px solid #e0e0e0;
        }

        

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Table header row */
        th {
            background-color: #f0f0f0; /* very light grey */
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            text-align: left;
            color: #333;
        }

        /* Table body cells */
        td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid #e0e0e0; /* subtle divider */
            color: #555;
        }

        /* Remove last row’s border */
        tr:last-child td {
            border-bottom: none;
        }

        /* Status text styling */
        .status {
            font-weight: 500;
            text-transform: capitalize;
        }

        /* “No interviews” message styling */
        .no-data {
            text-align: center;
            padding: 24px;
            color: #777;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <!-- Include shelter-specific navbar -->
    <?php include('../includes/navbar_shelter.php'); ?>

    <div class="page-wrapper">
        <div class="box">
            <h2>📅 Scheduled Interviews</h2>

            <?php if ($result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Adopter</th>
                        <th>Pet</th>
                        <th>Date &amp; Time</th>
                        <th>Status</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <!-- Escape output to prevent XSS -->
                            <td><?= htmlspecialchars($row['adopter_name']) ?></td>
                            <td><?= htmlspecialchars($row['pet_name']) ?></td>
                            <td>
                                <?= date("j M Y, h:i A", strtotime($row['interview_datetime'])) ?>
                            </td>
                            <td class="status">
                                <?= htmlspecialchars($row['status']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div class="no-data">
                    No interviews found.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include global footer; this should stick to the bottom -->
    <?php include('../includes/footer.php'); ?>
</body>
</html>
