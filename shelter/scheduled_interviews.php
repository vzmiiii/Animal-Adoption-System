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
            max-width: 1100px;
            margin: 80px auto 40px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }
        .page-header h1 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        .styled-table th, .styled-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        .styled-table th {
            font-weight: 600;
            color: #555;
            background-color: rgba(255,255,255,0.7);
        }
        .styled-table tr:last-child td { border-bottom: none; }
        .status {
            font-weight: 600;
            text-transform: capitalize;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 13px;
            display: inline-block;
        }
        .status.confirmed { color: #1e6b3b; background-color: #d1f7de; }
        .status.pending { color: #9c5400; background-color: #fff1e0; }
        .status.rejected { color: #a82222; background-color: #ffe6e6; }
        .no-data {
            text-align: center;
            font-size: 1.2rem;
            color: #777;
            padding: 3rem;
            background: rgba(255,255,255,0.7);
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <?php include('../includes/navbar_shelter.php'); ?>
    <div class="content-container">
        <div class="page-header"><h1>📅 Scheduled Interviews</h1></div>
        <?php if ($result->num_rows > 0): ?>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Adopter</th>
                            <th>Pet</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['adopter_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                                <td><?php echo date("F j, Y, h:i A", strtotime($row['interview_datetime'])); ?></td>
                                <td>
                                    <span class="status <?php echo strtolower(htmlspecialchars($row['status'])); ?>">
                                        <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-data">No scheduled interviews found.</p>
        <?php endif; ?>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
