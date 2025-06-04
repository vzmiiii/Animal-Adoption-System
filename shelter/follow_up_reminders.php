<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_id     = $_POST['pet_id'];
    $adopter_id = $_POST['adopter_id'];
    $message    = trim($_POST['message']);

    $insert = $conn->prepare("INSERT INTO follow_ups (pet_id, adopter_id, message) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $pet_id, $adopter_id, $message);
    $insert->execute();

    echo "<div class='confirmation'>Follow-up message sent.<br><a href='dashboard.php' class='back-link'>← Back to Dashboard</a></div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Follow-Up Reminders</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
    <style>
        /* Reset and base styles */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Centered wrapper */
        .page-wrapper {
            max-width: 700px;
            margin: auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            border: 1px solid #e0e0e0;
        }

        /* Heading */
        h2 {
            margin-top: 0;
            font-size: 1.75em;
            font-weight: 600;
            color: #222;
        }

        /* Back link styling */
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #555;
            text-decoration: none;
            font-size: 0.95em;
        }
        .back-link:hover {
            text-decoration: underline;
        }

        /* Horizontal rule */
        hr {
            border: none;
            height: 1px;
            background-color: #e0e0e0;
            margin: 20px 0;
        }

        /* Form labels */
        label {
            display: block;
            margin-top: 20px;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 1em;
            color: #444;
        }

        /* Select & textarea */
        select, textarea {
            width: 100%;
            font-size: 1em;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #fafafa;
            resize: vertical;
            transition: border-color 0.2s;
        }
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #888;
            background-color: #fff;
        }

        /* Submit button */
        button {
            margin-top: 20px;
            background-color: #111;
            color: #fff;
            padding: 12px 24px;
            font-size: 1em;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #333;
        }

        /* Empty state */
        .no-data {
            margin-top: 30px;
            font-style: italic;
            color: #777;
        }

        /* Confirmation message */
        .confirmation {
            max-width: 600px;
            margin: 80px auto;
            padding: 30px;
            background-color: #eafaf1;
            border: 1px solid #cdeac7;
            border-radius: 8px;
            text-align: center;
            font-size: 1em;
            color: #2a5d34;
        }
    </style>
</head>
<body>

    <?php include('../includes/navbar_shelter.php'); ?>

    <div class="page-wrapper">
        <h2>Send Follow-Up to Adopters</h2>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <hr>

        <?php if ($result->num_rows > 0): ?>
            <form method="post" novalidate>
                <!-- Pet & Adopter selection -->
                <label for="pet_id">Select Pet & Adopter</label>
                <select id="pet_id" name="pet_id" required>
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

                <!-- Hidden field for the adopter_id; populated via JS -->
                <input type="hidden" name="adopter_id" id="adopter_id" value="">

                <!-- Message textarea -->
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4" placeholder="Type your follow-up message here…" required></textarea>

                <!-- Submit button -->
                <button type="submit">Send Reminder</button>
            </form>

            <script>
                // Update the hidden adopter_id whenever the selected pet changes
                const petSelect   = document.getElementById('pet_id');
                const adopterField = document.getElementById('adopter_id');

                function updateAdopterField() {
                    const selectedOption = petSelect.options[petSelect.selectedIndex];
                    adopterField.value = selectedOption.getAttribute('data-adopter');
                }

                petSelect.addEventListener('change', updateAdopterField);
                // Initialize on page load
                document.addEventListener('DOMContentLoaded', updateAdopterField);
            </script>
        <?php else: ?>
            <p class="no-data">No adopted pets with approved applications found.</p>
        <?php endif; ?>
    </div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
