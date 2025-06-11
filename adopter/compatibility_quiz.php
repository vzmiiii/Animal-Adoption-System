<?php
session_start();
// Ensure user is adopter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$suggestions = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect answers
    $lifestyle = $_POST['lifestyle'];
    $home_type = $_POST['home_type'];
    $experience = $_POST['experience'];
    $has_kids = $_POST['has_kids'];
    $preferred_gender = $_POST['preferred_gender'];
    $preferred_age = $_POST['preferred_age'];
    $noise_level = $_POST['noise_level'];
    $likes_cuddle = $_POST['likes_cuddle'];

    $sql = "SELECT * FROM pets WHERE status = 'available'";

    // Lifestyle affects species
    if ($lifestyle === "active") {
        $sql .= " AND species = 'Dog'";
    } elseif ($lifestyle === "quiet") {
        $sql .= " AND species = 'Cat'";
    }

    // Home type affects species and age/size compatibility
    if (in_array($home_type, ["highrise", "flat"])) {
        $sql .= " AND (species != 'Dog' OR age <= 5)"; // no large dogs in small homes
    }

    // Experience level affects ease of handling
    if ($experience === "first_time") {
        $sql .= " AND age <= 3";
    }

    // Kids should be paired with younger, gentler pets
    if ($has_kids === "yes") {
        $sql .= " AND age <= 5";
    }

    // Gender preference
    if ($preferred_gender !== "no_pref") {
        $sql .= " AND gender = '" . $conn->real_escape_string($preferred_gender) . "'";
    }

    // Age range
    if ($preferred_age === "young") {
        $sql .= " AND age <= 2";
    } elseif ($preferred_age === "adult") {
        $sql .= " AND age > 2 AND age <= 7";
    } elseif ($preferred_age === "senior") {
        $sql .= " AND age > 7";
    }

    // Noise preference
    if ($noise_level === "quiet") {
        $sql .= " AND species = 'Cat'";
    }

    // Cuddly pets assumed to be younger and calmer
    if ($likes_cuddle === "yes") {
        $sql .= " AND age <= 4";
    }

    // Execute query
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Compatibility Quiz</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

.page-wrapper {
    width: 90%;
    max-width: 700px;
    margin: 40px auto;
    padding: 40px 50px;
    background-color: #fef9ec;
    border-radius: 25px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.05);
}

h2 {
    font-size: 26px;
    font-weight: 600;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title {
    margin-top: 30px;
    margin-bottom: 10px;
    font-size: 18px;
    color: #444;
    border-left: 4px solid #4caf50;
    padding-left: 10px;
}

form .form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

form label {
    font-weight: 500;
    margin-bottom: 6px;
}

form select {
    padding: 12px;
    border-radius: 10px;
    font-size: 15px;
    border: 1px solid #ccc;
}

form button {
    background-color: #000;
    color: white;
    border: none;
    padding: 14px 20px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
    width: 100%;
}

form button:hover {
    background-color: #333;
    transform: scale(1.02);
}

.recommended-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 30px;
}

.pet-card {
    background-color: #fff;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    text-align: center;
    transition: transform 0.2s ease;
}

.pet-card:hover {
    transform: scale(1.02);
}

.pet-card img {
    width: 100%;
    max-height: 180px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 12px;
}

.pet-card h3 {
    margin: 0;
    font-size: 20px;
    color: #222;
}

.pet-card p {
    margin: 6px 0;
    font-size: 14px;
    color: #555;
}

.pet-card a {
    margin-top: 10px;
    display: inline-block;
    background-color: #000;
    color: #fff;
    padding: 10px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
}

p.no-result {
    font-style: italic;
    color: #555;
    margin-top: 30px;
    text-align: center;
}
</style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="page-wrapper">
    <h2>🧩 Compatibility Quiz</h2>

    <form method="post">

    <h3 class="section-title">Your Lifestyle</h3>

    <div class="form-group">
        <label>Your Lifestyle:</label>
        <select name="lifestyle" required>
            <option value="active">Active & Outdoorsy</option>
            <option value="quiet">Calm & Indoors</option>
        </select>
    </div>

    <div class="form-group">
        <label>Your Home Type:</label>
        <select name="home_type" required>
            <option value="bungalow">Bungalow</option>
            <option value="terrace">Terrace</option>
            <option value="highrise">High-Rise Apartment</option>
            <option value="flat">Flat</option>
        </select>
    </div>

    <div class="form-group">
        <label>Pet Experience Level:</label>
        <select name="experience" required>
            <option value="first_time">First-time Pet Owner</option>
            <option value="experienced">Experienced</option>
        </select>
    </div>

    <div class="form-group">
        <label>Do You Have Children?</label>
        <select name="has_kids" required>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>
    </div>

    <h3 class="section-title">Pet Preferences</h3>

    <div class="form-group">
        <label>Preferred Pet Gender:</label>
        <select name="preferred_gender" required>
            <option value="no_pref">No Preference</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
    </div>

    <div class="form-group">
        <label>Preferred Pet Age:</label>
        <select name="preferred_age" required>
            <option value="young">0–2 years</option>
            <option value="adult">3–7 years</option>
            <option value="senior">8+ years</option>
        </select>
    </div>

    <div class="form-group">
        <label>Do You Prefer a Quiet Pet?</label>
        <select name="noise_level" required>
            <option value="quiet">Yes, I prefer quiet pets</option>
            <option value="any">No preference</option>
        </select>
    </div>

    <div class="form-group">
        <label>Do You Want a Pet That Likes to Cuddle?</label>
        <select name="likes_cuddle" required>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>
    </div>

    <button type="submit">Get Recommendations</button>
</form>

    <div class="recommended-grid">
<?php foreach ($suggestions as $pet): ?>
    <div class="pet-card">
        <?php if (!empty($pet['image'])): ?>
            <img src="../images/pets/<?php echo htmlspecialchars($pet['image']); ?>" alt="Pet image">
        <?php else: ?>
            <img src="../images/pets/default.jpg" alt="Default image">
        <?php endif; ?>
        <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
        <p><?php echo htmlspecialchars($pet['species']); ?> • 
           <?php echo htmlspecialchars($pet['age']) . " yrs"; ?> • 
           <?php echo htmlspecialchars($pet['gender']); ?></p>
        <p><em><?php echo htmlspecialchars($pet['breed']); ?></em></p>
        <p>Shelter: <?php echo htmlspecialchars($pet['shelter_name'] ?? 'Unknown'); ?></p>
        <a href="view_pet.php?id=<?php echo $pet['id']; ?>">View Details</a>
    </div>
<?php endforeach; ?>
</div>
        </div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
