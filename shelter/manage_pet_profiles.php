<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];

$sql = "SELECT * FROM pets WHERE shelter_id = ? AND status = 'available'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Pet Profiles</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
    <style>
        body {
            background-color: #fff;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
        }

        .pet-card {
            background-color: #f7e6cf;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .pet-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .pet-card h3 {
            margin: 0 0 6px;
            font-size: 18px;
        }

        .pet-card p {
            margin: 4px 0;
            font-size: 14px;
        }

        .pet-card .actions {
            margin-top: 12px;
        }

        .pet-card a.button {
            display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 10px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            margin-right: 10px;
        }

        .pet-card a.button:hover {
            background-color: #222;
        }

        .top-link {
            display: block;
            text-align: center;
            margin-bottom: 25px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="page-wrapper">
    <h2>📂 Manage My Pet Listings</h2>
    <a href="dashboard.php" class="top-link">← Back to Dashboard</a>

    <div class="pet-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($pet = $result->fetch_assoc()): ?>
                <div class="pet-card">
                    <?php if (!empty($pet['image'])): ?>
                        <img src="../images/pets/<?php echo htmlspecialchars($pet['image']); ?>" alt="Pet image">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($pet['name']); ?> (<?php echo htmlspecialchars($pet['species']); ?>)</h3>
                    <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years</p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?></p>
                    <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>

                    <div class="actions">
                        <a href="edit_pet.php?id=<?php echo $pet['id']; ?>" class="button">✏️ Edit</a>
                        <a href="mark_adopted.php?id=<?php echo $pet['id']; ?>" class="button" onclick="return confirm('Mark this pet as adopted?');">✅ Mark Adopted</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">You haven't added any available pets yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>

