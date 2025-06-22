<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];
$msg = "";
$msg_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_id     = $_POST['pet_id'];
    $adopter_id = $_POST['adopter_id'];
    $message    = trim($_POST['message']);

    if (empty($pet_id) || empty($adopter_id) || empty($message)) {
        $msg = "All fields are required.";
        $msg_class = "error";
    } else {
        $insert = $conn->prepare("INSERT INTO follow_ups (pet_id, adopter_id, message) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $pet_id, $adopter_id, $message);
        
        if ($insert->execute()) {
            $msg = "Follow-up message sent successfully!";
            $msg_class = "success";
        } else {
            $msg = "Failed to send message. Please try again.";
            $msg_class = "error";
        }
    }
}

$sql = "SELECT p.id AS pet_id, p.name AS pet_name, a.adopter_id, u.username AS adopter_name
        FROM pets p
        JOIN adoption_applications a ON p.id = a.pet_id
        JOIN users u ON a.adopter_id = u.id
        WHERE p.shelter_id = ? AND p.status = 'adopted'
        AND a.status = 'approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Follow-Up Reminders</title>
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
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }
        .form-header p {
            font-size: 16px;
            color: #555;
            margin-top: 0.5rem;
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
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4e8cff;
            box-shadow: 0 0 0 3px rgba(78, 140, 255, 0.3);
        }
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        .button {
            display: inline-block;
            width: 100%;
            padding: 15px;
            margin-top: 1rem;
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
        .no-data {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-top: 4rem;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
        }
    </style>
</head>
<body>

    <?php include('../includes/navbar_shelter.php'); ?>

    <div class="content-container">
        <div class="form-container">
            <div class="form-header">
                <h2>Send Follow-Up to Adopters</h2>
                <p>Select an adopted pet to send a message to their new owner.</p>
            </div>

            <?php if (!empty($msg)): ?>
                <div class="message <?php echo $msg_class; ?>"><?= htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <?php if ($result->num_rows > 0): ?>
                <form method="post" novalidate>
                    <div class="form-group">
                        <label for="pet_id">Select Pet & Adopter</label>
                        <select id="pet_id" name="pet_id" required>
                            <option value="" disabled selected>-- Choose a Pet --</option>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <option 
                                    value="<?php echo $row['pet_id']; ?>" 
                                    data-adopter="<?php echo $row['adopter_id']; ?>">
                                    <?php 
                                        echo htmlspecialchars($row['pet_name']) 
                                             . " → " 
                                             . htmlspecialchars($row['adopter_name']); 
                                    ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <input type="hidden" name="adopter_id" id="adopter_id" value="">

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Type your follow-up message here…" required></textarea>
                    </div>

                    <button type="submit" class="button">Send Reminder</button>
                </form>

                <script>
                    const petSelect = document.getElementById('pet_id');
                    const adopterField = document.getElementById('adopter_id');

                    function updateAdopterField() {
                        const selectedOption = petSelect.options[petSelect.selectedIndex];
                        if (selectedOption) {
                             adopterField.value = selectedOption.getAttribute('data-adopter') || '';
                        }
                    }

                    petSelect.addEventListener('change', updateAdopterField);
                    document.addEventListener('DOMContentLoaded', updateAdopterField);
                </script>
            <?php else: ?>
                <p class="no-data">No adopted pets with approved applications found.</p>
            <?php endif; ?>
        </div>
    </div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
