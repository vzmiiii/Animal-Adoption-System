<?php
session_start();
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            if ($role == 'adopter') {
                header("Location: adopter/dashboard.php");
            } elseif ($role == 'shelter') {
                header("Location: shelter/dashboard.php");
            } elseif ($role == 'admin') {
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
    <link rel="stylesheet" href="css/common.css">
    <style>
        .login-container {
            background-color: #f7e6cf;
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

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Adopt Now</button>
    </form>

    <div style="margin-top: 10px;">
        <a href="#">Forgot Password?</a><br>
        <span>Don't have an account? <a href="register.php">Register here!</a></span>
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
