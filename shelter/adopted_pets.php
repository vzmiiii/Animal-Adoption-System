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
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        .content-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }
        .page-header {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 30px;
        }
        .pet-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        @media (max-width: 992px) {
            .pet-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .pet-grid {
                grid-template-columns: 1fr;
            }
        }
        .pet-card {
            background-color: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .pet-card-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .pet-card-body {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        .pet-card-body h3 {
            margin: 0 0 0.5rem 0;
            font-size: 22px;
            color: #333;
        }
        .pet-card-body p {
            margin: 0.25rem 0;
            color: #666;
            font-size: 15px;
            line-height: 1.5;
        }
        .pet-card-body .adoption-date {
            font-weight: 600;
            color: #4CAF50;
            margin-top: 0.5rem;
        }
        .pet-card-footer {
            margin-top: auto;
            padding-top: 1rem;
            display: flex;
            justify-content: center;
        }
        .button {
            display: block;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            color: #fff;
            background-image: linear-gradient(90deg, #4e8cff 0%, #6ed6a5 100%);
            border: none;
            transition: all 0.3s ease;
            text-align: center;
        }
        .button:hover {
            box-shadow: 0 4px 15px rgba(78, 140, 255, 0.4);
            transform: translateY(-2px) scale(1.02);
        }
        .no-pets-msg {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-top: 4rem;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <?php include('../includes/navbar_shelter.php'); ?>

    <div class="content-container">
        <h2 class="page-header">List of Adopted Pets</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="pet-grid">
                <?php while ($pet = $result->fetch_assoc()): ?>
                    <div class="pet-card">
                        <img
                            src="../images/pets/<?php echo htmlspecialchars($pet['image']); ?>"
                            alt="Image of <?php echo htmlspecialchars($pet['name']); ?>"
                            class="pet-card-img"
                        >
                        <div class="pet-card-body">
                            <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                            <p><strong>Species:</strong> <?php echo htmlspecialchars($pet['species']); ?></p>
                            <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
                            <p class="adoption-date">Adopted on: <?php echo date("F j, Y", strtotime($pet['adoption_date'])); ?></p>
                            <div class="pet-card-footer">
                                <a href="follow_up_history.php?pet_id=<?php echo $pet['id']; ?>" class="button">
                                    View Follow-Up History
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-pets-msg">No pets have been marked as adopted yet.</p>
        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
