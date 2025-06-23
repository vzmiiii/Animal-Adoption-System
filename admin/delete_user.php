<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

// Check user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid user ID.";
    exit();
}

$user_id = intval($_GET['id']);

// Prevent admin from deleting themselves
if ($_SESSION['user_id'] == $user_id) {
    echo "You cannot delete your own account.";
    exit();
}

// Delete related notifications first
$stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: manage_users.php?deleted=1");
exit();
?>
