<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid user ID.";
    exit();
}

$user_id = intval($_GET['id']);

// Update on form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=?, first_name=?, last_name=?, status=? WHERE id=?");
    $stmt->bind_param("ssssssi", $username, $email, $role, $first_name, $last_name, $status, $user_id);
    $stmt->execute();

    header("Location: manage_users.php?updated=1");
    exit();
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
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
        form label {
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
            letter-spacing: 0.01em;
        }
        form input, form select {
            width: 100%;
            box-sizing: border-box;
            padding: 14px 16px;
            border: 1.5px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            font-size: 16px;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
            transition: border 0.2s, box-shadow 0.2s, background 0.2s;
            margin-bottom: 0px;
        }
        form input:not([type='submit']), form select {
            margin-bottom: 8px;
        }
        form input:focus, form select:focus {
            border-color: #6ed6a5;
            outline: none;
            background: #f7f7f7;
            box-shadow: 0 0 10px rgba(110, 214, 165, 0.13);
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
        <h1>Edit User</h1>
        <form method="POST">
            <label for="username">Username</label>
            <input type="text" name="username" required value="<?= htmlspecialchars($user['username']) ?>">

            <label for="email">Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">

            <label for="role">Role</label>
            <select name="role" required>
                <option value="adopter" <?= $user['role'] === 'adopter' ? 'selected' : '' ?>>Adopter</option>
                <option value="shelter" <?= $user['role'] === 'shelter' ? 'selected' : '' ?>>Shelter Personnel</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <label for="first_name">First Name</label>
            <input type="text" name="first_name" required value="<?= htmlspecialchars($user['first_name']) ?>">

            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" required value="<?= htmlspecialchars($user['last_name']) ?>">

            <label for="status">Status</label>
            <select name="status" required>
                <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit">Update User</button>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
