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

// If pet not found or already adopted
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
    <title>View Pet - <?php echo htmlspecialchars($pet['name']); ?></title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #ffffff;
            color: #333;
        }

        .page-wrapper {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        a.back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }

        h2 {
            margin-top: 0;
            font-size: 28px;
            text-align: center;
        }

        img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 20px auto;
            border-radius: 12px;
        }

        p {
            font-size: 16px;
            margin: 8px 0;
        }

        .info-group strong {
            display: inline-block;
            width: 100px;
        }

        form {
            text-align: center;
            margin-top: 30px;
        }

        button {
            background-color: black;
            color: white;
            border: none;
            padding: 12px 28px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="page-wrapper">
    <a href="browse_available_pets.php" class="back-link">← Back to Browse</a>

    <h2><?php echo htmlspecialchars($pet['name']); ?> (<?php echo htmlspecialchars($pet['species']); ?>)</h2>

    <?php if (!empty($pet['image'])): ?>
        <img src="../images/pets/<?php echo htmlspecialchars($pet['image']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
    <?php else: ?>
        <p style="text-align: center; font-style: italic;">No image available.</p>
    <?php endif; ?>

    <div class="info-group">
        <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years</p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($pet['description']); ?></p>
        <p><strong>Shelter:</strong> <?php echo htmlspecialchars($pet['shelter_name']); ?></p>
    </div>

    <form method="post" action="apply_adoption.php">
        <input type="hidden" name="pet_id" value="<?php echo $pet['id']; ?>">
        <button type="submit">Apply to Adopt</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
