<?php
session_start();
include('db_connection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim and sanitize input
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare SQL to find user by email
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $hashed_password, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect based on user role
            if ($role === 'adopter') {
                header("Location: adopter/dashboard.php");
            } elseif ($role === 'shelter') {
                header("Location: shelter/dashboard.php");
            } elseif ($role === 'admin') {
                header("Location: admin/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Animal Adoption System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mobile responsive -->
    <link rel="stylesheet" href="css/common.css">
<style>
    body {
        margin: 0;
        background: url('images/bg_animals.png') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Segoe UI', sans-serif;
    }

    .login-container {
        background-color: #fef9ec;
        max-width: 420px;
        margin: 100px auto;
        padding: 40px;
        border-radius: 25px;
        text-align: center;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    .login-container h2 {
        margin-bottom: 25px;
        font-size: 24px;
        font-weight: 600;
    }

    .login-container input {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        border: 1px solid #ccc;
        margin-bottom: 18px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .login-container button {
        width: 100%;
        background-color: #000;
        color: #fff;
        padding: 14px;
        border: none;
        border-radius: 20px;
        font-weight: bold;
        text-transform: uppercase;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.3s ease;
    }

    .login-container button:hover {
        background-color: #333;
    }

    .login-container .bottom-link {
        margin-top: 12px;
        font-size: 14px;
    }

    .login-container .bottom-link a {
        color: #000;
        text-decoration: underline;
    }

    .error {
        background-color: #ffe6e6;
        color: #a33;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 18px;
    }

    @media (max-width: 500px) {
        .login-container {
            margin: 50px 20px;
            padding: 30px 20px;
        }
    }
</style>
</head>
<body>

<?php include('includes/navbar_public.php'); ?>

<div class="login-container">
    <h2>Welcome Back!</h2>

    <?php if (!empty($error)) echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Adopt Now</button>
    </form>

    <div class="bottom-link">
        Don’t have an account? <a href="register.php">Register here!</a>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
