<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$sql = "SELECT id, username, email, first_name, last_name, phone_number, status 
        FROM users 
        WHERE role = 'shelter'
        ORDER BY status, username";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Shelters</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fff; margin: 0; }
        .page-wrapper { max-width: 1100px; margin: auto; padding: 30px; }
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
            background: #f5f5f5;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <h1>Manage Shelter Accounts</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td class="actions">
                    <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                    <a href="toggle_shelter_status.php?id=<?= $row['id'] ?>" onclick="return confirm('Change shelter status?')">Toggle Status</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
