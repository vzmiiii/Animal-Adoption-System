<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];
$sql = "SELECT * FROM pets WHERE shelter_id = ? AND status = 'adopted' ORDER BY adoption_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adopted Pets</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
    <style>
        body {
            background-color: #ffffff;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .page-wrapper {
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .pet-card {
            background-color: #fce7cd;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .pet-image {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .pet-details {
            background: #fff;
            padding: 10px;
            border-radius: 8px;
        }

        .black-btn {
            display: inline-block;
            background-color: black;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 8px;
        }

        p {
            text-align: center;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_shelter.php'); ?>

<div class="page-wrapper">
    <h2>List of Adopted Pets</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="pet-grid">
            <?php while ($pet = $result->fetch_assoc()): ?>
                <div class="pet-card">
                    <img src="../images/pets/<?php echo htmlspecialchars($pet['image']); ?>" alt="Pet Image" class="pet-image">
                    <div class="pet-details">
                        <strong><?php echo htmlspecialchars($pet['name']); ?></strong><br>
                        Species: <?php echo htmlspecialchars($pet['species']); ?><br>
                        Breed: <?php echo htmlspecialchars($pet['breed']); ?><br>
                        Adopted on: <?php echo htmlspecialchars($pet['adoption_date']); ?><br><br>
                        <a href="follow_up_history.php?pet_id=<?php echo $pet['id']; ?>" class="black-btn">View Follow-Up History</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No pets have been marked as adopted yet.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
