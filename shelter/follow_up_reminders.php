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
    $pet_id = $_POST['pet_id'];
    $adopter_id = $_POST['adopter_id'];
    $message = $_POST['message'];

    $insert = $conn->prepare("INSERT INTO follow_ups (pet_id, adopter_id, message) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $pet_id, $adopter_id, $message);
    $insert->execute();

    echo "Follow-up message sent.<br><a href='dashboard.php'>Back to Dashboard</a>";
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
        body {
            background-color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .page-wrapper {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fce7cd;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        form label {
            font-weight: bold;
            display: block;
            margin-top: 20px;
            margin-bottom: 8px;
        }

        select, textarea {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        button {
            background-color: black;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #333;
        }

        a {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .no-data {
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="page-wrapper">
    <h2>Send Follow-Up to Adopters</h2>
    <a href="dashboard.php">← Back to Dashboard</a>
    <hr>

    <?php if ($result->num_rows > 0): ?>
        <form method="post">
            <label>Select Adopter & Pet:</label>
            <select name="pet_id" required>
                <?php while($row = $result->fetch_assoc()): ?>
                    <option value="<?php echo $row['pet_id']; ?>" data-adopter="<?php echo $row['adopter_id']; ?>">
                        <?php echo htmlspecialchars($row['pet_name']) . " → " . htmlspecialchars($row['adopter_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Message:</label>
            <textarea name="message" rows="4" required></textarea>

            <input type="hidden" name="adopter_id" id="adopter_id" value="">
            <button type="submit">Send Reminder</button>
        </form>

        <script>
        document.querySelector('select[name="pet_id"]').addEventListener('change', function() {
            var adopterId = this.options[this.selectedIndex].getAttribute('data-adopter');
            document.getElementById('adopter_id').value = adopterId;
        });
        document.querySelector('select[name="pet_id"]').dispatchEvent(new Event('change'));
        </script>
    <?php else: ?>
        <p class="no-data">No adopted pets with approved applications found.</p>
    <?php endif; ?>
</div>

</body>
</html>
