<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

// Filter sanitization
$allowed_roles = ['adopter', 'shelter'];
$filter = isset($_GET['role']) && in_array($_GET['role'], $allowed_roles) ? $_GET['role'] : '';

$where = '';
$params = [];
$types = '';

if ($filter !== '') {
    $where = "WHERE role = ?";
    $params[] = $filter;
    $types .= 's';
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total for pagination
$count_query = "SELECT COUNT(*) AS total FROM users $where";
$count_stmt = $conn->prepare($count_query);
if ($filter) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_result = $count_stmt->get_result()->fetch_assoc();
$total_pages = ceil($total_result['total'] / $limit);

// Fetch users with prepared statements
$sql = "SELECT id, username, email, role, first_name, last_name, status 
        FROM users $where 
        ORDER BY role, username 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if ($filter) {
    $stmt->bind_param($types . "ii", $filter, $offset, $limit);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
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
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th { background-color: #f3f3f3; }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .filter { text-align: right; }
        .filter select { padding: 6px 12px; }
        .pagination { text-align: center; margin-top: 20px; }
        .pagination a {
            display: inline-block; padding: 8px 12px;
            border: 1px solid #ddd; margin: 2px; text-decoration: none; color: #007bff;
        }
        .pagination a.active {
            background-color: #007bff; color: white; border-color: #007bff;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <h1>Manage Users</h1>
    <div class="filter">
        <form method="GET">
            <label>Filter by Role:</label>
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
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                <td class="actions">
                    <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                    <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?role=<?= htmlspecialchars($filter) ?>&page=<?= $i ?>" class="<?= $page === $i ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
