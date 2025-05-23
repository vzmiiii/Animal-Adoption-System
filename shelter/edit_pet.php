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
<html>
<head>
    <title>Edit Pet</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

    <h2>Edit Pet - <?php echo htmlspecialchars($pet['name']); ?></h2>
    <form method="post" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($pet['name']); ?>" required><br><br>

        <label>Species:</label><br>
        <input type="text" name="species" value="<?php echo htmlspecialchars($pet['species']); ?>"><br><br>

        <label>Breed:</label><br>
        <input type="text" name="breed" value="<?php echo htmlspecialchars($pet['breed']); ?>"><br><br>

        <label>Age:</label><br>
        <input type="number" name="age" value="<?php echo htmlspecialchars($pet['age']); ?>"><br><br>

        <label>Gender:</label><br>
        <select name="gender">
            <option value="Male" <?php if ($pet['gender'] === 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($pet['gender'] === 'Female') echo 'selected'; ?>>Female</option>
        </select><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="4" cols="40"><?php echo htmlspecialchars($pet['description']); ?></textarea><br><br>

        <label>Change Image (optional):</label><br>
        <input type="file" name="image"><br><br>

        <button type="submit">Update Pet</button>
    </form>
</body>
</html>
