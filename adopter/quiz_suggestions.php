<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    http_response_code(403);
    exit('Unauthorized');
}
include('../db_connection.php');
// Get all quiz params
$lifestyle = $_GET['lifestyle'] ?? '';
$home_type = $_GET['home_type'] ?? '';
$experience = $_GET['experience'] ?? '';
$has_kids = $_GET['has_kids'] ?? '';
$preferred_gender = $_GET['preferred_gender'] ?? '';
$preferred_age = $_GET['preferred_age'] ?? '';
$noise_level = $_GET['noise_level'] ?? '';
$likes_cuddle = $_GET['likes_cuddle'] ?? '';
// Start with all species
$all_species = ['Cat', 'Dog', 'Reptile', 'Small Mammal', 'Bird', 'Exotic Pet'];
$allowed_species = $all_species;
// Lifestyle
if ($lifestyle === 'active') {
    $allowed_species = array_intersect($allowed_species, ['Cat', 'Dog']);
} else if ($lifestyle) {
    $allowed_species = array_intersect($allowed_species, ['Cat', 'Reptile', 'Small Mammal', 'Bird', 'Exotic Pet']);
}
// Home Type
if (in_array(strtolower($home_type), ['apartment', 'flat', 'highrise', 'high-rise apartment'])) {
    $allowed_species = array_intersect($allowed_species, ['Cat', 'Reptile', 'Small Mammal', 'Bird', 'Exotic Pet']);
}
// Experience
if ($experience === 'first_time') {
    $allowed_species = array_intersect($allowed_species, ['Cat', 'Small Mammal', 'Reptile']);
}
// Children
if ($has_kids === 'yes') {
    $allowed_species = array_diff($allowed_species, ['Exotic Pet']);
}
// Quiet
if ($noise_level === 'quiet') {
    $allowed_species = array_intersect($allowed_species, ['Reptile', 'Small Mammal', 'Bird', 'Exotic Pet']);
}
// Cuddly
if ($likes_cuddle === 'yes') {
    $allowed_species = array_intersect($allowed_species, ['Cat', 'Dog']);
} else if ($likes_cuddle) {
    $allowed_species = array_diff($allowed_species, ['Cat', 'Dog']);
}
// Build SQL
if (empty($allowed_species)) {
    echo '<div class="results-wrapper"><h2>Live Pet Suggestions</h2><p class="empty-msg">No pets matched your criteria. Try adjusting your preferences!</p></div>';
    exit;
}
$species_sql = implode(",", array_map(function($s) use ($conn) { return "'" . $conn->real_escape_string($s) . "'"; }, $allowed_species));
$sql = "SELECT p.*, u.username AS shelter_name
        FROM pets p
        JOIN users u ON p.shelter_id = u.id
        WHERE p.status = 'available' AND p.species IN ($species_sql)";
if ($preferred_gender && $preferred_gender !== 'no_pref') {
    $sql .= " AND p.gender = '" . $conn->real_escape_string($preferred_gender) . "'";
}
if ($preferred_age) {
    if ($preferred_age === 'young') {
        $sql .= " AND p.age <= 2";
    } elseif ($preferred_age === 'adult') {
        $sql .= " AND p.age > 2 AND p.age <= 7";
    } elseif ($preferred_age === 'senior') {
        $sql .= " AND p.age > 7";
    }
}
$result = $conn->query($sql);
if ($result) {
    echo '<div class="results-wrapper"><h2>Live Pet Suggestions</h2>';
    if ($result->num_rows > 0) {
        echo '<div class="recommended-grid">';
        while ($pet = $result->fetch_assoc()) {
            echo '<div class="pet-card">';
            if (!empty($pet['image'])) {
                echo '<img src="../images/pets/' . htmlspecialchars($pet['image']) . '" alt="Pet Image">';
            } else {
                echo '<img src="../images/pets/default.jpg" alt="Default Image">';
            }
            echo '<div class="pet-card-content">';
            echo '<h3>' . htmlspecialchars($pet['name']) . '</h3>';
            echo '<p>' . htmlspecialchars($pet['breed']) . '</p>';
            echo '<p class="description">' . htmlspecialchars($pet['age']) . ' years old | ' . htmlspecialchars($pet['gender']) . ' | ' . htmlspecialchars($pet['species']) . '</p>';
            echo '<p><strong>Shelter:</strong> ' . htmlspecialchars($pet['shelter_name']) . '</p>';
            echo '<a href="view_pet.php?id=' . $pet['id'] . '" class="adopt-btn">View Details</a>';
            echo '</div></div>';
        }
        echo '</div>';
    } else {
        echo '<p class="empty-msg">No pets matched your criteria. Try adjusting your preferences!</p>';
    }
    echo '</div>';
} 