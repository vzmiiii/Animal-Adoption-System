<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$type = $_GET['type'] ?? '';
$min_age = $_GET['min_age'] ?? '';
$shelter = $_GET['shelter'] ?? '';
$keyword = $_GET['keyword'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$where = "WHERE pets.status = 'available'";
if (!empty($type)) {
    $where .= " AND pets.species = '" . $conn->real_escape_string($type) . "'";
}
if (!empty($min_age)) {
    $where .= " AND pets.age >= " . intval($min_age);
}
if (!empty($shelter)) {
    $where .= " AND users.username LIKE '%" . $conn->real_escape_string($shelter) . "%'";
}
if (!empty($keyword)) {
    $escaped = $conn->real_escape_string($keyword);
    $where .= " AND (
        pets.name LIKE '%$escaped%' OR 
        pets.breed LIKE '%$escaped%' OR 
        pets.description LIKE '%$escaped%'
    )";
}

$count_sql = "SELECT COUNT(*) AS total FROM pets JOIN users ON pets.shelter_id = users.id $where";
$count_result = $conn->query($count_sql);
$total_pets = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_pets / $limit);

$sql = "SELECT pets.*, users.username AS shelter_name
        FROM pets
        JOIN users ON pets.shelter_id = users.id
        $where
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Available Pets</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        body {
            background-color: #ffffff;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-wrapper {
            background-color: #ffffff;
            box-shadow: 0 0 8px rgba(0, 51, 102, 0.1);
            padding: 0 20px 40px;
            max-width: 1000px;
            margin: 80px auto 40px;
        }

        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        form input,
        form select {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 13px;
            max-width: 150px;
        }

        form button {
            padding: 8px 16px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 16px;
            font-size: 13px;
            font-weight: bold;
        }

        .top-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .top-links a {
            background-color: #eee;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .top-links a:hover {
            background-color: #ddd;
        }

        .pet-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Exactly 3 per row */
    gap: 20px;
    justify-items: center;
}

.pet-card {
    width: 90%; /* Shrinks card width */
    max-width: 280px;
    background-color: #f7e6cf;
    border-radius: 20px;
    padding: 16px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.pet-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 10px;
}

.pet-card h3 {
    margin: 0 0 6px;
    font-size: 17px;
}

.pet-card p {
    margin: 3px 0;
    font-size: 13px;
}

.pet-card a {
    display: inline-block;
    margin-top: 10px;
    background-color: #000;
    color: #fff;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 13px;
    text-decoration: none;
    font-weight: bold;
}
        .pagination {
            text-align: center;
            margin-top: 30px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            border-radius: 12px;
            background-color: #eee;
            color: #333;
            text-decoration: none;
            font-weight: bold;
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
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="page-wrapper">
    <h2>Available Pets for Adoption</h2>

    <div class="top-links">
        <a href="compatibility_quiz.php">🧩 Compatibility Quiz</a>
        <a href="adoption_tracker.php">📋 Adoption Tracker</a>
    </div>

    <form method="get">
        <input type="text" name="keyword" placeholder="Search keyword..." value="<?php echo htmlspecialchars($keyword); ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="Cat" <?php if ($type === 'Cat') echo 'selected'; ?>>Cat</option>
            <option value="Dog" <?php if ($type === 'Dog') echo 'selected'; ?>>Dog</option>
            <option value="Bird" <?php if ($type === 'Bird') echo 'selected'; ?>>Bird</option>
            <option value="Reptiles" <?php if ($type === 'Reptiles') echo 'selected'; ?>>Reptiles</option>
            <option value="Small Mammals" <?php if ($type === 'Small Mammals') echo 'selected'; ?>>Small Mammals</option>
            <option value="Exotic Animals" <?php if ($type === 'Exotic Animals') echo 'selected'; ?>>Exotic Animals</option>
        </select>
        <input type="number" name="min_age" min="0" placeholder="Min Age" value="<?php echo htmlspecialchars($min_age); ?>">
        <input type="text" name="shelter" placeholder="Shelter name" value="<?php echo htmlspecialchars($shelter); ?>">
        <button type="submit">Filter</button>
        <a href="browse_available_pets.php" style="background:#ccc; padding:8px 16px; border-radius:16px; text-decoration:none; color:#000;">Clear Filters</a>
    </form>

    <div class="pet-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="pet-card">
                    <?php if (!empty($row['image'])): ?>
                        <img src="../images/pets/<?php echo htmlspecialchars($row['image']); ?>" alt="Pet Image">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['age']); ?> Old</p>
                    <p><?php echo htmlspecialchars($row['species']); ?></p>
                    <p><?php echo htmlspecialchars($row['breed']); ?></p>
                    <p><?php echo htmlspecialchars($row['shelter_name']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                    <a href="view_pet.php?id=<?php echo $row['id']; ?>">Adopt Now</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No pets available right now.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">&laquo; Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                   class="<?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
