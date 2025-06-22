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
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        body {
            background: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .content-container {
            max-width: 1200px;
            margin: 80px auto 40px;
            padding: 40px;
            background: linear-gradient(to right, #f4f6f8, #dde1e7);
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
        }
        .pet-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        .pet-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #e0e0e0;
        }
        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
        }
        .pet-card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .pet-card-body { padding: 1.5rem; flex-grow: 1; }
        .pet-card-body h3 { margin-top: 0; margin-bottom: 0.5rem; font-size: 1.5rem; color: #333; }
        .pet-card-body .pet-breed { font-style: italic; color: #666; margin-bottom: 1rem; }
        .pet-card-body p { margin: 0.5rem 0; line-height: 1.6; }
        .pet-card-body .pet-description { color: #555; margin-top: 1rem; }
        .pet-card-footer {
            padding: 1rem 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .empty-message {
            grid-column: 1 / -1;
            text-align: center;
            font-size: 1.2rem;
            color: #777;
            padding: 3rem;
            background: rgba(255,255,255,0.7);
            border-radius: 12px;
        }
        .pagination {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }
        .pagination a {
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            background-color: #fff;
            color: #333;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 1px solid #e0e0e0;
        }
        .pagination a:hover {
            background-image: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: #fff;
        }
        .pagination a.active {
            background-image: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: #fff;
            box-shadow: 0 6px 12px rgba(78, 140, 255, 0.4);
        }
        .button-danger {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            color: #e53e3e;
            background-color: #fff;
            border: 1px solid #e53e3e;
            transition: all 0.3s ease;
            text-align: center;
        }
        .button-danger:hover {
            background-color: #e53e3e;
            color: #fff;
            box-shadow: 0 4px 10px rgba(229, 62, 62, 0.3);
            transform: translateY(-2px);
            }
        .button {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            color: #444;
            background-color: #fff;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
            text-align: center;
            }
        .button:hover {
            background-image: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(78, 140, 255, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="content-container">
    <div class="page-header">
        <h1>Manage My Pet Listings</h1>
        <a href="dashboard.php" class="button">← Back to Dashboard</a>
    </div>

    <div class="pet-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($pet = $result->fetch_assoc()): ?>
                <div class="pet-card">
                    <?php if (!empty($pet['image'])): ?>
                        <img src="../images/pets/<?php echo htmlspecialchars($pet['image']); ?>" alt="Pet image" class="pet-card-img">
                    <?php else: ?>
                        <img src="../images/pets/default.png" alt="Default pet image" class="pet-card-img">
                    <?php endif; ?>
                    <div class="pet-card-body">
                        <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                        <p class="pet-breed"><?php echo htmlspecialchars($pet['species']); ?> - <?php echo htmlspecialchars($pet['breed']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years</p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?></p>
                        <p class="pet-description"><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>
                    </div>
                    <div class="pet-card-footer">
                        <a href="edit_pet.php?id=<?php echo $pet['id']; ?>" class="button">✏️ Edit</a>
                        <a href="mark_adopted.php?id=<?php echo $pet['id']; ?>" class="button-danger" onclick="return confirm('Mark this pet as adopted?');">✅ Mark Adopted</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-message">You haven't added any available pets yet.</p>
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
