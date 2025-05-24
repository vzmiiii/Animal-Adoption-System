<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];

$sql = "SELECT a.id, a.status, a.application_date, u.username AS adopter_name, p.name AS pet_name
        FROM adoption_applications a
        JOIN users u ON a.adopter_id = u.id
        JOIN pets p ON a.pet_id = p.id
        WHERE p.shelter_id = ?
        ORDER BY a.application_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Adoption Applications</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
<style>
body {
    background-color: white;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 16px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
    background-color: #fff;
}

.styled-table th,
.styled-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.styled-table th {
    background-color: #fce7cd;
    font-weight: bold;
}

.action-btn {
    padding: 6px 12px;
    margin-right: 6px;
    text-decoration: none;
    border-radius: 4px;
    color: white;
    font-weight: bold;
}

.action-btn.approve {
    background-color: #28a745;
}

.action-btn.reject {
    background-color: #dc3545;
}

.action-complete {
    color: #888;
}
</style>
</head>
<body>
<?php include('../includes/navbar_shelter.php'); ?>

<div class="page-wrapper">
    <h2>Review Applications</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-container">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Pet</th>
                        <th>Adopter</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['adopter_name']); ?></td>
                        <td><?php echo date("d M Y", strtotime($row['application_date'])); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <?php if ($row['status'] === 'pending'): ?>
                                <a class="action-btn approve" href="update_application_status.php?id=<?php echo $row['id']; ?>&status=approved">Approve</a>
                                <a class="action-btn reject" href="update_application_status.php?id=<?php echo $row['id']; ?>&status=rejected">Reject</a>
                            <?php else: ?>
                                <span class="action-complete">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No applications found.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
