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
        :root {
            --primary-gradient: linear-gradient(to right, #f4f6f8, #dde1e7);
            --accent-gradient: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            --text-color: #333;
            --text-color-light: #555;
            --container-bg: rgba(255, 255, 255, 0.92);
            --border-color: #e0e0e0;
            --shadow: 0 8px 25px rgba(0,0,0,0.1);
            --border-radius: 16px;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-color);
        }

        .page-wrapper {
            max-width: 800px;
            margin: 80px auto 40px;
            padding: 40px;
            background: var(--container-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .back-link {
            align-self: flex-start;
            margin-bottom: 25px;
            color: var(--text-color-light);
            font-size: 15px;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #4e8cff;
        }

        h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }

        .pet-image {
            width: 100%;
            max-width: 400px;
            height: auto;
            border-radius: var(--border-radius);
            margin-bottom: 35px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 40px;
            width: 100%;
            text-align: left;
            margin-bottom: 20px;
        }

        .info-item {
            font-size: 16px;
            line-height: 1.6;
        }

        .info-item strong {
            display: block;
            font-weight: 600;
            color: var(--text-color-light);
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .description {
            grid-column: 1 / -1; /* Span full width */
        }

        form {
            text-align: center;
            margin-top: 35px;
        }

        button {
            background: var(--accent-gradient);
            color: #fff;
            border: none;
            padding: 15px 40px;
            font-size: 16px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.18);
        }

        @media (max-width: 768px) {
            .page-wrapper {
                margin: 40px 20px;
                padding: 30px;
            }
             h2 {
                font-size: 28px;
            }
        }
        
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 20px;
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
        <img src="../images/pets/<?= htmlspecialchars($pet['image']) ?>" alt="<?= htmlspecialchars($pet['name']) ?>" class="pet-image">
    <?php else: ?>
        <p style="text-align:center; font-style: italic;">No image available.</p>
    <?php endif; ?>

    <div class="info-grid">
        <div class="info-item">
            <strong>Breed</strong>
            <?= htmlspecialchars($pet['breed']) ?>
        </div>
        <div class="info-item">
            <strong>Age</strong>
             <?= htmlspecialchars($pet['age']) ?> years
        </div>
        <div class="info-item">
            <strong>Gender</strong>
            <?= htmlspecialchars($pet['gender']) ?>
        </div>
        <div class="info-item">
            <strong>Shelter</strong>
            <?= htmlspecialchars($pet['shelter_name']) ?>
        </div>
        <div class="info-item description">
            <strong>Description</strong>
            <?= nl2br(htmlspecialchars($pet['description'])) ?>
        </div>
    </div>

    <form method="post" action="apply_adoption.php">
        <input type="hidden" name="pet_id" value="<?= $pet['id'] ?>">
        <button type="submit">Apply to Adopt</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>

