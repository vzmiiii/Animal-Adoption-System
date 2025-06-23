<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid pet ID.";
    exit();
}
$pet_id = intval($_GET['id']);

// Fetch pet and shelter info
$sql = "SELECT pets.*, users.username AS shelter_name FROM pets JOIN users ON pets.shelter_id = users.id WHERE pets.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    echo "Pet not found.";
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
    $status = $_POST['status'];
    $description = trim($_POST['description']);
    $update_sql = "UPDATE pets SET name=?, species=?, breed=?, age=?, gender=?, status=?, description=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssisssi", $name, $species, $breed, $age, $gender, $status, $description, $pet_id);
    if ($update_stmt->execute()) {
        header("Location: manage_pets.php?updated=1");
        exit();
    } else {
        $msg = "Error updating record: " . htmlspecialchars($conn->error);
        $msg_class = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pet</title>
    <link rel="stylesheet" href="../css/common.css">
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
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .content-container {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 40px 60px 32px 60px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            margin-top: 32px;
            min-width: 600px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        h1 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 36px;
            color: #2c3e50;
            font-size: 2.2em;
            letter-spacing: 1px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 8px;
        }
        label {
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
            letter-spacing: 0.01em;
        }
        input, select, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 14px 16px;
            border: 1.5px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            font-size: 16px;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
            transition: border 0.2s, box-shadow 0.2s, background 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #6ed6a5;
            outline: none;
            background: #f7f7f7;
            box-shadow: 0 0 10px rgba(110, 214, 165, 0.13);
        }
        .readonly {
            background: #f4f6f8;
            color: #888;
            cursor: not-allowed;
        }
        button {
            width: 100%;
            margin-top: 18px;
            padding: 15px;
            background: #fff;
            color: #2c3e50;
            font-size: 17px;
            border: 1.5px solid #dde1e7;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(.4,2,.6,1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        button:hover {
            background: linear-gradient(90deg, #6ed6a5, #4e8cff);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            text-align: center;
        }
        @media (max-width: 800px) {
            .content-container {
                padding: 24px 8px 18px 8px;
                min-width: unset;
                max-width: 98vw;
            }
        }
        @media (max-width: 600px) {
            h1 {
                font-size: 1.3em;
            }
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>
<div class="page-wrapper">
    <div class="content-container">
        <h1>Edit Pet</h1>
        <?php if (!empty($msg)): ?>
            <div class="message error"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="name">Pet Name</label>
                <input type="text" name="name" id="name" required value="<?= htmlspecialchars($pet['name']) ?>">
            </div>
            <div class="form-group">
                <label for="species">Species</label>
                <select name="species" id="species" required>
                    <option value="" disabled>-- Select Species --</option>
                    <?php
                    $speciesOptions = ['Dog', 'Cat', 'Reptile', 'Small Mammal', 'Bird', 'Exotic Pet'];
                    foreach ($speciesOptions as $sp) {
                        echo "<option value=\"$sp\"" . ($pet['species'] === $sp ? " selected" : "") . ">$sp</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="breed">Breed</label>
                <select name="breed" id="breed" required>
                    <option value="" disabled>-- Select Breed --</option>
                </select>
            </div>
            <div class="form-group">
                <label for="age">Age (years)</label>
                <input type="number" name="age" id="age" min="0" required value="<?= htmlspecialchars($pet['age']) ?>">
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" id="gender" required>
                    <option value="Male" <?= $pet['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $pet['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" required>
                    <option value="available" <?= $pet['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="adopted" <?= $pet['status'] === 'adopted' ? 'selected' : '' ?>>Adopted</option>
                    <option value="inactive" <?= $pet['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="shelter">Shelter</label>
                <input type="text" id="shelter" value="<?= htmlspecialchars($pet['shelter_name']) ?>" class="readonly" readonly>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($pet['description']) ?></textarea>
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
<script>
// Breed options for each species
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
    breedSelect.innerHTML = '<option value="" disabled>-- Select Breed --</option>';
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