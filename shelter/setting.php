<?php
session_start();

// Restrict access to shelter personnel only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$user_id = $_SESSION['user_id'];
$msg = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim inputs to remove extra whitespace
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone_number']);

    // Initialize variables for query
    if (!empty($_POST['password'])) {
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username=?, first_name=?, last_name=?, email=?, phone_number=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $username, $first_name, $last_name, $email, $phone, $password, $user_id);
    } else {
        $sql = "UPDATE users SET username=?, first_name=?, last_name=?, email=?, phone_number=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $username, $first_name, $last_name, $email, $phone, $user_id);
    }

    // Execute query and set feedback message
    if ($stmt->execute()) {
        $msg = "✅ Profile updated successfully.";
    } else {
        $msg = "⚠️ Update failed: " . htmlspecialchars($stmt->error);
    }
}

// Retrieve current user information
$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shelter Profile Settings</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #000;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            opacity: 0.9;
        }
        .msg {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_shelter.php'); ?>

<div class="form-container">
    <h2>Shelter Profile Settings</h2>
    <?php if (!empty($msg)): ?>
        <p class="msg"><?= $msg; ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="username">Username:</label>
        <input id="username" type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>

        <label for="first_name">First Name:</label>
        <input id="first_name" type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input id="last_name" type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']); ?>" required>

        <label for="email">Email:</label>
        <input id="email" type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

        <label for="password">Password (leave blank to keep current):</label>
        <input id="password" type="password" name="password">

        <label for="phone_number">Phone Number:</label>
        <input id="phone_number" type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']); ?>" required>

        <button type="submit">Update Profile</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
