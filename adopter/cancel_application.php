<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['application_id'])) {
    echo "Invalid request.";
    exit();
}

$app_id = intval($_POST['application_id']);
$adopter_id = $_SESSION['user_id'];

// Ensure the application belongs to this user and is still pending
$sql = "DELETE FROM adoption_applications 
        WHERE id = ? AND adopter_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $app_id, $adopter_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: adoption_tracker.php?cancel=success");
    exit();
} else {
    echo "Failed to cancel. It may have already been approved or rejected.";
}

