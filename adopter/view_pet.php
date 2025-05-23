<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: login.php");
    exit();
}

include('../db_connection.php');

if (!isset($_GET['id'])) {
    echo "No pet ID provided.";
    exit();
}

$pet_id = intval($_GET['id']);

$sql = "SELECT pets.*, users.username AS shelter_name 
        FROM pets
        JOIN users ON pets.shelter_id = users.id
        WHERE pets.id = $pet_id AND pets.status = 'available'";
$result = $conn->query($sql);

if ($result->num_rows !== 1) {
    echo "Pet not found or already adopted.";
    exit();
}

$pet = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Pet - <?php echo htmlspecialchars($pet['name']); ?></title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: white;
            color: #333;
        }

        .page-wrapper {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f7e6cf;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        a.back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 0 auto 20px;
            border-radius: 8px;
        }

        p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        form {
            text-align: center;
            margin-top: 20px;
        }

        button {
            background-color: black;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #333;
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
    <?php endif; ?>

    <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
    <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years</p>
    <p><strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?></p>
    <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>
    <p><strong>Shelter:</strong> <?php echo htmlspecialchars($pet['shelter_name']); ?></p>

    <form method="post" action="apply_adoption.php">
        <input type="hidden" name="pet_id" value="<?php echo $pet['id']; ?>">
        <button type="submit">Apply to Adopt</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>

