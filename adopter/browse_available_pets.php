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
    // Smart keyword: split into words, each must match any field
    $words = preg_split('/\s+/', $escaped);
    foreach ($words as $word) {
        $word = trim($word);
        if ($word === '') continue;
        $like = "%$word%";
        $where .= " AND (pets.name LIKE '$like' OR pets.breed LIKE '$like' OR pets.description LIKE '$like' OR pets.species LIKE '$like' OR pets.gender LIKE '$like' OR users.username LIKE '$like')";
    }
}

// Count for pagination
$count_sql = "SELECT COUNT(*) AS total FROM pets JOIN users ON pets.shelter_id = users.id $where";
$count_result = $conn->query($count_sql);
$total_pets = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_pets / $limit);

// Fetch all active shelter usernames for dropdown
$shelter_options = [];
$shelter_sql = "SELECT DISTINCT username FROM users WHERE role='shelter' AND status='active' ORDER BY username ASC";
$shelter_result = $conn->query($shelter_sql);
if ($shelter_result) {
    while ($row = $shelter_result->fetch_assoc()) {
        $shelter_options[] = $row['username'];
    }
}

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
    :root {
        --primary-gradient: linear-gradient(to right, #f4f6f8, #dde1e7);
        --sidebar-bg: #d5d0b0;
        --accent-gradient: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
        --text-color: #333;
        --text-color-light: #444;
        --container-bg: rgba(255,255,255,0.92);
        --border-color: #e0e0e0;
        --shadow: 0 5px 15px rgba(0,0,0,0.08);
        --border-radius: 12px;
    }
    .page-wrapper {
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        background: var(--primary-gradient);
        background-color: var(--container-bg);
        -webkit-backdrop-filter: blur(8px);
        backdrop-filter: blur(8px);
        max-width: 1200px;
        margin: 80px auto 40px;
        padding: 40px;
    }

    h2 {
        text-align: center;
        font-size: 36px;
        font-weight: 700;
        color: var(--text-color-light);
        margin-bottom: 30px;
    }

    .top-links {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 30px;
    }

    .top-links a {
        padding: 12px 25px;
        border-radius: 50px;
        background: var(--primary-gradient);
        color: var(--text-color);
        text-decoration: none;
        font-weight: 600;
        transition: transform 0.2s, box-shadow 0.2s, background 0.2s, color 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.07);
        border: 1px solid var(--border-color);
    }

    .top-links a:hover {
        background: var(--accent-gradient);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }

    form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-bottom: 40px;
        background: none;
        padding: 0;
        box-shadow: none;
        border-radius: 0;
    }

    .input-group {
        display: flex;
        flex-grow: 1;
        background: #fff;
        border-radius: 12px;
        box-shadow: var(--shadow);
        overflow: hidden;
        border: 1px solid var(--border-color);
        min-width: 200px;
    }

    form input,
    form select {
        flex-grow: 1;
        width: auto;
        padding: 14px 20px;
        border: none;
        border-radius: 0;
        font-size: 15px;
        background: none;
        color: var(--text-color);
        transition: none;
        box-shadow: none;
    }

    .input-group > *:not(:last-child) {
        border-right: 1px solid var(--border-color);
    }

    form input:focus,
    form select:focus {
        outline: none;
        background-color: #f7f7f7;
        box-shadow: none;
    }

    form select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 20px center;
        background-size: 12px;
        padding-right: 45px;
    }

    form button,
    .clear-btn {
        padding: 14px 25px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        text-align: center;
        border: 1px solid var(--border-color);
        background: #fff;
        box-shadow: var(--shadow);
    }

    form button {
        background: #fff;
        color: var(--text-color-light);
        border-color: var(--border-color);
    }
    
    .clear-btn {
        background: #fdfdfd;
        color: var(--text-color-light);
    }

    form button:hover,
    .clear-btn:hover {
        background: var(--accent-gradient);
        color: #fff;
        border-color: transparent;
        transform: translateY(-2px);
    }

    .pet-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }

    .pet-card {
        background: var(--container-bg);
        border-radius: var(--border-radius);
        padding: 0;
        text-align: center;
        box-shadow: var(--shadow);
        transition: transform 0.3s, box-shadow 0.3s;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border: 1px solid var(--border-color);
    }

    .pet-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.13);
        border-color: #6ed6a5;
    }

    .pet-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: #dde1e7;
    }
    
    .pet-card-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .pet-card h3 {
        margin: 0 0 8px;
        font-size: 22px;
        color: var(--text-color-light);
    }

    .pet-card p {
        margin: 4px 0;
        font-size: 14px;
        color: var(--text-color);
        line-height: 1.5;
    }
    
    .pet-card .pet-meta {
        margin-bottom: 15px;
    }

    .pet-card .description {
        flex-grow: 1;
        margin-bottom: 15px;
    }

    .pet-card a.adopt-btn {
        margin-top: auto;
        display: inline-block;
        background: var(--primary-gradient);
        color: var(--text-color);
        padding: 12px 20px;
        border-radius: 50px;
        font-size: 14px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.2s, color 0.2s;
        border: 1px solid var(--border-color);
    }

    .pet-card a.adopt-btn:hover {
        background: var(--accent-gradient);
        color: #fff;
        border-color: #6ed6a5;
    }

    .pagination {
        text-align: center;
        margin-top: 40px;
    }

    .pagination a {
        margin: 0 5px;
        padding: 10px 18px;
        border-radius: var(--border-radius);
        background: #fff;
        color: var(--text-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
        border: 1px solid var(--border-color);
    }

    .pagination a:hover {
        background: var(--accent-gradient);
        color: #fff;
        border-color: #6ed6a5;
    }

    .pagination a.active {
        background: var(--accent-gradient);
        color: #fff;
        border-color: #6ed6a5;
        box-shadow: 0 2px 5px rgba(110, 214, 165, 0.18);
    }

    @media (max-width: 1200px) {
        .page-wrapper {
            margin-left: 20px;
            margin-right: 20px;
        }
    }
    @media (max-width: 992px) {
        .pet-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        form {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }
        .input-group {
            flex-direction: column;
        }
        .input-group > *:not(:last-child) {
            border-right: none;
            border-bottom: 1px solid var(--border-color);
        }
        .form-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
    }

    @media (max-width: 768px) {
        .page-wrapper {
            padding: 25px;
        }
        form {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }
        .input-group {
            flex-direction: column;
        }
        .input-group > *:not(:last-child) {
            border-right: none;
            border-bottom: 1px solid var(--border-color);
        }
        .form-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        h2 {
            font-size: 28px;
        }
        .top-links {
            flex-direction: column;
            align-items: center;
        }
    }

    @media (max-width: 576px) {
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

    <form method="get" id="filter-form">
        <div class="input-group">
            <input type="text" id="keyword" name="keyword" placeholder="Search keyword..." value="<?= htmlspecialchars($keyword) ?>">
            <select name="type" id="type">
                <option value="">All Types</option>
                <?php
                $types = ['Cat', 'Dog', 'Bird', 'Reptile', 'Small Mammal', 'Exotic Animal'];
                foreach ($types as $option) {
                    echo "<option value=\"$option\"" . ($type === $option ? ' selected' : '') . ">$option</option>";
                }
                ?>
            </select>
            <input type="number" id="min_age" name="min_age" min="0" placeholder="Min Age" value="<?= htmlspecialchars($min_age) ?>">
            <select name="shelter" id="shelter">
                <option value="">All Shelters</option>
                <?php foreach ($shelter_options as $option): ?>
                    <option value="<?= htmlspecialchars($option) ?>" <?= $shelter === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-buttons">
            <button type="submit">Filter</button>
            <a class="clear-btn" href="browse_available_pets.php">Clear Filters</a>
        </div>
    </form>

    <div id="pet-grid-ajax">
    <div class="pet-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="pet-card">
                    <?php if (!empty($row['image'])): ?>
                        <img src="../images/pets/<?= htmlspecialchars($row['image']) ?>" alt="Pet Image">
                    <?php else: ?>
                        <img src="../images/pets/default.jpg" alt="Default Image">
                    <?php endif; ?>
                    <div class="pet-card-content">
                        <h3><?= htmlspecialchars($row['name']) ?></h3>
                        <div class="pet-meta">
                            <p><?= htmlspecialchars($row['age']) ?> Old | <?= htmlspecialchars($row['species']) ?> | <?= htmlspecialchars($row['breed']) ?></p>
                            <p>Shelter: <?= htmlspecialchars($row['shelter_name']) ?></p>
                        </div>
                        <p class="description"><?= nl2br(htmlspecialchars(substr($row['description'], 0, 80))) ?>...</p>
                        <a href="view_pet.php?id=<?= $row['id'] ?>" class="adopt-btn">View Details & Adopt</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No pets available right now.</p>
        <?php endif; ?>
    </div>
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Prev</a>
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
    <script>
    // Live search for keyword
    const keywordInput = document.getElementById('keyword');
    const petGridAjax = document.getElementById('pet-grid-ajax');
    const typeInput = document.getElementById('type');
    const minAgeInput = document.getElementById('min_age');
    const shelterInput = document.getElementById('shelter');
    let lastRequest = null;
    function fetchPets() {
        const params = new URLSearchParams({
            keyword: keywordInput.value,
            type: typeInput.value,
            min_age: minAgeInput.value,
            shelter: shelterInput.value
        });
        if (lastRequest) lastRequest.abort();
        lastRequest = new XMLHttpRequest();
        lastRequest.open('GET', 'search_pets.php?' + params.toString(), true);
        lastRequest.onreadystatechange = function() {
            if (lastRequest.readyState === 4 && lastRequest.status === 200) {
                petGridAjax.innerHTML = lastRequest.responseText;
            }
        };
        lastRequest.send();
    }
    keywordInput.addEventListener('input', function() {
        fetchPets();
    });
    // Optionally, add live search for other filters too:
    // typeInput.addEventListener('change', fetchPets);
    // minAgeInput.addEventListener('input', fetchPets);
    // shelterInput.addEventListener('change', fetchPets);
    </script>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>

