<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

if (!isset($_GET['id'])) {
    echo "No pet ID provided.";
    exit();
}

$pet_id = intval($_GET['id']);
$shelter_id = $_SESSION['user_id'];

$sql = "SELECT * FROM pets WHERE id = ? AND shelter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $pet_id, $shelter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Pet not found or access denied.";
    exit();
}

$pet = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];

    $update_image = $pet['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array(strtolower($ext), $allowed)) {
            $filename = time() . '_' . basename($_FILES['image']['name']);
            $destination = "../images/pets/" . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                if (!empty($pet['image']) && file_exists("../images/pets/" . $pet['image'])) {
                    unlink("../images/pets/" . $pet['image']);
                }
                $update_image = $filename;
            }
        }
    }

    $update_sql = "UPDATE pets SET name=?, species=?, breed=?, age=?, gender=?, description=?, image=? WHERE id=? AND shelter_id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssisssii", $name, $species, $breed, $age, $gender, $description, $update_image, $pet_id, $shelter_id);
    
    if ($update_stmt->execute()) {
        header("Location: manage_pet_profiles.php?status=updated");
    } else {
        echo "Error updating record: " . $conn->error;
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pet</title>
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
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }
        .form-header h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #444;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            font-size: 15px;
            box-sizing: border-box;
            background-color: #fff;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4e8cff;
            box-shadow: 0 0 0 3px rgba(78, 140, 255, 0.3);
        }
        .button {
            display: inline-block;
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            color: #fff;
            background-image: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            border: none;
            transition: all 0.3s ease;
            text-align: center;
            cursor: pointer;
        }
        .button:hover {
            box-shadow: 0 4px 15px rgba(78, 140, 255, 0.4);
            transform: translateY(-2px);
        }
        .button-secondary {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            color: #444;
            background-color: #fff;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
            text-align: center;
        }
        .button-secondary:hover {
            background-image: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(78, 140, 255, 0.3);
            transform: translateY(-2px);
        }
        .back-link-container {
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="content-container">
    <div class="form-container">
        <div class="form-header">
        <h2>Edit Pet - <?php echo htmlspecialchars($pet['name']); ?></h2>
        </div>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($pet['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="species">Species:</label>
                <input type="text" id="species" name="species" value="<?php echo htmlspecialchars($pet['species']); ?>">
            </div>

            <div class="form-group">
                <label for="breed">Breed:</label>
                <input type="text" id="breed" name="breed" value="<?php echo htmlspecialchars($pet['breed']); ?>">
            </div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($pet['age']); ?>">
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender">
                <option value="Male" <?php if ($pet['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($pet['gender'] === 'Female') echo 'selected'; ?>>Female</option>
            </select>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($pet['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">Change Image (optional):</label>
                <input type="file" id="image" name="image">
            </div>

            <button type="submit" class="button">Update Pet</button>
        </form>

        <div class="back-link-container">
            <a href="manage_pet_profiles.php" class="button-secondary">← Back to Manage Pet Profiles</a>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
