<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$limit = 10; // shelters per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query with prepared statement for security
$stmt = $conn->prepare("SELECT id, username, email, first_name, last_name, phone_number, status 
                        FROM users 
                        WHERE role = 'shelter' AND username LIKE ?
                        ORDER BY status, username
                        LIMIT ? OFFSET ?");
$search_param = "%{$search}%";
$stmt->bind_param('sii', $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Count total records for pagination
$count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'shelter' AND username LIKE ?");
$count_stmt->bind_param('s', $search_param);
$count_stmt->execute();
$total_shelters = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_shelters / $limit);
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
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            text-decoration: none;
            color: #007bff;
            padding: 5px 10px;
            border: 1px solid #ddd;
            margin: 0 2px;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .search-form {
            text-align: center;
            margin-top: 20px;
        }
        .search-form input[type="text"] {
            padding: 5px 10px;
            width: 250px;
        }
        .search-form button {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <h1>Manage Shelter Accounts</h1>

    <form class="search-form" method="get">
        <input type="text" name="search" placeholder="Search shelters..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

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
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                <td class="actions">
                    <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                    <a href="toggle_shelter_status.php?id=<?= $row['id'] ?>" onclick="return confirm('Change shelter status?')">Toggle Status</a>
                </td>
            </tr>
        <?php endwhile; ?>
        <?php if ($result->num_rows == 0): ?>
            <tr><td colspan="7" style="text-align:center;">No shelters found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
