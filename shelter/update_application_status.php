<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    echo "Missing parameters.";
    exit();
}

$app_id = intval($_GET['id']);
$status = $_GET['status'];

if (!in_array($status, ['approved', 'rejected'])) {
    echo "Invalid status.";
    exit();
}

// Step 1: Update the application status
$sql = "UPDATE adoption_applications SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $app_id);

if ($stmt->execute()) {
    // Step 2: If approved, mark the pet as adopted
    if ($status === 'approved') {
        // Get the pet_id related to this application
        $get_pet_sql = "SELECT pet_id FROM adoption_applications WHERE id = ?";
        $pet_stmt = $conn->prepare($get_pet_sql);
        $pet_stmt->bind_param("i", $app_id);
        $pet_stmt->execute();
        $pet_result = $pet_stmt->get_result();
        $pet_data = $pet_result->fetch_assoc();
        $pet_id = $pet_data['pet_id'];

        // Update pet status to 'adopted' and set adoption_date
        $update_pet_sql = "UPDATE pets SET status = 'adopted', adoption_date = NOW() WHERE id = ?";
        $update_pet_stmt = $conn->prepare($update_pet_sql);
        $update_pet_stmt->bind_param("i", $pet_id);
        $update_pet_stmt->execute();

        // Optional: Reject all other pending applications for this pet
        $reject_sql = "UPDATE adoption_applications SET status = 'rejected' WHERE pet_id = ? AND id != ? AND status = 'pending'";
        $reject_stmt = $conn->prepare($reject_sql);
        $reject_stmt->bind_param("ii", $pet_id, $app_id);
        $reject_stmt->execute();
    }

    header("Location: view_applications.php");
} else {
    echo "Failed to update status.";
}
?>
