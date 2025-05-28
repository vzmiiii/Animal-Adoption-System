<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$suggestions = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lifestyle = $_POST['lifestyle'];
    $home_type = $_POST['home_type'];
    $experience = $_POST['experience'];
    $has_kids = $_POST['has_kids'];
    $preferred_gender = $_POST['preferred_gender'];
    $preferred_age = $_POST['preferred_age'];
    $noise_level = $_POST['noise_level'];
    $likes_cuddle = $_POST['likes_cuddle'];

    $sql = "SELECT * FROM pets WHERE status = 'available'";

    if ($lifestyle === "active") {
        $sql .= " AND species = 'Dog'";
    } elseif ($lifestyle === "quiet") {
        $sql .= " AND species = 'Cat'";
    }

    if ($home_type === "apartment") {
        $sql .= " AND (species != 'Dog' OR age <= 5)";
    }

    if ($experience === "first_time") {
        $sql .= " AND age <= 3";
    }

    if ($has_kids === "yes") {
        $sql .= " AND age <= 5";
    }

    if ($preferred_gender !== "no_pref") {
        $sql .= " AND gender = '" . $conn->real_escape_string($preferred_gender) . "'";
    }

    if ($preferred_age === "young") {
        $sql .= " AND age <= 2";
    } elseif ($preferred_age === "adult") {
        $sql .= " AND age > 2 AND age <= 7";
    } elseif ($preferred_age === "senior") {
        $sql .= " AND age > 7";
    }

    if ($noise_level === "quiet") {
        $sql .= " AND species = 'Cat'";
    }

    if ($likes_cuddle === "yes") {
        $sql .= " AND age <= 4";
    }

    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Compatibility Quiz</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        body {
            background-color: #ffffff;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-wrapper {
            max-width: 800px;
            margin: 60px auto;
            padding: 40px;
            background-color: #f7e6cf;
            border-radius: 30px;
        }

        form label {
            font-weight: bold;
            display: block;
            margin: 15px 0 5px;
        }

        form select, form button {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .pet-card {
            background-color: #fff;
            border-radius: 20px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
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
            font-size: 13px;
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
            <option value="apartment">Apartment</option>
            <option value="house">House with Yard</option>
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
                <strong><?php echo htmlspecialchars($pet['name']); ?></strong> (<?php echo htmlspecialchars($pet['species']); ?>)<br>
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

