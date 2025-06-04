<?php
session_start();

// Redirect if not logged in or not an adopter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

// Get current adopter's ID from session
$adopter_id = $_SESSION['user_id'];

// SQL to retrieve interview details for the adopter
$sql = "SELECT i.*, p.name AS pet_name, u.username AS shelter_name
        FROM interviews i
        JOIN pets p ON i.pet_id = p.id
        JOIN users u ON i.shelter_id = u.id
        WHERE i.adopter_id = ?
        ORDER BY i.interview_datetime ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adopter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Interview Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body {
            margin: 0;
            background-color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .box {
            max-width: 900px;
            margin: 80px auto;
            padding: 40px;
            background-color: #f7e6cf;
            border-radius: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 18px;
            font-size: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #eee;
        }

        tr:last-child td {
            border-bottom: none;
        }

        p {
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>

<!-- Include adopter navbar -->
<?php include('../includes/navbar_adopter.php'); ?>

<div class="box">
    <h2>📅 My Interview Status</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Pet</th>
                <th>Shelter</th>
                <th>Date & Time</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['pet_name']) ?></td>
                    <td><?= htmlspecialchars($row['shelter_name']) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($row['interview_datetime'])) ?></td>
                    <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No interviews found.</p>
    <?php endif; ?>
</div>

<!-- Include footer -->
<?php include('../includes/footer.php'); ?>
</body>
</html>
