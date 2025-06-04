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
            background-color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-wrapper {
            width: 50%;
            max-width: 1400px;
            margin: 40px auto;
            padding: 40px 50px;
            background-color: #f9f9f9;
            border-radius: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        h2 {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 30px;
        }

        form label {
            display: block;
            font-weight: 600;
            margin: 20px 0 8px;
        }

        form select,
        form button {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        form button {
            background-color: #000;
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
        }

        .pet-card {
            background-color: #fff;
            border-radius: 20px;
            padding: 20px;
            margin-top: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        }

        .pet-card img {
            width: 100%;
            max-width: 180px;
            height: auto;
            border-radius: 10px;
            margin-top: 10px;
        }

        .pet-card a {
            display: inline-block;
            margin-top: 10px;
            background-color: #000;
            color: #fff;
            padding: 10px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
        }

        p {
            font-style: italic;
            color: #555;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="page-wrapper">
    <h2>🧩 Compatibility Quiz</h2>

    <form method="post">
        <label>Your Lifestyle:</label>
        <select name="lifestyle" required>
            <option value="active">Active & Outdoorsy</option>
            <option value="quiet">Calm & Indoors</option>
        </select>

        <label>Your Home Type:</label>
        <select name="home_type" required>
            <option value="bungalow">Bungalow</option>
            <option value="terrace">Terrace</option>
            <option value="highrise">High-Rise Apartment</option>
            <option value="flat">Flat</option>
        </select>

        <label>Pet Experience Level:</label>
        <select name="experience" required>
            <option value="first_time">First-time Pet Owner</option>
            <option value="experienced">Experienced</option>
        </select>

        <label>Do You Have Children?</label>
        <select name="has_kids" required>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>

        <label>Preferred Pet Gender:</label>
        <select name="preferred_gender" required>
            <option value="no_pref">No Preference</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label>Preferred Pet Age:</label>
        <select name="preferred_age" required>
            <option value="young">0–2 years</option>
            <option value="adult">3–7 years</option>
            <option value="senior">8+ years</option>
        </select>

        <label>Do You Prefer a Quiet Pet?</label>
        <select name="noise_level" required>
            <option value="quiet">Yes, I prefer quiet pets</option>
            <option value="any">No preference</option>
        </select>

        <label>Do You Want a Pet That Likes to Cuddle?</label>
        <select name="likes_cuddle" required>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>

        <button type="submit">Get Recommendations</button>
    </form>

    <?php if (!empty($suggestions)): ?>
        <h3>Recommended Pets:</h3>
        <?php foreach ($suggestions as $pet): ?>
            <div class="pet-card">
                <strong><?php echo htmlspecialchars($pet['name']); ?></strong>
                (<?php echo htmlspecialchars($pet['species']); ?>)<br>
                <?php if (!empty($pet['image'])): ?>
                    <img src="../images/pets/<?php echo $pet['image']; ?>" alt="Pet image"><br>
                <?php endif; ?>
                <a href="view_pet.php?id=<?php echo $pet['id']; ?>">View Details</a>
            </div>
        <?php endforeach; ?>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p>No pets matched your preferences at this time.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
