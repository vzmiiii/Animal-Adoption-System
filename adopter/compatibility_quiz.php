<?php
session_start();
// Ensure user is adopter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$suggestions = [];
$form_submitted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_submitted = true;
    $lifestyle = $_POST['lifestyle'];
    $home_type = $_POST['home_type'];
    $experience = $_POST['experience'];
    $has_kids = $_POST['has_kids'];
    $preferred_gender = $_POST['preferred_gender'];
    $preferred_age = $_POST['preferred_age'];
    $noise_level = $_POST['noise_level'];
    $likes_cuddle = $_POST['likes_cuddle'];

    $sql = "SELECT p.*, u.username AS shelter_name
            FROM pets p
            JOIN users u ON p.shelter_id = u.id
            WHERE p.status = 'available'";

    if ($lifestyle === "active") {
        $sql .= " AND p.species = 'Dog'";
    } elseif ($lifestyle === "quiet") {
        $sql .= " AND p.species = 'Cat'";
    }

    if (in_array($home_type, ["highrise", "flat"])) {
        $sql .= " AND (p.species != 'Dog' OR p.age <= 5)";
    }

    if ($experience === "first_time") {
        $sql .= " AND p.age <= 3";
    }

    if ($has_kids === "yes") {
        $sql .= " AND p.age <= 5";
    }

    if ($preferred_gender !== "no_pref") {
        $sql .= " AND p.gender = '" . $conn->real_escape_string($preferred_gender) . "'";
    }

    if ($preferred_age === "young") {
        $sql .= " AND p.age <= 2";
    } elseif ($preferred_age === "adult") {
        $sql .= " AND p.age > 2 AND p.age <= 7";
    } elseif ($preferred_age === "senior") {
        $sql .= " AND p.age > 7";
    }

    if ($noise_level === "quiet") {
        $sql .= " AND p.species = 'Cat'";
    }

    if ($likes_cuddle === "yes") {
        $sql .= " AND p.age <= 4";
    }

    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row;
        }
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
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        :root {
            --accent-gradient: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            --text-color: #333;
            --text-color-light: #555;
            --container-bg: rgba(255, 255, 255, 0.92);
            --border-color: #e0e0e0;
            --shadow: 0 8px 25px rgba(0,0,0,0.1);
            --border-radius: 16px;
        }
        .page-wrapper {
            max-width: 800px;
            margin: 80px auto 40px;
            padding: 40px;
            background: var(--container-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }

        h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }

        .section-title {
            grid-column: 1 / -1;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-color-light);
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-color-light);
        }

        select {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-size: 15px;
            background-color: #fff;
        }

        button {
            grid-column: 1 / -1;
            width: 100%;
            padding: 15px;
            background: var(--accent-gradient);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .results-wrapper {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }

        .recommended-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 30px;
        }

        .pet-card {
            background: #fff;
            border-radius: var(--border-radius);
            padding: 0;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border-color);
        }

        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .pet-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .pet-card-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .pet-card h3 {
            margin: 0 0 8px;
            font-size: 22px;
        }

        .pet-card p {
            margin: 2px 0;
            font-size: 14px;
            color: var(--text-color-light);
            line-height: 1.5;
        }

        .pet-card .description {
            flex-grow: 1;
            margin-top: 10px;
        }

        .pet-card a.adopt-btn {
            margin-top: 15px;
            display: block;
            background: var(--accent-gradient);
            color: #fff;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
        }

        .empty-msg {
            text-align: center;
            color: var(--text-color-light);
            font-size: 16px;
            padding: 30px;
            background: rgba(255,255,255,0.5);
            border-radius: var(--border-radius);
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_adopter.php'); ?>

<div class="page-wrapper">
    <h2>🧩 Compatibility Quiz</h2>
    <p style="text-align:center; margin-top:-20px; margin-bottom:30px; color: var(--text-color-light);">Answer a few questions to find the perfect companion for you!</p>

    <form method="post">
        <h3 class="section-title">About You & Your Home</h3>
        <div class="form-group">
            <label for="lifestyle">What's your lifestyle like?</label>
            <select id="lifestyle" name="lifestyle" required>
                <option value="active">Active & Outdoorsy</option>
                <option value="quiet">Calm & Indoors</option>
            </select>
        </div>
        <div class="form-group">
            <label for="home_type">What's your home type?</label>
            <select id="home_type" name="home_type" required>
                <option value="bungalow">Bungalow</option>
                <option value="terrace">Terrace</option>
                <option value="highrise">High-Rise Apartment</option>
                <option value="flat">Flat</option>
            </select>
        </div>
        <div class="form-group">
            <label for="experience">What's your experience with pets?</label>
            <select id="experience" name="experience" required>
                <option value="first_time">First-time Pet Owner</option>
                <option value="experienced">Experienced</option>
            </select>
        </div>
        <div class="form-group">
            <label for="has_kids">Do you have children at home?</label>
            <select id="has_kids" name="has_kids" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
        </div>

        <h3 class="section-title">Your Future Pet</h3>
        <div class="form-group">
            <label for="preferred_gender">Preferred pet gender?</label>
            <select id="preferred_gender" name="preferred_gender" required>
                <option value="no_pref">No Preference</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="preferred_age">Preferred pet age?</label>
            <select id="preferred_age" name="preferred_age" required>
                <option value="young">Young (0-2 years)</option>
                <option value="adult">Adult (3-7 years)</option>
                <option value="senior">Senior (8+ years)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="noise_level">Do you prefer a quiet pet?</label>
            <select id="noise_level" name="noise_level" required>
                <option value="quiet">Yes, peace and quiet is a must</option>
                <option value="any">A little noise is fine</option>
            </select>
        </div>
        <div class="form-group">
            <label for="likes_cuddle">Do you want a cuddly pet?</label>
            <select id="likes_cuddle" name="likes_cuddle" required>
                <option value="yes">Yes, give me all the cuddles!</option>
                <option value="no">No, I prefer an independent pet</option>
            </select>
        </div>

        <!-- JS Suggestion Preview -->
        <div id="jsSuggestionBox" style="grid-column: 1 / -1; padding: 15px; background: #f9f9f9; border-radius: 10px; display: none; border: 1px solid #ccc; margin-bottom: 10px;">
            <strong>Pet Suggestions:</strong>
            <p id="jsSuggestionText" style="margin: 0;"></p>
        </div>

        <button type="submit">Find My Match</button>
    </form>

    <?php if ($form_submitted): ?>
        <div class="results-wrapper">
            <h2>Your Recommended Pets</h2>
            <?php if (!empty($suggestions)): ?>
                <div class="recommended-grid">
                    <?php foreach ($suggestions as $pet): ?>
                        <div class="pet-card">
                            <?php if (!empty($pet['image'])): ?>
                                <img src="../images/pets/<?= htmlspecialchars($pet['image']) ?>" alt="Pet Image">
                            <?php else: ?>
                                <img src="../images/pets/default.jpg" alt="Default Image">
                            <?php endif; ?>
                            <div class="pet-card-content">
                                <h3><?= htmlspecialchars($pet['name']) ?></h3>
                                <p><?= htmlspecialchars($pet['breed']) ?></p>
                                <p class="description"><?= htmlspecialchars($pet['age']) ?> years old | <?= htmlspecialchars($pet['gender']) ?> | <?= htmlspecialchars($pet['species']) ?></p>
                                <p><strong>Shelter:</strong> <?= htmlspecialchars($pet['shelter_name']) ?></p>
                                <a href="view_pet.php?id=<?= $pet['id'] ?>" class="adopt-btn">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-msg">No pets matched your criteria. Try adjusting your preferences!</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>

<!-- JavaScript for live compatibility suggestion -->
<script>
function getSuggestion() {
    const lifestyle = document.getElementById("lifestyle").value;
    const homeType = document.getElementById("home_type").value;
    const experience = document.getElementById("experience").value;
    const hasKids = document.getElementById("has_kids").value;
    const noise = document.getElementById("noise_level").value;
    const cuddle = document.getElementById("likes_cuddle").value;

    let score = 0;
    if (lifestyle === "active") score += 3; else score += 1;
    if (homeType === "bungalow" || homeType === "terrace") score += 3; else score += 1;
    if (experience === "experienced") score += 2; else score += 1;
    if (hasKids === "yes") score += 2;
    if (noise === "quiet") score += 1; else score += 3;
    if (cuddle === "yes") score += 2;

    let suggestion = "Small pets like hamsters, rabbits, or cats";
    if (score >= 12) suggestion = "High-energy dogs (e.g., Retrievers, Huskies)";
    else if (score >= 9) suggestion = "Cats or calm dogs (e.g., Beagle, Shih Tzu)";

    document.getElementById("jsSuggestionText").innerText = suggestion;
    document.getElementById("jsSuggestionBox").style.display = "block";
}
document.querySelectorAll("select").forEach(select => {
    select.addEventListener("change", getSuggestion);
});
</script>

</body>
</html>

