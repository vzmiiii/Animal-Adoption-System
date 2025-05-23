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

$sql = "UPDATE adoption_applications SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $app_id);

if ($stmt->execute()) {
    header("Location: view_applications.php");
} else {
    echo "Failed to update status.";
}
?>
