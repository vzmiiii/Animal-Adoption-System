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
    $update_stmt->execute();

    echo "Pet updated successfully. <a href='manage_pet_profiles.php'>Back to List</a>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pet</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
            padding: 40px 20px;
        }

        .page-wrapper {
            max-width: 700px;
            margin: auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            border: 1px solid #e0e0e0;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 26px;
            font-weight: 600;
        }

        form label {
            display: block;
            margin-top: 16px;
            font-weight: 600;
        }

        form input[type="text"],
        form input[type="number"],
        form textarea,
        form select,
        form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
        }

        form input:focus,
        form select:focus,
        form textarea:focus {
            border-color: #888;
            outline: none;
        }

        form input:focus-visible,
        form textarea:focus-visible,
        form select:focus-visible {
            outline: none;
        }

        form textarea {
            resize: vertical;
        }

        button[type="submit"] {
            width: 100%;
            background-color: #000;
            color: #fff;
            padding: 12px;
            margin-top: 30px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #333;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            text-decoration: none;
            color: #000;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="content">
    <div class="page-wrapper">
        <h2>Edit Pet - <?php echo htmlspecialchars($pet['name']); ?></h2>
        <form method="post" enctype="multipart/form-data">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($pet['name']); ?>" required>

            <label>Species:</label>
            <input type="text" name="species" value="<?php echo htmlspecialchars($pet['species']); ?>">

            <label>Breed:</label>
            <input type="text" name="breed" value="<?php echo htmlspecialchars($pet['breed']); ?>">

            <label>Age:</label>
            <input type="number" name="age" value="<?php echo htmlspecialchars($pet['age']); ?>">

            <label>Gender:</label>
            <select name="gender">
                <option value="Male" <?php if ($pet['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($pet['gender'] === 'Female') echo 'selected'; ?>>Female</option>
            </select>

            <label>Description:</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($pet['description']); ?></textarea>

            <label>Change Image (optional):</label>
            <input type="file" name="image">

            <button type="submit">Update Pet</button>
        </form>

        <div class="back-link">
            <a href="manage_pet_profiles.php">← Back to Manage Pet Profiles</a>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
