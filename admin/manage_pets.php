<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$sql = "SELECT pets.id, pets.name, pets.species, pets.breed, pets.age, pets.gender, pets.status,
               shelters.username AS shelter_name
        FROM pets
        JOIN users shelters ON pets.shelter_id = shelters.id
        ORDER BY pets.status, pets.name";

$result = $conn->query($sql);
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
    <h1>Manage All Pets</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Pet Name</th>
                <th>Species</th>
                <th>Breed</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Status</th>
                <th>Shelter</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['species'] ?></td>
                <td><?= $row['breed'] ?></td>
                <td><?= $row['age'] ?></td>
                <td><?= $row['gender'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
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
</body>
</html>
