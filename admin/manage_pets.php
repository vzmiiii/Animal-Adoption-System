<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

// Sorting logic
$order_by = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order_dir = isset($_GET['dir']) && $_GET['dir'] === 'desc' ? 'DESC' : 'ASC';

$allowed_sort = ['id', 'name', 'species', 'breed', 'age', 'gender', 'status', 'shelter_name'];
$order_by = in_array($order_by, $allowed_sort) ? $order_by : 'id';

$sql = "SELECT pets.id, pets.name, pets.species, pets.breed, pets.age, pets.gender, pets.status,
               shelters.username AS shelter_name
        FROM pets
        JOIN users shelters ON pets.shelter_id = shelters.id
        ORDER BY $order_by $order_dir, pets.name ASC";

$result = $conn->query($sql);

// Toggle sorting direction
function toggleDir($currentDir) {
    return $currentDir === 'asc' ? 'desc' : 'asc';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Pets</title>
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

        th a {
            text-decoration: none;
            color: white;
            transition: opacity 0.2s;
        }

        th a:hover {
            opacity: 0.8;
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
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <div class="content-container">
        <h1>Manage All Pets</h1>

        <table>
            <thead>
                <tr>
                    <?php foreach ($allowed_sort as $column): ?>
                        <th>
                            <a href="?sort=<?= $column ?>&dir=<?= toggleDir($order_dir) ?>">
                                <?= ucwords(str_replace('_', ' ', $column)) ?>
                            </a>
                        </th>
                    <?php endforeach; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['species']) ?></td>
                    <td><?= htmlspecialchars($row['breed']) ?></td>
                    <td><?= $row['age'] ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                    <td><?= htmlspecialchars($row['shelter_name']) ?></td>
                    <td class="actions">
                        <a href="edit_pet.php?id=<?= $row['id'] ?>">Edit</a>
                        <a href="delete_pet.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this pet?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>