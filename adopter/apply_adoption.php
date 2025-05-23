<?php
session_start();
include('../db_connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

$adopter_id = $_SESSION['user_id'];
$pet_id = intval($_POST['pet_id']);

// Check if already applied
$check_sql = "SELECT id FROM adoption_applications WHERE adopter_id = ? AND pet_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $adopter_id, $pet_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['message'] = "You have already applied for this pet.";
    header("Location: dashboard.php");
    exit();
}
$stmt->close();

// Submit application
$sql = "INSERT INTO adoption_applications (adopter_id, pet_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $adopter_id, $pet_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Application submitted successfully!";
} else {
    $_SESSION['message'] = "Error submitting application: " . $conn->error;
}

header("Location: dashboard.php");
exit();
?>
