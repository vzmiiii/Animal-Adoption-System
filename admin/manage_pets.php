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
        body { font-family: 'Segoe UI', sans-serif; background: #fff; margin: 0; }
        .page-wrapper { max-width: 1150px; margin: auto; padding: 30px; }
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
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        th a {
            text-decoration: none;
            color: inherit;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
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
<?php include('../includes/footer.php'); ?>
</body>
</html>