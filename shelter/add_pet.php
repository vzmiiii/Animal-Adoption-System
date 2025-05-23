<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];
    $shelter_id = $_SESSION['user_id'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array(strtolower($ext), $allowed)) {
            $filename = time() . '_' . basename($_FILES['image']['name']);
            $destination = "../images/pets/" . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image = $filename;
            }
        }
    }

    $sql = "INSERT INTO pets (name, species, breed, age, gender, description, image, shelter_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisssi", $name, $species, $breed, $age, $gender, $description, $image, $shelter_id);

    if ($stmt->execute()) {
        $msg = "✅ Pet added successfully!";
    } else {
        $msg = "❌ Error: " . $conn->error;
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
            background-color: #ffffff;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-wrapper {
            max-width: 700px;
            margin: 80px auto;
            background-color: #f7e6cf;
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
        }

        .form-wrapper h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .form-wrapper form label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
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
            border-radius: 12px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .form-wrapper textarea {
            resize: vertical;
        }

        .form-wrapper button {
            width: 100%;
            background-color: #000;
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            cursor: pointer;
        }

        .form-wrapper .msg {
            margin-bottom: 20px;
            font-weight: 500;
            text-align: center;
            padding: 12px;
            border-radius: 12px;
            background-color: #e7fbe7;
            color: #246b24;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="form-wrapper">
    <h2>➕ Add a New Pet for Adoption</h2>

    <?php if (!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Pet Name:</label>
        <input type="text" name="name" required>

        <label>Species:</label>
        <input type="text" name="species" required>

        <label>Breed:</label>
        <input type="text" name="breed" required>

        <label>Age (in years):</label>
        <input type="number" name="age" required>

        <label>Gender:</label>
        <select name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label>Description:</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Upload Image:</label>
        <input type="file" name="image">

        <button type="submit">Add Pet</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
