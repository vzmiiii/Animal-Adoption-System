<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');
$shelter_id = $_SESSION['user_id'];

// Pagination
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count pets
$count_sql = "SELECT COUNT(*) as total FROM pets WHERE shelter_id = ? AND status = 'available'";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $shelter_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_pets = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_pets / $limit);

// Fetch pet records
$sql = "SELECT * FROM pets WHERE shelter_id = ? AND status = 'available' LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $shelter_id, $limit, $offset);
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
        * {
            box-sizing: border-box;
            outline: none !important;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            border: none;
            outline: none;
        }

        .page-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            background-color: transparent;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
            color: #222;
        }

        .top-link {
            display: block;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
            color: #333;
            padding: 10px 18px;
            border-radius: 8px;
            background-color: #f0f0f0;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .top-link:hover {
            background-color: #e4e4e4;
        }

        .pet-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .pet-card {
            background-color: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .pet-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .pet-card h3 {
            margin: 0 0 8px;
            font-size: 18px;
            color: #111;
        }

        .pet-card p {
            margin: 4px 0;
            font-size: 14px;
        }

        .actions {
            margin-top: auto;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .button {
            background-color: #000;
            color: #fff;
            padding: 10px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .button:hover {
            background-color: #222;
        }

        .pagination {
            margin-top: 40px;
            text-align: center;
        }

        .pagination a {
            margin: 0 6px;
            padding: 8px 14px;
            background-color: #eee;
            border-radius: 6px;
            text-decoration: none;
            color: #000;
            font-weight: bold;
            transition: all 0.2s ease;
        }

        .pagination a.active {
            background-color: #000;
            color: #fff;
        }

        @media (max-width: 992px) {
            .pet-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .pet-grid {
                grid-template-columns: 1fr;
            }

            h2 {
                font-size: 24px;
            }

            .top-link {
                font-size: 13px;
                padding: 8px 14px;
            }
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="page-wrapper">
    <h2>Manage My Pet Listings</h2>
    <a href="dashboard.php" class="top-link">← Back to Dashboard</a>

    <div class="pet-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($pet = $result->fetch_assoc()): ?>
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
            <p style="text-align:center;">You haven’t added any available pets yet.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
