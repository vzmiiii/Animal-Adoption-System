<?php
session_start();

// Redirect if not logged in or not an adopter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$adopter_id = $_SESSION['user_id'];

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
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        .box {
            max-width: 900px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fef9ec;
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
            font-weight: bold;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-label {
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-label.confirmed {
            color: #2e7d32; /* green */
        }

        .status-label.pending {
            color: #ff9800; /* orange */
        }

        p {
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>

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
            <?php while ($row = $result->fetch_assoc()):
                $status = strtolower($row['status']);
                $status_class = "status-label " . $status;
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['pet_name']) ?></td>
                    <td><?= htmlspecialchars($row['shelter_name']) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($row['interview_datetime'])) ?></td>
                    <td><span class="<?= $status_class ?>"><?= ucfirst($status) ?></span></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No interviews found.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
