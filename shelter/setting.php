<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$user_id = $_SESSION['user_id'];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];

    // Update password only if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username=?, first_name=?, last_name=?, email=?, phone_number=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $username, $first_name, $last_name, $email, $phone, $password, $user_id);
    } else {
        $sql = "UPDATE users SET username=?, first_name=?, last_name=?, email=?, phone_number=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $username, $first_name, $last_name, $email, $phone, $user_id);
    }

    if ($stmt->execute()) {
        $msg = "Profile updated successfully.";
    } else {
        $msg = "Update failed: " . $stmt->error;
    }
}

$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shelter Settings</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
</head>
<body>
<?php include('../includes/navbar_shelter.php'); ?>

<div class="page-wrapper">
    <h2>My Shelter Profile</h2>
    <?php if (!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

    <form method="post">
        <label>Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label>First Name:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

        <label>Last Name:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Password (leave blank to keep current):</label>
        <input type="password" name="password">

        <label>Phone Number:</label>
        <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

        <button type="submit">Update</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
