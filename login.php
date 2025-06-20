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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/common.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    min-height: 100vh;
    font-family: 'Montserrat', 'Segoe UI', sans-serif;
    background: url('images/bg_animals.png') no-repeat center center fixed;
    background-size: cover;
    position: relative;
}
.login-container {
    background: #fff;
    max-width: 420px;
    margin: 100px auto;
    padding: 44px 36px 36px 36px;
    border-radius: 28px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(78,140,255,0.10);
    position: relative;
    z-index: 1;
}
.login-logo {
    margin-bottom: 18px;
}
.login-logo img {
    width: 54px;
    height: 54px;
    object-fit: contain;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    background: #f8f9fa;
}
.login-container h2 {
    margin-bottom: 10px;
    font-size: 26px;
    font-weight: 700;
    color: #2d3a4a;
}
.login-container .subtitle {
    font-size: 15px;
    color: #6a7a8a;
    margin-bottom: 28px;
}
.login-container input {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    margin-bottom: 18px;
    font-size: 15px;
    box-sizing: border-box;
    background: #f8f9fa;
    transition: border 0.2s;
}
.login-container input:focus {
    border: 1.5px solid #4e8cff;
    outline: none;
}
.login-container button {
    width: 100%;
    background: linear-gradient(90deg, #4e8cff 0%, #6ed6a5 100%);
    color: #fff;
    padding: 14px;
    border: none;
    border-radius: 20px;
    font-weight: 700;
    text-transform: uppercase;
    cursor: pointer;
    font-size: 15px;
    letter-spacing: 1px;
    box-shadow: 0 2px 8px rgba(78,140,255,0.10);
    transition: background 0.2s, transform 0.2s;
}
.login-container button:hover {
    background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
    transform: translateY(-2px) scale(1.03);
}
.login-container .bottom-link {
    margin-top: 16px;
    font-size: 14px;
    color: #4a5a6a;
}
.login-container .bottom-link a {
    color: #4e8cff;
    text-decoration: underline;
    font-weight: 500;
}
.error {
    background-color: #ffe6e6;
    color: #a33;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 18px;
}
@media (max-width: 600px) {
    .login-container {
        margin: 50px 10px;
        padding: 28px 8px 22px 8px;
    }
    .login-logo img {
        width: 44px;
        height: 44px;
    }
}
</style>
</head>
<body>
<?php include('includes/navbar_public.php'); ?>
<div class="login-container">
    <div class="body-overlay"></div>
    <div class="login-logo">
        <img src="images/pawprint.png" alt="Animal Adoption System Logo">
    </div>
    <h2>Welcome Back!</h2>
    <div class="subtitle">Log in to continue your adoption journey</div>
    <?php if (!empty($error)) echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Adopt Now</button>
    </form>
    <div class="bottom-link">
        Don't have an account? <a href="register.php">Register here!</a>
    </div>
</div>
<?php include('includes/footer.php'); ?>
</body>
</html>
