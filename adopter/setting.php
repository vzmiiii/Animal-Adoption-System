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
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        :root {
            --accent-gradient: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            --text-color: #333;
            --text-color-light: #555;
            --container-bg: rgba(255, 255, 255, 0.92);
            --border-color: #e0e0e0;
            --shadow: 0 8px 25px rgba(0,0,0,0.1);
            --border-radius: 16px;
        }
        .settings-wrapper {
            max-width: 700px;
            margin: 80px auto 40px;
            padding: 40px;
            background: var(--container-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }

        .settings-wrapper h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }
        
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .input-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-color-light);
        }

        input {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-size: 15px;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #6ed6a5;
            box-shadow: 0 0 0 3px rgba(110, 214, 165, 0.18);
        }

        input[readonly] {
            background-color: #f1f1f1;
            cursor: not-allowed;
            color: #777;
        }

        button {
            width: 100%;
            padding: 15px;
            background: var(--accent-gradient);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
            grid-column: 1 / -1;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .success-msg {
            background-color: #d1f7de;
            color: #1e6b3b;
            padding: 15px;
            border-radius: 10px;
            font-weight: 500;
            text-align: center;
            border: 1px solid #b2e8c2;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="settings-wrapper">
    <h2>⚙️ My Settings</h2>

    <?php if (!empty($msg)): ?>
        <div class="success-msg"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="input-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($user['first_name']); ?>">
        </div>

        <div class="input-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($user['last_name']); ?>">
        </div>

        <div class="input-group full-width">
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" required value="<?php echo htmlspecialchars($user['phone_number']); ?>">
        </div>
        
        <div class="input-group full-width">
            <label for="username">Username (cannot be changed)</label>
            <input type="text" id="username" name="username" readonly value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>
        
        <div class="input-group full-width">
            <label for="email">Email (cannot be changed)</label>
            <input type="text" id="email" name="email" readonly value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>

        <button type="submit">Update Profile</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
