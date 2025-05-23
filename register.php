<?php
include('db_connection.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, first_name, last_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $first_name, $last_name, $email, $phone, $password, $role);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Account created. You can now log in.";
        header("Location: login.php");
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Animal Adoption System</title>
    <link rel="stylesheet" href="css/common.css">
    <style>
        .form-box {
            background-color: #f7e6cf;
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            border-radius: 25px;
            text-align: center;
        }

        .form-box h2 {
            margin-bottom: 25px;
            font-size: 24px;
        }

        .form-box input,
        .form-box select {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .form-box button {
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

        .form-box a {
            font-size: 14px;
            text-decoration: none;
        }

        .form-box a:hover {
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

<div class="form-box">
    <h2>Create Your Account</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
        <select name="role" required>
            <option value="">Register as...</option>
            <option value="adopter">Adopter</option>
            <option value="shelter">Shelter Personnel</option>
        </select>
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Sign Up</button>
    </form>

    <div style="margin-top: 10px;">
        <span>Already have an account? <a href="login.php">Log in here</a></span>
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
