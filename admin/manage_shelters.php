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

        .search-form {
            text-align: center;
            margin-bottom: 30px;
        }

        .search-form input[type="text"] {
            padding: 14px 20px;
            width: 350px;
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            font-size: 16px;
            margin-right: 10px;
            transition: all 0.3s;
        }

        .search-form input[type="text"]:focus {
            outline: none;
            border-color: #6ed6a5;
            box-shadow: 0 0 10px rgba(110, 214, 165, 0.2);
        }

        .search-form button {
            padding: 14px 28px;
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
            margin-top: 30px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            text-decoration: none;
            color: #4e8cff;
            padding: 10px 16px;
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            margin: 0 4px;
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
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
