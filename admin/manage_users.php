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
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

        .page-wrapper {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .content-container {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            margin-top: 20px;
        }

        h1 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 40px;
            color: #2c3e50;
            font-size: 2.8em;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }

        .filter { 
            text-align: center; 
            margin-bottom: 30px;
        }
        
        .filter label {
            font-weight: 500;
            color: #333;
            margin-right: 10px;
        }

        .filter select { 
            padding: 12px 18px; 
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .filter select:focus {
            outline: none;
            border-color: #6ed6a5;
            box-shadow: 0 0 10px rgba(110, 214, 165, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 18px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        th {
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        tr:last-of-type td {
            border-bottom: none;
        }

        tr:hover {
            background: rgba(110, 214, 165, 0.1);
        }

        .actions a {
            margin-right: 12px;
            text-decoration: none;
            color: #4e8cff;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
        }

        .actions a:hover {
            background: #4e8cff;
            color: white;
        }

        .pagination { 
            text-align: center; 
            margin-top: 30px; 
        }

        .pagination a {
            display: inline-block; 
            padding: 10px 16px;
            border: 1.5px solid rgba(0,0,0,0.1); 
            margin: 2px; 
            text-decoration: none; 
            color: #4e8cff;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .pagination a:hover {
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .pagination a.active {
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: white; 
            border-color: transparent;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <div class="content-container">
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
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
