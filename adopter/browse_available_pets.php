<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

// Filter input
$type = $_GET['type'] ?? '';
$min_age = $_GET['min_age'] ?? '';
$shelter = $_GET['shelter'] ?? '';
$keyword = $_GET['keyword'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Build SQL filter
$where = "WHERE pets.status = 'available'";
if (!empty($type)) $where .= " AND pets.species = '" . $conn->real_escape_string($type) . "'";
if (!empty($min_age)) $where .= " AND pets.age >= " . intval($min_age);
if (!empty($shelter)) $where .= " AND users.username LIKE '%" . $conn->real_escape_string($shelter) . "%'";
if (!empty($keyword)) {
    $escaped = $conn->real_escape_string($keyword);
    $where .= " AND (pets.name LIKE '%$escaped%' OR pets.breed LIKE '%$escaped%' OR pets.description LIKE '%$escaped%')";
}

// Count for pagination
$count_sql = "SELECT COUNT(*) AS total FROM pets JOIN users ON pets.shelter_id = users.id $where";
$count_result = $conn->query($count_sql);
$total_pets = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_pets / $limit);

// Main data query
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
            background-color: #fff;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-wrapper {
            max-width: 1000px;
            margin: 80px auto 40px;
            background-color: #fff;
            padding: 20px 20px 40px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08); /* Softer shadow */
            border-radius: 20px;
        }

        h2 {
            text-align: center;
            font-size: 26px;
            margin-bottom: 25px;
        }

        .top-links {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 25px;
            gap: 10px;
        }

        .top-links a {
            padding: 10px 20px;
            border-radius: 20px;
            background-color: #eee;
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }

        .top-links a:hover {
            background-color: #ddd;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        form input,
        form select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 13px;
        }

        form button,
        .clear-btn {
            background-color: #000;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .clear-btn {
            background-color: #ccc;
            color: #000;
        }

        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-top: 10px;
        }

        .pet-card {
            background-color: #f9f9f9;
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
            margin: 0;
            font-size: 17px;
        }

        .pet-card p {
            margin: 4px 0;
            font-size: 13px;
        }

        .pet-card a {
            margin-top: 10px;
            display: inline-block;
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

        @media (max-width: 600px) {
            .top-links {
                flex-direction: column;
                align-items: center;
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
        <input type="text" name="keyword" placeholder="Search keyword..." value="<?= htmlspecialchars($keyword) ?>">
        <select name="type">
            <option value="">All Types</option>
            <?php
            $types = ['Cat', 'Dog', 'Bird', 'Reptiles', 'Small Mammals', 'Exotic Animals'];
            foreach ($types as $option) {
                echo "<option value=\"$option\"" . ($type === $option ? ' selected' : '') . ">$option</option>";
            }
            ?>
        </select>
        <input type="number" name="min_age" min="0" placeholder="Min Age" value="<?= htmlspecialchars($min_age) ?>">
        <input type="text" name="shelter" placeholder="Shelter name" value="<?= htmlspecialchars($shelter) ?>">
        <button type="submit">Filter</button>
        <a class="clear-btn" href="browse_available_pets.php">Clear Filters</a>
    </form>

    <div class="pet-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="pet-card">
                    <?php if (!empty($row['image'])): ?>
                        <img src="../images/pets/<?= htmlspecialchars($row['image']) ?>" alt="Pet Image">
                    <?php else: ?>
                        <img src="../images/pets/default.jpg" alt="Default Image">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p><?= htmlspecialchars($row['age']) ?> Old</p>
                    <p><?= htmlspecialchars($row['species']) ?></p>
                    <p><?= htmlspecialchars($row['breed']) ?></p>
                    <p>Shelter: <?= htmlspecialchars($row['shelter_name']) ?></p>
                    <p><?= nl2br(htmlspecialchars(substr($row['description'], 0, 100))) ?>...</p>
                    <a href="view_pet.php?id=<?= $row['id'] ?>">Adopt Now</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No pets available right now.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
