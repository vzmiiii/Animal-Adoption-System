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
    $password_raw = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    // Validate phone number format
    elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $error = "Phone number should be 10 to 15 digits.";
    }
    // Check if passwords match
    elseif ($password_raw !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check for duplicate email or username
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email or username already exists.";
        } else {
            // Hash password and insert new user
            $password = password_hash($password_raw, PASSWORD_DEFAULT);

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
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Animal Adoption System</title>
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
        .register-container {
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
        .register-logo {
            margin-bottom: 18px;
        }
        .register-logo img {
            width: 54px;
            height: 54px;
            object-fit: contain;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            background: #f8f9fa;
        }
        .register-container h2 {
            margin-bottom: 10px;
            font-size: 26px;
            font-weight: 700;
            color: #2d3a4a;
        }
        .register-container .subtitle {
            font-size: 15px;
            color: #6a7a8a;
            margin-bottom: 28px;
        }
        .register-container input,
        .register-container select {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            margin-bottom: 15px;
            font-size: 15px;
            box-sizing: border-box;
            background: #f8f9fa;
            transition: border 0.2s;
        }
        .register-container input:focus,
        .register-container select:focus {
            border: 1.5px solid #4e8cff;
            outline: none;
        }
        .register-container button {
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
        .register-container button:hover {
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .register-container .bottom-link {
            margin-top: 16px;
            font-size: 14px;
            color: #4a5a6a;
        }
        .register-container .bottom-link a {
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
            margin-bottom: 15px;
        }
        @media (max-width: 600px) {
            .register-container {
                margin: 50px 10px;
                padding: 28px 8px 22px 8px;
            }
            .register-logo img {
                width: 44px;
                height: 44px;
            }
        }
    </style>
</head>
<body>
<?php include('includes/navbar_public.php'); ?>
<div class="register-container">
    <div class="register-logo">
        <img src="images/pawprint.png" alt="Animal Adoption System Logo">
    </div>
    <h2>Create Your Account</h2>
    <div class="subtitle">Join us and start your adoption journey!</div>
    <?php if (!empty($error)) echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>
    <form method="post">
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="adopter" <?= (isset($role) && $role === 'adopter') ? 'selected' : '' ?>>Adopter</option>
            <option value="shelter" <?= (isset($role) && $role === 'shelter') ? 'selected' : '' ?>>Shelter</option>
        </select>
        <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>" required>
        <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($first_name ?? '') ?>" required>
        <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($last_name ?? '') ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>" required>
        <input type="text" name="phone" placeholder="Phone Number" 
            value="<?= htmlspecialchars($phone ?? '') ?>" 
            required maxlength="12" pattern="\d{8,12}" 
            title="Please enter a 8 to 12-digit phone number.">
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>
    <div class="bottom-link">
        Already have an account? <a href="login.php">Login here!</a>
    </div>
</div>
<?php include('includes/footer.php'); ?>
</body>
</html>
