<?php
session_start();

// Ensure the user is logged in and is an adopter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$user_id = $_SESSION['user_id'];
$msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone_number']);

    // Update only editable fields (not username/email here)
    $sql = "UPDATE users SET first_name = ?, last_name = ?, phone_number = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $first_name, $last_name, $phone, $user_id);
    $stmt->execute();

    $msg = "✅ Profile updated successfully.";
}

// Fetch latest user info
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adopter Settings</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        body {
            background-color: #f5f5f5;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .settings-wrapper {
            width: 60%;
            max-width: 700px;
            margin: 80px auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .settings-wrapper h2 {
            font-size: 26px;
            margin-bottom: 25px;
            text-align: center;
        }

        .settings-wrapper label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .settings-wrapper input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .settings-wrapper button {
            width: 100%;
            padding: 14px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            cursor: pointer;
        }

        .settings-wrapper .success-msg {
            background-color: #e7fbe7;
            color: #246b24;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            text-align: center;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="settings-wrapper">
    <h2>⚙️ My Settings</h2>

    <!-- Show message if profile is updated -->
    <?php if (!empty($msg)): ?>
        <div class="success-msg"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <!-- Settings form -->
    <form method="post">
        <!-- Username (readonly for now) -->
        <label>Username:</label>
        <input type="text" name="username" readonly value="<?php echo htmlspecialchars($user['username']); ?>">

        <!-- First Name -->
        <label>First Name:</label>
        <input type="text" name="first_name" required value="<?php echo htmlspecialchars($user['first_name']); ?>">

        <!-- Last Name -->
        <label>Last Name:</label>
        <input type="text" name="last_name" required value="<?php echo htmlspecialchars($user['last_name']); ?>">

        <!-- Phone Number -->
        <label>Phone Number:</label>
        <input type="text" name="phone_number" required value="<?php echo htmlspecialchars($user['phone_number']); ?>">

        <!-- Email (readonly for now) -->
        <label>Email:</label>
        <input type="text" name="email" readonly value="<?php echo htmlspecialchars($user['email']); ?>">

        <button type="submit">Update</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
