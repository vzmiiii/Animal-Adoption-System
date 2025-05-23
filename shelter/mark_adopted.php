<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

if (!isset($_GET['id'])) {
    echo "No pet ID provided.";
    exit();
}

$pet_id = intval($_GET['id']);
$shelter_id = $_SESSION['user_id'];

// Update pet status
$sql = "UPDATE pets SET status = 'adopted', adoption_date = CURDATE() WHERE id = ? AND shelter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $pet_id, $shelter_id);

if ($stmt->execute()) {
    header("Location: manage_pet_profiles.php");
    exit();
} else {
    echo "Failed to mark as adopted.";
}
?>
