<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];

$sql = "SELECT i.*, p.name AS pet_name, u.username AS adopter_name
        FROM interviews i
        JOIN pets p ON i.pet_id = p.id
        JOIN users u ON i.adopter_id = u.id
        WHERE i.shelter_id = ?
        ORDER BY i.interview_datetime DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scheduled Interviews</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body { background: #fff; font-family: 'Segoe UI', sans-serif; margin: 0; }
        .box {
            max-width: 900px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fce7cd;
            border-radius: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }
        h2 { text-align: center; margin-bottom: 30px; font-size: 28px; }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            text-align: left;
        }
        th { background-color: #eee; }
        tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="box">
    <h2>📅 Scheduled Interviews</h2>
    <?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Adopter</th>
            <th>Pet</th>
            <th>Date & Time</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['adopter_name']) ?></td>
            <td><?= htmlspecialchars($row['pet_name']) ?></td>
            <td><?= date("d M Y, h:i A", strtotime($row['interview_datetime'])) ?></td>
            <td><?= ucfirst($row['status']) ?></td>
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
