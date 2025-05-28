<?php
session_start();
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
        ORDER BY i.interview_datetime DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adopter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Interview Status</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body { background: #fff; font-family: 'Segoe UI', sans-serif; margin: 0; }
        .box {
            max-width: 900px;
            margin: 80px auto;
            padding: 40px;
            background-color: #f7e6cf;
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
