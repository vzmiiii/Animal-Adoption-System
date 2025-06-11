<?php
session_start();

// Redirect unauthenticated or non-adopter users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

// Ensure pet ID is passed
if (!isset($_GET['id'])) {
    echo "No pet ID provided.";
    exit();
}

$pet_id = intval($_GET['id']);

// Fetch pet info and associated shelter name
$sql = "SELECT pets.*, users.username AS shelter_name 
        FROM pets
        JOIN users ON pets.shelter_id = users.id
        WHERE pets.id = ? AND pets.status = 'available'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Pet not found or already adopted.";
    exit();
}

$pet = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Pet - <?= htmlspecialchars($pet['name']) ?></title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #ffffff;
            color: #333;
        }

        .page-wrapper {
            max-width: 700px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fef9ec;
            border-radius: 25px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            font-size: 14px;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            font-size: 26px;
            margin-bottom: 25px;
        }

        img {
            display: block;
            max-width: 100%;
            border-radius: 16px;
            margin: 0 auto 25px;
        }

        .info-group {
            font-size: 15px;
            line-height: 1.7;
        }

        .info-group p {
            margin: 8px 0;
        }

        .info-group strong {
            display: inline-block;
            width: 100px;
            font-weight: 600;
        }

        form {
            text-align: center;
            margin-top: 30px;
        }

        button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 12px 28px;
            font-size: 15px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #333;
        }

        @media (max-width: 600px) {
            .page-wrapper {
                margin: 40px 20px;
                padding: 30px 20px;
            }

            .info-group strong {
                width: auto;
                display: block;
                margin-bottom: 4px;
            }
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="page-wrapper">
    <a href="browse_available_pets.php" class="back-link">← Back to Browse</a>

    <h2><?= htmlspecialchars($pet['name']) ?> (<?= htmlspecialchars($pet['species']) ?>)</h2>

    <?php if (!empty($pet['image'])): ?>
        <img src="../images/pets/<?= htmlspecialchars($pet['image']) ?>" alt="<?= htmlspecialchars($pet['name']) ?>">
    <?php else: ?>
        <p style="text-align:center; font-style: italic;">No image available.</p>
    <?php endif; ?>

    <div class="info-group">
        <p><strong>Breed:</strong> <?= htmlspecialchars($pet['breed']) ?></p>
        <p><strong>Age:</strong> <?= htmlspecialchars($pet['age']) ?> years</p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($pet['gender']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($pet['description']) ?></p>
        <p><strong>Shelter:</strong> <?= htmlspecialchars($pet['shelter_name']) ?></p>
    </div>

    <form method="post" action="apply_adoption.php">
        <input type="hidden" name="pet_id" value="<?= $pet['id'] ?>">
        <button type="submit">Apply to Adopt</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>

