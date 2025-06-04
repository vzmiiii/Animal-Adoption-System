<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

// Optional filter by role
$filter = $_GET['role'] ?? '';
$where = '';
if ($filter === 'adopter' || $filter === 'shelter') {
    $where = "WHERE role = '$filter'";
}

$sql = "SELECT id, username, email, role, first_name, last_name, status FROM users $where ORDER BY role, username";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
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
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .filter {
            text-align: right;
        }
        .filter select {
            padding: 6px 12px;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <h1>Manage Users</h1>
    <div class="filter">
        <form method="GET" action="">
            <label>Filter by Role: </label>
            <select name="role" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="adopter" <?= $filter === 'adopter' ? 'selected' : '' ?>>Adopters</option>
                <option value="shelter" <?= $filter === 'shelter' ? 'selected' : '' ?>>Shelter Personnel</option>
            </select>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['role'] ?></td>
                <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td class="actions">
                    <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                    <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
