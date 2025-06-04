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
        .login-container {
            background-color: #f5f5f5;
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            border-radius: 25px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 25px;
            font-size: 24px;
        }

        .login-container input {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            font-size: 14px;
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
        }

        .login-container a {
            font-size: 14px;
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<?php include('includes/navbar_public.php'); ?>

<div class="login-container">
    <h2>Welcome Back!</h2>

    <!-- Display error if any -->
    <?php if (!empty($error)) echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>

    <!-- Login form -->
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Adopt Now</button>
    </form>

    <!-- Additional links -->
    <div style="margin-top: 10px;">
        <span>Don't have an account? <a href="register.php">Register here!</a></span>
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
