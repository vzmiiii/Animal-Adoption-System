<?php
session_start();
// Restrict access to shelter role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $species = trim($_POST['species']);
    $breed = trim($_POST['breed']);
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);
    $shelter_id = $_SESSION['user_id'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($ext, $allowed)) {
            $filename = time() . '_' . basename($_FILES['image']['name']);
            $destination = "../images/pets/" . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image = $filename;
            } else {
                $msg = "❌ Failed to upload image.";
            }
        } else {
            $msg = "❌ Only JPG, JPEG, or PNG files are allowed.";
        }
    }

    if (empty($msg)) {
        $sql = "INSERT INTO pets (name, species, breed, age, gender, description, image, shelter_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssi", $name, $species, $breed, $age, $gender, $description, $image, $shelter_id);
        if ($stmt->execute()) {
            $msg = "✅ Pet added successfully!";
        } else {
            $msg = "❌ Error: " . htmlspecialchars($conn->error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Pet</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .form-wrapper {
            max-width: 700px;
            margin: 80px auto;
            padding: 50px;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .form-wrapper h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .form-wrapper label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .form-wrapper input[type="text"],
        .form-wrapper input[type="number"],
        .form-wrapper select,
        .form-wrapper textarea,
        .form-wrapper input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 12px;
            font-size: 14px;
            background-color: #fff;
        }

        .form-wrapper textarea {
            resize: vertical;
        }

        .form-wrapper button {
            width: 100%;
            padding: 14px;
            background-color: #000;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-wrapper button:hover {
            background-color: #222;
        }

        .form-wrapper .msg {
            text-align: center;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 500;
            background-color: #e6f5e6;
            color: #2c6b2c;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="form-wrapper">
    <h2>➕ Add a New Pet for Adoption</h2>

    <?php if (!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="name">Pet Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="species">Species:</label>
        <input type="text" name="species" id="species" required>

        <label for="breed">Breed:</label>
        <input type="text" name="breed" id="breed" required>

        <label for="age">Age (in years):</label>
        <input type="number" name="age" id="age" required>

        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="">-- Select Gender --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4" required></textarea>

        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image">

        <button type="submit">Add Pet</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
