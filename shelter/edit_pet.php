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
$msg = "";
$msg_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $species = trim($_POST['species']);
    $breed = trim($_POST['breed']);
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);
    $update_image = $pet['image'];

    if ($age < 0) {
        $msg = "Age must be a non-negative number.";
        $msg_class = "error";
    }

    if (empty($msg) && isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
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

    if (empty($msg)) {
        $update_sql = "UPDATE pets SET name=?, species=?, breed=?, age=?, gender=?, description=?, image=? WHERE id=? AND shelter_id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssisssii", $name, $species, $breed, $age, $gender, $description, $update_image, $pet_id, $shelter_id);

        if ($update_stmt->execute()) {
            header("Location: manage_pet_profiles.php?status=updated");
            exit();
        } else {
            $msg = "Error updating record: " . htmlspecialchars($conn->error);
            $msg_class = "error";
        }
    }
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
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            font-weight: 600;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-sizing: border-box;
        }
        .button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_shelter.php'); ?>

<div class="form-container">
    <h2>Edit Pet - <?= htmlspecialchars($pet['name']) ?></h2>

    <?php if (!empty($msg)): ?>
        <div class="message <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Pet Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($pet['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="species">Species:</label>
            <select name="species" id="species" required>
                <option value="">-- Select Species --</option>
                <?php
                $speciesOptions = ['Dog', 'Cat', 'Reptile', 'Small Mammal', 'Bird', 'Exotic Pet'];
                foreach ($speciesOptions as $sp) {
                    echo "<option value=\"$sp\"" . ($pet['species'] === $sp ? " selected" : "") . ">$sp</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="breed">Breed:</label>
            <select name="breed" id="breed" required>
                <option value="">-- Select Breed --</option>
            </select>
        </div>

        <div class="form-group">
            <label for="age">Age (years):</label>
            <input type="number" name="age" id="age" value="<?= htmlspecialchars($pet['age']) ?>" min="0" required>
        </div>

        <div class="form-group">
            <label for="gender">Gender:</label>
            <select name="gender" id="gender" required>
                <option value="Male" <?= $pet['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $pet['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($pet['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Change Image (optional):</label>
            <input type="file" name="image" id="image">
        </div>

        <button type="submit" class="button">Update Pet</button>
    </form>
</div>

<script>
// Breed options by species
const breedOptions = {
    'Dog': [
        'Golden Retriever', 'Labrador Retriever', 'German Shepherd', 'Bulldog', 'Beagle',
        'Poodle', 'Rottweiler', 'Yorkshire Terrier', 'Boxer', 'Dachshund',
        'Siberian Husky', 'Great Dane', 'Doberman', 'Shih Tzu', 'Chihuahua',
        'Border Collie', 'Australian Shepherd', 'Bernese Mountain Dog', 'Cavalier King Charles Spaniel', 'Pomeranian'
    ],
    'Cat': [
        'Persian', 'Maine Coon', 'Siamese', 'British Shorthair', 'Ragdoll',
        'Bengal', 'Abyssinian', 'Russian Blue', 'Sphynx', 'Norwegian Forest Cat',
        'American Shorthair', 'Exotic Shorthair', 'Birman', 'Oriental Shorthair', 'Turkish Van',
        'Scottish Fold', 'Devon Rex', 'Cornish Rex', 'Himalayan', 'Burmese'
    ],
    'Reptile': [
        'Bearded Dragon', 'Leopard Gecko', 'Ball Python', 'Corn Snake', 'Green Iguana',
        'Crested Gecko', 'Blue Tongue Skink', 'Red-Eared Slider Turtle', 'Painted Turtle', 'Chameleon',
        'Anole', 'Uromastyx', 'Tegu', 'Monitor Lizard', 'Tortoise'
    ],
    'Small Mammal': [
        'Hamster', 'Guinea Pig', 'Rabbit', 'Ferret', 'Chinchilla',
        'Gerbil', 'Mouse', 'Rat', 'Hedgehog', 'Sugar Glider',
        'Degu', 'Dwarf Hamster', 'Syrian Hamster', 'Netherland Dwarf Rabbit', 'Holland Lop'
    ],
    'Bird': [
        'Budgerigar (Budgie)', 'Cockatiel', 'African Grey Parrot', 'Macaw', 'Cockatoo',
        'Canary', 'Finch', 'Lovebird', 'Conure', 'Amazon Parrot',
        'Quaker Parrot', 'Eclectus Parrot', 'Senegal Parrot', 'Pionus Parrot', 'Lorikeet'
    ],
    'Exotic Pet': [
        'Sugar Glider', 'Fennec Fox', 'Capybara', 'Kinkajou', 'Serval',
        'Wallaby', 'Skunk', 'Raccoon', 'Squirrel Monkey', 'Pygmy Goat',
        'Miniature Horse', 'Alpaca', 'Llama', 'Pot-Bellied Pig', 'Axolotl'
    ]
};

function updateBreeds() {
    const species = document.getElementById('species').value;
    const breedSelect = document.getElementById('breed');
    const currentBreed = "<?= htmlspecialchars($pet['breed']) ?>";

    breedSelect.innerHTML = '<option value="">-- Select Breed --</option>';

    if (breedOptions[species]) {
        breedOptions[species].forEach(breed => {
            const opt = document.createElement('option');
            opt.value = breed;
            opt.textContent = breed;
            if (breed === currentBreed) opt.selected = true;
            breedSelect.appendChild(opt);
        });
    }
}

document.getElementById('species').addEventListener('change', updateBreeds);
window.onload = updateBreeds;
</script>
</body>
</html>

