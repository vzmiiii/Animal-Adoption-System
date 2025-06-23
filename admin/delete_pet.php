<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid pet ID.";
    exit();
}
$pet_id = intval($_GET['id']);

// Fetch pet and shelter info
$sql = "SELECT pets.*, users.username AS shelter_name FROM pets JOIN users ON pets.shelter_id = users.id WHERE pets.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    echo "Pet not found.";
    exit();
}
$pet = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete related adoption applications first
    $del_apps = $conn->prepare("DELETE FROM adoption_applications WHERE pet_id = ?");
    $del_apps->bind_param("i", $pet_id);
    $del_apps->execute();
    // Delete pet
    $del_stmt = $conn->prepare("DELETE FROM pets WHERE id = ?");
    $del_stmt->bind_param("i", $pet_id);
    $del_stmt->execute();
    header("Location: manage_pets.php?deleted=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Pet</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        .content-container {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 40px 60px 32px 60px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            margin-top: 32px;
            min-width: 600px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }
        h1 {
            color: #e53e3e;
            font-size: 2em;
            margin-bottom: 18px;
            font-weight: 700;
        }
        .pet-details {
            margin: 24px 0 32px 0;
            font-size: 1.1em;
            color: #444;
            background: #f9fafb;
            border-radius: 12px;
            padding: 18px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .pet-details strong {
            color: #2c3e50;
        }
        .button-row {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 24px;
        }
        .btn {
            padding: 14px 32px;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-cancel {
            background: #fff;
            color: #2c3e50;
            border: 1.5px solid #dde1e7;
        }
        .btn-cancel:hover {
            background: #dde1e7;
        }
        .btn-delete {
            background: #e53e3e;
            color: #fff;
            border: 1.5px solid #e53e3e;
        }
        .btn-delete:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }
        @media (max-width: 800px) {
            .content-container {
                padding: 24px 8px 18px 8px;
                min-width: unset;
                max-width: 98vw;
            }
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>
<div class="page-wrapper">
    <div class="content-container">
        <h1>Delete Pet</h1>
        <div class="pet-details">
            <div><strong>Name:</strong> <?= htmlspecialchars($pet['name']) ?></div>
            <div><strong>Species:</strong> <?= htmlspecialchars($pet['species']) ?></div>
            <div><strong>Breed:</strong> <?= htmlspecialchars($pet['breed']) ?></div>
            <div><strong>Age:</strong> <?= htmlspecialchars($pet['age']) ?> years</div>
            <div><strong>Shelter:</strong> <?= htmlspecialchars($pet['shelter_name']) ?></div>
        </div>
        <div style="color:#b91c1c; font-weight:600; margin-bottom:18px;">Are you sure you want to delete this pet? This action cannot be undone.</div>
        <form method="POST" style="margin:0;">
            <div class="button-row">
                <button type="submit" class="btn btn-delete">Delete</button>
                <a href="manage_pets.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html> 