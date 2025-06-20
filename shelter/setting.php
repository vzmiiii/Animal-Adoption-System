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
$msg_class = "";

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
        $msg = "Profile updated successfully.";
        $msg_class = "success";
    } else {
        $msg = "Update failed: " . htmlspecialchars($stmt->error);
        $msg_class = "error";
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
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        .form-group {
            margin-bottom: 0;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #444;
        }
        .form-group input {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            font-size: 15px;
            box-sizing: border-box;
            background-color: #fff;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #4e8cff;
            box-shadow: 0 0 0 3px rgba(78, 140, 255, 0.3);
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
            grid-column: 1 / -1;
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
            <h2>Shelter Profile Settings</h2>
        </div>
        <form method="post" class="form-grid">
            <?php if (!empty($msg)): ?>
                <div class="message <?php echo $msg_class; ?>"><?= htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="username">Username:</label>
                <input id="username" type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input id="email" type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input id="first_name" type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input id="last_name" type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input id="phone_number" type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">New Password:</label>
                <input id="password" type="password" name="password" placeholder="Leave blank to keep current">
            </div>

            <div class="form-group full-width">
                <button type="submit" class="button">Update Profile</button>
            </div>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
