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
        /* Reset box-sizing for predictable spacing */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background-color: #f5f5f5;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        /* Center-wrapper to limit content width */
        .page-wrapper {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
        }

        h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 40px;
        }

        /* Fixed 3-column grid. On narrower screens, collapse gracefully. */
        .pet-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 900px) {
            .pet-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .pet-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Individual card styling */
        .pet-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            transition: box-shadow 0.15s ease, transform 0.15s ease;
        }
        .pet-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        /* Image at top of card, cropped and centered */
        .pet-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        /* Text container inside card */
        .pet-details {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .pet-details strong {
            font-size: 18px;
            margin-bottom: 8px;
        }
        .pet-details p {
            margin: 4px 0;
            font-size: 14px;
            line-height: 1.4;
            color: #555;
        }

        /* Button at bottom of card */
        .black-btn {
            margin-top: auto;
            padding: 8px 16px;
            background-color: #333;
            color: #fff;
            font-size: 14px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }
        .black-btn:hover {
            background-color: #111;
        }

        /* If no records exist */
        .no-pets-msg {
            text-align: center;
            font-size: 16px;
            color: #777;
            margin-top: 60px;
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
                        <img
                            src="../images/pets/<?php echo htmlspecialchars($pet['image']); ?>"
                            alt="Image of <?php echo htmlspecialchars($pet['name']); ?>"
                            class="pet-image"
                        >
                        <div class="pet-details">
                            <strong><?php echo htmlspecialchars($pet['name']); ?></strong>
                            <p>Species: <?php echo htmlspecialchars($pet['species']); ?></p>
                            <p>Breed: <?php echo htmlspecialchars($pet['breed']); ?></p>
                            <p>Adopted on: <?php echo htmlspecialchars($pet['adoption_date']); ?></p>
                            <a href="follow_up_history.php?pet_id=<?php echo $pet['id']; ?>" class="black-btn">
                                View Follow-Up
                            </a>
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
