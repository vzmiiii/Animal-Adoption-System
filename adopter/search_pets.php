<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    http_response_code(403);
    exit('Unauthorized');
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
if (!empty($type)) $where .= " AND pets.species = '" . $conn->real_escape_string($type) . "'";
if (!empty($min_age)) $where .= " AND pets.age >= " . intval($min_age);
if (!empty($shelter)) $where .= " AND users.username LIKE '%" . $conn->real_escape_string($shelter) . "%'";
if (!empty($keyword)) {
    $escaped = $conn->real_escape_string($keyword);
    $words = preg_split('/\\s+/', $escaped);
    foreach ($words as $word) {
        $word = trim($word);
        if ($word === '') continue;
        $like = "%$word%";
        $where .= " AND (pets.name LIKE '$like' OR pets.breed LIKE '$like' OR pets.description LIKE '$like' OR pets.species LIKE '$like' OR pets.gender LIKE '$like' OR users.username LIKE '$like')";
    }
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
echo '<div class="pet-grid">';
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="pet-card">';
        if (!empty($row['image'])) {
            echo '<img src="../images/pets/' . htmlspecialchars($row['image']) . '" alt="Pet Image">';
        } else {
            echo '<img src="../images/pets/default.jpg" alt="Default Image">';
        }
        echo '<div class="pet-card-content">';
        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
        echo '<div class="pet-meta">';
        echo '<p>' . htmlspecialchars($row['age']) . ' Old | ' . htmlspecialchars($row['species']) . ' | ' . htmlspecialchars($row['breed']) . '</p>';
        echo '<p>Shelter: ' . htmlspecialchars($row['shelter_name']) . '</p>';
        echo '</div>';
        echo '<p class="description">' . nl2br(htmlspecialchars(substr($row['description'], 0, 80))) . '...</p>';
        echo '<a href="view_pet.php?id=' . $row['id'] . '" class="adopt-btn">View Details & Adopt</a>';
        echo '</div></div>';
    }
} else {
    echo '<p style="text-align:center;">No pets available right now.</p>';
}
echo '</div>';
if ($total_pages > 1) {
    echo '<div class="pagination">';
    if ($page > 1) {
        $params = $_GET;
        $params['page'] = $page - 1;
        echo '<a href="#" onclick="return false;" data-page="' . ($page - 1) . '">&laquo; Prev</a>';
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        $params = $_GET;
        $params['page'] = $i;
        $active = ($i == $page) ? 'active' : '';
        echo '<a href="#" onclick="return false;" class="' . $active . '" data-page="' . $i . '">' . $i . '</a>';
    }
    if ($page < $total_pages) {
        $params = $_GET;
        $params['page'] = $page + 1;
        echo '<a href="#" onclick="return false;" data-page="' . ($page + 1) . '">Next &raquo;</a>';
    }
    echo '</div>';
} 