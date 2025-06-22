<?php
session_start();
// Restrict access to shelter role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');
$msg = "";
$msg_class = "";

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
                $msg = "Failed to upload image.";
                $msg_class = "error";
            }
        } else {
            $msg = "Only JPG, JPEG, or PNG files are allowed.";
            $msg_class = "error";
        }
    }

    if (empty($msg)) {
        $sql = "INSERT INTO pets (name, species, breed, age, gender, description, image, shelter_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssi", $name, $species, $breed, $age, $gender, $description, $image, $shelter_id);
        if ($stmt->execute()) {
            $msg = "Pet added successfully!";
            $msg_class = "success";
        } else {
            $msg = "Error: " . htmlspecialchars($conn->error);
            $msg_class = "error";
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
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #444;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            font-size: 15px;
            box-sizing: border-box;
            background-color: #fff;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
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
        .message {
            text-align: center;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            font-weight: 600;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="content-container">
    <div class="form-container">
        <div class="form-header">
            <h2>➕ Add a New Pet for Adoption</h2>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="message <?php echo $msg_class; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Pet Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="species">Species:</label>
                <select name="species" id="species" required>
                    <option value="" disabled selected>-- Select Species --</option>
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                    <option value="Reptile">Reptile</option>
                    <option value="Small Mammal">Small Mammal</option>
                    <option value="Bird">Bird</option>
                    <option value="Exotic Pet">Exotic Pet</option>
                </select>
            </div>
            <div class="form-group">
                <label for="breed">Breed:</label>
                <select name="breed" id="breed" required>
                    <option value="" disabled selected>-- Select Species First --</option>
                </select>
            </div>
            <div class="form-group">
                <label for="age">Age (in years):</label>
                <input type="number" name="age" id="age" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="" disabled selected>-- Select Gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" name="image" id="image">
            </div>
            <button type="submit" class="button">Add Pet</button>
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

// Function to update breed options based on selected species
function updateBreedOptions() {
    const speciesSelect = document.getElementById('species');
    const breedSelect = document.getElementById('breed');
    const selectedSpecies = speciesSelect.value;
    
    // Clear current options
    breedSelect.innerHTML = '<option value="" disabled selected>-- Select Breed --</option>';
    
    // Add new options based on selected species
    if (selectedSpecies && breedOptions[selectedSpecies]) {
        breedOptions[selectedSpecies].forEach(breed => {
            const option = document.createElement('option');
            option.value = breed;
            option.textContent = breed;
            breedSelect.appendChild(option);
        });
    }
}

// Add event listener to species select
document.addEventListener('DOMContentLoaded', function() {
    const speciesSelect = document.getElementById('species');
    speciesSelect.addEventListener('change', updateBreedOptions);
});
</script>

</body>
</html>
