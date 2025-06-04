<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$sql = "SELECT a.id, a.status, a.application_date,
               u.username AS adopter_name,
               p.name AS pet_name,
               s.username AS shelter_name
        FROM adoption_applications a
        JOIN users u ON a.adopter_id = u.id
        JOIN pets p ON a.pet_id = p.id
        JOIN users s ON p.shelter_id = s.id
        ORDER BY a.application_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Adoption Applications</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fff; margin: 0; }
        .page-wrapper { max-width: 1200px; margin: auto; padding: 30px; }
        h1 { text-align: center; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background: #f3f3f3;
        }
        .status-approved { color: green; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <h1>All Adoption Applications</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Adopter</th>
                <th>Pet</th>
                <th>Shelter</th>
                <th>Status</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['adopter_name']) ?></td>
                <td><?= htmlspecialchars($row['pet_name']) ?></td>
                <td><?= htmlspecialchars($row['shelter_name']) ?></td>
                <td class="status-<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
                <td><?= date('Y-m-d', strtotime($row['application_date'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
